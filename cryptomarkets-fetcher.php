<?php

function fetch_data($urls, $callbacks, $options = []) {
    $multi_handle = curl_multi_init();
    $handles = [];
    $results = [];

    // Default options
    $default_options = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 5,
    ];

    foreach ($urls as $key => $url) {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException("Invalid URL: $url");
        }

        $handles[$key] = curl_init($url);
        curl_setopt_array($handles[$key], $default_options);

        // Apply custom options if provided
        if (isset($options[$key])) {
            curl_setopt_array($handles[$key], $options[$key]);
        }

        curl_multi_add_handle($multi_handle, $handles[$key]);
    }

    $running = null;
    do {
        while (($status = curl_multi_exec($multi_handle, $running)) == CURLM_CALL_MULTI_PERFORM);
        if ($status != CURLM_OK) {
            break;
        }

        // Wait for activity on any of the cURL handles
        if ($running && curl_multi_select($multi_handle) != -1) {
            do {
                $info = curl_multi_info_read($multi_handle);
                if ($info === false) {
                    break;
                }
                $handle = $info['handle'];
                $key = array_search($handle, $handles, true);
                if ($key !== false) {
                    $response = curl_multi_getcontent($handle);
                    $results[$key] = call_user_func($callbacks[$key], $response);
                    curl_multi_remove_handle($multi_handle, $handle);
                    curl_close($handle);
                    unset($handles[$key]);
                }
            } while (true);
        }
    } while ($running);

    // Close any remaining handles
    foreach ($handles as $key => $handle) {
        curl_multi_remove_handle($multi_handle, $handle);
        curl_close($handle);
    }

    curl_multi_close($multi_handle);
    return $results;
}

function process_haveno_data($ticker) {
    $url = "https://haveno.markets/api/v1/tickers";
    $response = file_get_contents($url);
    $data = json_decode($response, true);
    if (isset($data[$ticker])) {
        $info = $data[$ticker];
        return [
            'ticker' => $ticker,
            'price' => (float)($info['last_price'] ?? 0),
            'volume' => (float)($info['rel_vol'] ?? 0)
        ];
    } else {
        echo "Error: Market $ticker not found in Haveno data.\n";
        return null;
    }
}

function process_bisq_data($ticker) {
    $url = "https://markets.bisq.services/api/ticker?market=$ticker";
    $response = file_get_contents($url);
    $data = json_decode($response, true);
    if (isset($data['last'])) {
        return [
            'ticker' => $ticker,
            'price' => (float)($data['last'] ?? 0),
            'volume' => (float)($data['volume_right'] ?? 0)
        ];
    } else {
        echo "Error: Bisq API request failed, no last price found.\n";
        return null;
    }
}

function process_tradeogre_data($ticker) {
    $url = "https://tradeogre.com/api/v1/ticker/$ticker";
    $response = file_get_contents($url);
    $data = json_decode($response, true);
    if ($data['success'] ?? false) {
        return [
            'ticker' => $ticker,
            'price' => (float)($data['price'] ?? 0),
            'volume' => (float)($data['volume'] ?? 0)
        ];
    } else {
        echo "Error: TradeOgre API request failed, no success flag.\n";
        return null;
    }
}

function process_nonkyc_data($ticker) {
    $url = "https://api.nonkyc.io/api/v2/market/getbysymbol/$ticker";
    $response = file_get_contents($url);
    $data = json_decode($response, true);
    if (isset($data['_id'])) {
        return [
            'ticker' => $ticker,
            'price' => (float)($data['lastPrice'] ?? 0),
            'volume' => (float)($data['volume'] ?? 0)
        ];
    } else {
        echo "Error: NonKYC API request failed, unexpected response format.\n";
        return null;
    }
}

function process_kraken_data($ticker) {
    $url = "https://api.kraken.com/0/public/Ticker?pair=$ticker";
    $response = file_get_contents($url);
    $data = json_decode($response, true);
    if (empty($data['error'])) {
        $result = $data['result'];
        $first_key = key($result);
        return [
            'ticker' => $ticker,
            'price' => (float)($result[$first_key]['c'][0] ?? 0),
            'volume' => (float)($result[$first_key]['v'][0] ?? 0),
        ];
    } else {
        echo "Error: Kraken API request failed, error: " . implode(", ", $data['error']) . "\n";
        return null;
    }
}

function process_mexc_data($ticker) {
    $url = "https://api.mexc.com/api/v3/ticker/24hr?symbol=$ticker";
    $response = file_get_contents($url);
    $data = json_decode($response, true);
    if (empty($data['error'])) {
        return [
            'ticker' => $ticker,
            'price' => (float)($data['lastPrice'] ?? 0),
            'volume' => (float)($data['volume'] ?? 0)
        ];
    } else {
        echo "Error: mexc API request failed, error: " . implode(", ", $data['error']) . "\n";
        return null;
    }
}

function process_coinex_data($ticker) {
    $url = "https://api.coinex.com/v2/spot/ticker?market=$ticker";
    $response = file_get_contents($url);
    $data = json_decode($response, true);
    if ($data['code'] === 0 && !empty($data['data'])) {
        $result = $data['data'][0];
        return [
            'ticker' => $ticker,
            'price' => (float)($result['last'] ?? 0),
            'volume' => (float)($result['volume'] ?? 0)
        ];
    } else {
        echo "Error: CoinEx API request failed, error: " . ($data['message'] ?? 'Unknown error') . "\n";
        return null;
    }
}

function process_kucoin_data($ticker) {
    $url = "https://api.kucoin.com/api/v1/market/stats?symbol=$ticker";
    $response = file_get_contents($url);
    $data = json_decode($response);
    if ($data->code === '200000' && !empty($data->data)) {
        $result = $data->data;
        return [
            'ticker' => $ticker,
            'price' => (float)($result->last ?? 0),
            'volume' => (float)($result->vol ?? 0)
        ];
    } else {
        echo "Error: KuCoin API request failed, error: " . ($data->message ?? 'Unknown error') . "\n";
        return null;
    }
}

function process_bitmart_data($ticker) {
    $url = "https://api-cloud.bitmart.com/spot/quotation/v3/ticker?symbol=$ticker";
    $response = file_get_contents($url);
    $data = json_decode($response);
    if ($data->code === 1000 && !empty($data->data)) {
        $result = $data->data;
        return [
            'ticker' => $ticker,
            'price' => (float)($result->last ?? 0),
            'volume' => (float)($result->qv_24h ?? 0)
        ];
    } else {
        echo "Error: bitmart API request failed, error: " . ($data->message ?? 'Unknown error') . "\n";
        return null;
    }
}
?>
