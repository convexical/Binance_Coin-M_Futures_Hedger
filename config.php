<?
	
// put in your API KEYS from Binance here
$_GLOBALS['api_key'] = "<<YOUR BINANCE API KEY HERE>>";
$_GLOBALS['secret_key'] = "<<YOUR BINANCE SECRET API KEY HERE>>";

// this variable controls how close the hedge should be to the actual position / when to stop trading / creating new buy / sell orders
// once the delta in btc (between the current open position (hedge) / and the wallet balance of Coin-M futures) is near this value, stop trading
$_GLOBALS['delta_amount_in_btc_to_stop_trading'] = "0.01"; // in BTC terms

// These variables control the amounts / sizes when new orders are placed.  These are ranges - so new orders will randomized be between two values.
$_GLOBALS['per_order_min'] = 0.01;
$_GLOBALS['per_order_max'] = 0.02;

// Symbol to trade - note, this code is designed for BTCUSD_PERP under COIN-M futures, if you try to use it for other symbols, it may break because of decimal order sizes or other issues - if you're interested in other symbols or features, please reach out.
$symbol = "BTCUSD_PERP";

// initialize / connect to the Binance API with the specified keys
$api = new Binance\API(
	$_GLOBALS['api_key'],
	$_GLOBALS['secret_key']
);	

	
?>