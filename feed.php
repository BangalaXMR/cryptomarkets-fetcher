<?php

require 'markets.php'; // Adjust the path as necessary

// Define the markets you want to fetch
$markets = [
    'haveno' => 'BTC',
    'bisq' => 'XMR_BTC',
    'tradeogre' => 'XMR-BTC',
    'nonkyc' => 'XMR_BTC',
    'kraken' => 'XMRXBT',
    'mexc' => 'XMRUSDT',
    'coinex' => 'XMRBTC',
    'kucoin' => 'XMR-BTC',
    'bitmart' => 'XMR_BTC'
];

// Function to fetch and display market data
function fetch_and_display_market_data($markets) {
    foreach ($markets as $exchange => $market) {
        switch ($exchange) {
            case 'haveno':
                $data = fetch_haveno_data($market);
                break;
            case 'bisq':
                $data = fetch_bisq_data($market);
                break;
            case 'tradeogre':
                $data = fetch_tradeogre_data($market);
                break;
            case 'nonkyc':
                $data = fetch_nonkyc_data($market);
                break;
            case 'kraken':
                $data = fetch_kraken_data($market);
                break;
            case 'mexc':
                $data = fetch_mexc_data($market);
                break;
            case 'coinex':
                $data = fetch_coinex_data($market);
                break;
            case 'kucoin':
                $data = fetch_kucoin_data($market);
                break;
            case 'bitmart':
                $data = fetch_bitmart_data($market);
                break;
            default:
                echo "Unknown exchange: $exchange\n";
                continue 2; // Skip to the next market
        }
    }
}

function run_price_feed() {
    global $markets; // Access the global markets array
    while (true) {
        fetch_and_display_market_data($markets); // Pass the markets array
        sleep(5); // Adjust the interval as needed
    }
}
