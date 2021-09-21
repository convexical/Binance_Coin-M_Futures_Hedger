<?

/*
	
This script controls the main logic of mananging the hedge / position.
	
*/

require __DIR__."/php-binance-api.php";
include(__DIR__."/config.php");
include(__DIR__."/functions.php");

$delivery_account_informations = $api->delivery_account_information();

foreach($delivery_account_informations['assets'] as $asset) {
	if ($asset['asset'] == 'BTC') {
		$user['futures_wallet_amount_BTC'] = $asset;
//		print_r($asset);
		break 1;
	}
}

foreach($delivery_account_informations['positions'] as $position) {	
	if ($position['symbol'] == 'BTCUSD_PERP') {
		$user['futures_current_position'] = $position;
// 		print_r($position);
		break 1;
	}
}

// print_r($user);

// calculate difference / delta in the wallet vs. position
$delta_wallet_position_amt = $user['futures_wallet_amount_BTC']['walletBalance'] - (abs($user['futures_current_position']['notionalValue']) - abs($user['futures_wallet_amount_BTC']['unrealizedProfit']));
$abs_delta_wallet_position_amt = abs($delta_wallet_position_amt);

print "Delta btwn Wallet / Position Amt = {$delta_wallet_position_amt}\n";

// which one is larger?  wallet or notionalValue (-profLoss)
if ($abs_delta_wallet_position_amt < $_GLOBALS['delta_amount_in_btc_to_stop_trading']) {
	die("WalletBalance / OpenPosition are very close to : {$abs_delta_wallet_position_amt} BTC - stopping trading\n");	
} else if ($user['futures_wallet_amount_BTC']['walletBalance'] > ((abs($user['futures_current_position']['notionalValue']) - abs($user['futures_wallet_amount_BTC']['unrealizedProfit'])))) {
	print "WalletBalance is larger by {$abs_delta_wallet_position_amt} BTC so I have to short more.\n";
	$direction_to_go = "SELL";
} else {
	print "NotionalValue +/- ProfLoss is Larger by {$abs_delta_wallet_position_amt} BTC so I have to buy / cover.\n";
	$direction_to_go = "BUY";
}

$instrument_name = $symbol;

$ticker = $api->delivery_symbol_order_book_ticker($symbol);
$ticker = $ticker[0];

/*
print_r($ticker);
die();
*/

// -- set the max / min values based on the delta btc amt
$max_trade_size = $_GLOBALS['per_order_max'];
$min_trade_size = $_GLOBALS['per_order_min'];

$amount_in_btc = mt_rand($min_trade_size*100, $max_trade_size*100)/100; // number of COIN for buy / sell

// if the open position is close to the wallet balance, the next order amount submitted doesn't cause the open position to go beyond the delta - aka it adjusts the new orders to help get close to the wallet balance and create a perfect hedge and eventually stop the script from submitting new orders
if ($amount_in_btc > $abs_delta_wallet_position_amt) {
	$min_trade_size = 0.01;
	$max_trade_size = $abs_delta_wallet_position_amt;
	$amount_in_btc = mt_rand($min_trade_size*100, $max_trade_size*100)/100; // number of COIN for buy / sell
}

$amount_in_USD = $amount_in_btc*$ticker['bidPrice'];
$amount = round(($amount_in_btc*$ticker['bidPrice'])/100);

// convert the $amount into BTC amount
print <<<EOD
max_trade_size: {$max_trade_size}
min_trade_size: {$min_trade_size}
Amount in BTC: {$amount_in_btc}
Amount in USD: {$amount_in_USD}
Ticker: {$ticker['bidPrice']}
Amount in Contracts: {$amount}
Direction to go: {$direction_to_go}

EOD;

// die();


$cancel = false;
$current_openOrder_price = 0;
		
$openPositions = $api->delivery_position_information();


$i=0;
foreach($openPositions as $openPosition) {
	if ($openPosition['symbol'] != $symbol) {
		unset($openPositions[$i]);
	}
	$i++;
}

$openPositions = array_values($openPositions);

$temp_openOrders = $api->delivery_current_all_open_orders($symbol);

$openOrders = array();
foreach($temp_openOrders as $openOrder) {
	if ($openOrder['type'] == "LIMIT") {
		$openOrders[] = $openOrder;
	}
}

if (sizeof($openOrders) > 1) {
	$orderC = $api->delivery_cancel_all_open_orders($symbol);
	$cancel = true;
} else if (sizeof($openOrders) == 1) {
	$current_openOrder_price = sprintf('%.8f', $openOrders[0]['price']);
	if ($direction_to_go == "BUY" && $current_openOrder_price == $ticker['bidPrice']) {
		$cancel = false;
	} else if ($direction_to_go == "SELL" && $current_openOrder_price == $ticker['askPrice']) {
		$cancel = false;		
	} else {
		$orderC = $api->delivery_cancel_all_open_orders($symbol);
		$cancel = true;
	}
}

$rand_string = random_strings(17);
$newClientOrderId = "HEDGER-MC-{$rand_string}";
// string | user defined label for the order (maximum 32 characters)
	
if ($direction_to_go == "BUY") {
	if ($cancel == true || ($current_openOrder_price != $ticker['bidPrice'])) {
		print "bid: {$ticker['bidPrice']}\n";
		print "Amount: {$amount}\n";
		
		$price = $ticker['bidPrice'];
		while (1 == 1) {
			$order = $api->delivery_create_order($side = "BUY", $symbol, $amount, $price,  $type = "LIMIT", array('newClientOrderId' => $newClientOrderId), false);

			print_r($order);
// 				die();
								
			if ($order['status'] != "NEW") {
				$price -= 0.50;

				$order = $api->delivery_create_order($side = "BUY", $symbol, $amount, $price,  $type = "LIMIT", array('newClientOrderId' => $newClientOrderId), false);
				print_r($order);
				
				$price -= 0.50;
			} else {
				break 1;
			}
			die();				
		}
	} else {
		print "Current Order Price = Current bid Price\n";
	}
} else if ($direction_to_go == "SELL") {
	if ($cancel == true || ($current_openOrder_price != $ticker['askPrice'])) {
		print "ask: {$ticker['askPrice']}\n";
		print "Amount: {$amount}\n";
		
		$price = $ticker['askPrice'];
		while(1 == 1) {								
			$order = $api->delivery_create_order($side = "SELL", $symbol, $amount, $price,  $type = "LIMIT", array('newClientOrderId' => $newClientOrderId), false);

			print_r($order);
			if ($order['status'] != "NEW") {
				$price += 0.50;

				$order = $api->delivery_create_order($side = "SELL", $symbol, $amount, $price,  $type = "LIMIT", array('newClientOrderId' => $newClientOrderId), false);
				print_r($order);
				
				$price += 0.50;
			} else {
				break 1;
			}
			die();

		}

	} else {
		print "Current Order Price = Current ask Price\n";
	}
}
	
?>