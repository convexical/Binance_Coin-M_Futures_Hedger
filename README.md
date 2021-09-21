<pre>

 _     _                                        _                _              _              _           _   
| |__ (_)_ __   __ _ _ __   ___ ___    ___ ___ (_)_ __   /\/\   | |__   ___  __| | __ _  ___  | |__   ___ | |_ 
| '_ \| | '_ \ / _` | '_ \ / __/ _ \  / __/ _ \| | '_ \ /    \  | '_ \ / _ \/ _` |/ _` |/ _ \ | '_ \ / _ \| __|
| |_) | | | | | (_| | | | | (_|  __/ | (_| (_) | | | | / /\/\ \ | | | |  __/ (_| | (_| |  __/ | |_) | (_) | |_ 
|_.__/|_|_| |_|\__,_|_| |_|\___\___|  \___\___/|_|_| |_\/    \/ |_| |_|\___|\__,_|\__, |\___| |_.__/ \___/ \__|
                                                                                  |___/                        

</pre>

Developed by [![Twitter URL](https://img.shields.io/twitter/url/https/twitter.com/mahaviracrypto.svg?style=social&label=MahaviraCrypto)](https://twitter.com/mahaviracrypto) - https://twitter.com/MahaviraCrypto

`Binance Futures Coin-M Hedger`                                                        

## What is this project?
This project contains code that automates / places various trades (long [buy] & short [sell]) to create a hedge (synthetic USD) of your COIN-M Futures Wallet on Binance.  For example, if you deposit 1 BTC on Binance under your spot wallet and transfer it into your Binance COIN-M futures account, it will automatically place trades until you are hedged (aka it will create short limit orders).  You can also set a min/max amount per order that the bot will randomize with to get your current open position to be hedged to your Coin-M Futures wallet balance.  If you choose to run the code with a cronjob or scheduler, it will continuously monitor your wallet balance and appropriately buy or sell to get the current open position to match your wallet balance.

## Why would I use this?
Creating a hedge for your BTC can be important in certain situations.  If you want more info on why you'd do this, please look at the resources section below.  This code allows you to hedge off your BTC using (1) limit orders which save you money [because you're not submiting market orders which have higher fees than limit orders], (2) manage your hedge by consistently checking your Coin-M  wallet balance and buying or selling more of the derivative to match your futures wallet.  The reason I created this script is that in certain situations, you want to hedge your position and protect the FIAT value.  If your wallet balance is a lower amount, it's likely easy to create a hedged position, but when you have larger BTC amounts the ability to hedge your position can be difficult because creating large market orders to hedge your wallet balance would (1) result in higher fees (which is money you're just giving to the exchange willingly) and (2) bad pricing of the hedge since it's possible you'd affect the market.

## How do I use this and run it?
This code is written in PHP.  I am a versatile developer / engineer but I still love PHP. While I could code it in Python, C, C++, etc. - PHP is something that I can move faster in and for me it just works. If you're a coder and want to port it, I'd love to work with you.

In order to get this code to work correctly, you have to do the following:

* Setup a Linux VPS or have a Linux server available - the below assumes a new version of Linux
* Install PHP 7 - in the terminal: `apt install php`
* Install PHP Curl - in the terminal: `apt install php-curl`

1. have an understanding of how to use a linux server and have PHP 7.0+ installed with the required modules of curl.
2. create an API key on Binance with trade permissions for Futures
3. update the config.php file with your API keys and set the various variables to your own preferences - order sizes for buy / sell side orders and the BTC delta amount distance when you want the bot to stop executing
4. setup a cronjob that executes the main.php at your specified interval or run `php main.php` to test and review the output
5. enjoy the magic / simplicitiy of the program running to consistently keep your hedge optimized correctly

## Example Cronjob
This executes every minute.  Please change to your desired time interval.

`* * * * * /usr/bin/php /<DIRECTORY_WHERE_SCRIPT_IS_LOCATED>/main.php > /dev/null 2>&1`

## Can you explain in detail what this code does 
At a high level - this code creates synthetic USD or hedges your BTC Coin-M futures wallet.  When the code is executed (main.php) it connects to your Binance wallet and gets the Coin-M wallet balance and open positions.  Once it has that information, it checks what the delta / difference is between the two.  The script then does the following:

1) The script then checks if there are existing orders open - if there's an order open that is at the top of the book (either the bid or ask side depending on the delta of the current position against the wallet size), then it will stop / exit.  If the current open limit order isn't at the top of the order book, it will cancel it and place a new limit order to hopefully get filled.
2) If the current open position is less than the wallet balance, it will create orders in the order book (short / sell) until the position is hedged / matches closely with the wallet balance
3) If the current open position is greater than the wallet balance, (because you moved coins out of the Coin-M futures wallet), then the bot will create orders (buy / long) in the order book to get close to the wallet balance.

It's important to note that in order to create a hedge - you are creating a position that is opposite your wallet balance - so if you have 1 BTC in your Binance.com Futures Coin-M wallet, this code will create / place trades until the open position is close to -1 BTC.

## Are there any online resources where I can learn more about Synthetic USD / hedging / delta neutral trading?
* https://medium.com/@zoomerjd/how-to-bitmex-synthetic-usd-cb6c89990a7a
* https://blog.bitmex.com/in-depth-creating-synthetic-usd/
* https://www.reddit.com/r/BitMEX/comments/eqed2d/guide_how_to_keep_your_balance_in_synthetic_usd/
* https://www.binance.com/en/blog/421499824684900458/exploring-marketneutral-strategies-in-cryptoderivatives-

## Does this work for other coins / tokens under Binance?
As of the release of this code, I have not tested it fully on other coins / tokens.  However, it wouldn't be that difficult to modify this code for other coins / tokens.  If you're interested in getting this code to work with other coins / tokens, please reach out to me.

## How do I get support for this program?
If you're looking for support on how to set this up or you just want to reach out, please contact me on Twitter - https://twitter.com/MahaviraCrypto.

## Important notes
This code utilizes the JaggedSoft PHP Binance API library (https://github.com/jaggedsoft/php-binance-api).  I've added various pieces of code to bring that library up to date to interact with the Binance Coin-M futures.

## Disclaimer
Please use this code at your own risk.  If you modify certain variables to be beyond certain ranges, you may end up with bad results.  Always test first and optimize after.

<pre>
               .__                .__                                                 __          
  _____ _____  |  |__ _____ ___  _|__|___________          ___________ ___.__._______/  |_  ____  
 /     \\__  \ |  |  \\__  \\  \/ /  \_  __ \__  \       _/ ___\_  __ <   |  |\____ \   __\/  _ \ 
|  Y Y  \/ __ \|   Y  \/ __ \\   /|  ||  | \// __ \_     \  \___|  | \/\___  ||  |_> >  | (  <_> )
|__|_|  (____  /___|  (____  /\_/ |__||__|  (____  /      \___  >__|   / ____||   __/|__|  \____/ 
      \/     \/     \/     \/                    \/           \/       \/     |__|                

</pre>