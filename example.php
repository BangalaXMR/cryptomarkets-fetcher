<?php
include "cryptomarkets-fetcher.php";

// Example usage
$results = [
    'bisq' => process_bisq_data('xmr_btc'),
    'tradeogre' => process_tradeogre_data('XMR-BTC'),
    'haveno' => process_haveno_data('USD'),
    'nonkyc' => process_nonkyc_data('XMR_USDT'),
    'kraken' => process_kraken_data('xmrusd'),
    'mexc' => process_mexc_data('XMRUSDT'),
    'coinex' => process_coinex_data('XMRUSDT'),
    'kucoin' => process_kucoin_data('XMR-USDT'),
    'bitmart' => process_bitmart_data('XMR_USDT')
];

// Print the results
foreach ($results as $key => $result) {
    if ($result) {
        echo "Data from $key for " . $result['ticker'] . ":\n";
        echo "Price: " . $result['price'] . "\n";
        echo "Volume: " . $result['volume'] . "\n";
    }
}
