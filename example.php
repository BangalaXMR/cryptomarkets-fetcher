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
