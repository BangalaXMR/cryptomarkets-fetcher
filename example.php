<?php
include "markets.php";

$bisqXMRBTC = fetch_bisq_data("XMR_BTC");
echo "Price of XMR on bisq: " . $bisqXMRBTC['price'] . " BTC\n";

$havenoBTCXMR = fetch_haveno_data("BTC");
echo "Price of 1 BTC in XMR on haveno: " . $havenoBTCXMR['price'] . " BTC\n";

$tradeogreXMRBTC = fetch_tradeogre_data("XMR-BTC");
echo "Price of XMR on tradeogre: " . $tradeogreXMRBTC['price'] . " BTC\n";

$nonkycXMRBTC = fetch_nonkyc_data("XMR_BTC");
echo "Price of XMR on nonkyc: " . $nonkycXMRBTC['price'] . " BTC\n";

$krakenXMRBTC = fetch_kraken_data("XMRXBT");
echo "Price of XMR on kraken: " . $krakenXMRBTC['price'] . " BTC\n";

$mexcXMRUSDT = fetch_mexc_data("XMRUSDT");
echo "Price of XMR on mexc: " . $mexcXMRUSDT['price'] . " USDT\n";

$coinexXMRUSDT = fetch_coinex_data("XMRUSDT");
echo "Price of XMR on coinex: " . $coinexXMRUSDT['price'] . " USDT\n";

$kucoinXMRUSDT = fetch_kucoin_data("XMR-USDT");
echo "Price of XMR on kucoin: " . $kucoinXMRUSDT['price'] . " USDT\n";

$bitmartXMRBTC = fetch_bitmart_data("XMR_BTC");
echo "Price of XMR on bitmart: " . $bitmartXMRBTC['price'] . " BTC\n";
