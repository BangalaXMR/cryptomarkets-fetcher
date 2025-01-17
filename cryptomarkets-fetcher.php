<?php

function fetch_haveno_data($market) {
    // Fetch data from Haveno exchange
    $url = "https://haveno.markets/api/v1/tickers";
    try {
        $response = file_get_contents($url);
        if ($response === FALSE) {
            echo "Error: Haveno API request failed.\n";
            return null;
        }
        // Decode the JSON response
        $data = json_decode($response, true);
        
        // Check if the requested market exists in the response
        if (isset($data[$market])) {
            $info = $data[$market];
            return [
                'price' => (float)($info['last_price'] ?? 0),
                'volume' => (float)($info['rel_vol'] ?? 0)
            ];
        } else {
            echo "Error: Market $market not found in Haveno data.\n";
            return null;
        }
    } catch (Exception $e) {
        echo "Error fetching Haveno data: " . $e->getMessage() . "\n";
        return null;
    }
}

function fetch_bisq_data($market) {
    // Base URL for Bisq API
    $bisq_base_url = "https://markets.bisq.services/api/ticker?market=";
    $url = $bisq_base_url . $market;
    try {
        // Make the API request
        $response = file_get_contents($url);
        if ($response === FALSE) {
            echo "Error: Bisq API request for $market failed.\n";
            return null;
        }
        // Decode the JSON response
        $data = json_decode($response, true);
        
        // Check if the request was successful
        if (isset($data['last'])) {
            // Extract price and volume
            return [
                'price' => (float)($data['last'] ?? 0), // Last price
                'volume' => (float)($data['volume_right'] ?? 0) // Volume in the right currency
            ];
        } else {
            echo "Error: Bisq API request for $market failed, no last price found.\n";
            return null;
        }
    } catch (Exception $e) {
        echo "Error fetching Bisq data for $market: " . $e->getMessage() . "\n";
        return null;
    }
}

function fetch_tradeogre_data($market) {
    // Base URL for TradeOgre API
    $tradeogre_base_url = "https://tradeogre.com/api/v1/ticker/";
    $url = $tradeogre_base_url . $market;
    try {
        // Make the API request
        $response = file_get_contents($url);
        if ($response === FALSE) {
            echo "Error: TradeOgre API request for $market failed.\n";
            return null;
        }
        // Decode the JSON response
        $data = json_decode($response, true);
        
        // Check if the request was successful
        if ($data['success'] ?? false) {
            // Extract price and volume
            return [
                'price' => (float)($data['price'] ?? 0),
                'volume' => (float)($data['volume'] ?? 0)
            ];
        } else {
            echo "Error: TradeOgre API request for $market failed, no success flag.\n";
            return null;
        }
    } catch (Exception $e) {
        echo "Error fetching TradeOgre data for $market: " . $e->getMessage() . "\n";
        return null;
    }
}

function fetch_nonkyc_data($market) {
    // Base URL for NonKYC API
    $nonkyc_base_url = "https://api.nonkyc.io/api/v2/market/getbysymbol/";
    $url = $nonkyc_base_url . $market;
    try {
        // Make the API request
        $response = file_get_contents($url);
        if ($response === FALSE) {
            echo "Error: NonKYC API request for $market failed.\n";
            return null;
        }
        // Decode the JSON response
        $data = json_decode($response, true);
        // Check if the response contains the expected data
        if (isset($data['_id'])) {
            // Extract price and volume
            return [
                'price' => (float)($data['lastPrice'] ?? 0),
                'volume' => (float)($data['volume'] ?? 0)
            ];
        } else {
            echo "Error: NonKYC API request for $market failed, unexpected response format.\n";
            return null;
        }
    } catch (Exception $e) {
        echo "Error fetching NonKYC data for $market: " . $e->getMessage() . "\n";
        return null;
    }
}

function fetch_kraken_data($market) {
    // Base URL for Kraken API
    $kraken_base_url = "https://api.kraken.com/0/public/Ticker?pair=" . $market;
    try {
        // Make the API request
        $response = file_get_contents($kraken_base_url);
        if ($response === FALSE) {
            echo "Error: Kraken API request failed.\n";
            return null;
        }
        // Decode the JSON response
        $data = json_decode($response, true);
        // Check if the response contains the expected data
        if (empty($data['error'])) {
            $result = $data['result'];
            $first_key = key($result); // Get the first key dynamically
            return [
                'price' => (float)($result[$first_key]['c'][0] ?? 0), // Last price
                'volume' => (float)($result[$first_key]['v'][0] ?? 0), // Volume
            ];
        } else {
            echo "Error: Kraken API request failed, error: " . implode(", ", $data['error']) . "\n";
            return null;
        }
    } catch (Exception $e) {
        echo "Error fetching Kraken data: " . $e->getMessage() . "\n";
        return null;
    }
}

function fetch_mexc_data($market) {
    // Base URL for mexc API
    $mexc_base_url = "https://api.mexc.com/api/v3/ticker/24hr?symbol=" . $market;
    try {
        // Make the API request
        $response = file_get_contents($mexc_base_url);
        if ($response === FALSE) {
            echo "Error: mexc API request failed.\n";
            return null;
        }
        // Decode the JSON response
        $data = json_decode($response, true);
        // Check if the response contains the expected data
        if (empty($data['error'])) {
            $result = $data['symbol'];
            // Extract price and volume
            return [
                'price' => (float)($data['lastPrice'] ?? 0),
                'volume' => (float)($data['volume'] ?? 0)
            ];
        } else {
            echo "Error: mexc API request failed, error: " . implode(", ", $data['error']) . "\n";
            return null;
        }
    } catch (Exception $e) {
        echo "Error fetching mexc data: " . $e->getMessage() . "\n";
        return null;
    }
}

function fetch_coinex_data($market) {
    // Base URL for CoinEx API
    $coinex_base_url = "https://api.coinex.com/v2/spot/ticker?market=" . $market;
    try {
        // Make the API request
        $response = file_get_contents($coinex_base_url);
        if ($response === FALSE) {
            echo "Error: CoinEx API request failed.\n";
            return null;
        }
        // Decode the JSON response
        $data = json_decode($response, true);
        // Check if the response contains the expected data
        if ($data['code'] === 0 && !empty($data['data'])) {
            $result = $data['data'][0]; // Access the first element of the data array
            // Extract price and volume
            return [
                'price' => (float)($result['last'] ?? 0),
                'volume' => (float)($result['volume'] ?? 0)
            ];
        } else {
            echo "Error: CoinEx API request failed, error: " . ($data['message'] ?? 'Unknown error') . "\n";
            return null;
        }
    } catch (Exception $e) {
        echo "Error fetching CoinEx data: " . $e->getMessage() . "\n";
        return null;
    }
}

function fetch_kucoin_data($market) {
    // Base URL for KuCoin API
    $kucoin_base_url = "https://api.kucoin.com/api/v1/market/stats?symbol=" . $market;
    try {
        // Make the API request
        $response = file_get_contents($kucoin_base_url);
        if ($response === FALSE) {
            echo "Error: KuCoin API request failed.\n";
            return null;
        }
        // Decode the JSON response as an object
        $data = json_decode($response);
        // Check if the response contains the expected data
        if ($data->code === '200000' && !empty($data->data)) {
            $result = $data->data; // Access the data object directly
            // Extract price and volume
            return [
                'price' => (float)($result->last ?? 0), // Last price
                'volume' => (float)($result->vol ?? 0)  // Volume
            ];
        } else {
            echo "Error: KuCoin API request failed, error: " . ($data->message ?? 'Unknown error') . "\n";
            return null;
        }
    } catch (Exception $e) {
        echo "Error fetching KuCoin data: " . $e->getMessage() . "\n";
        return null;
    }
}

function fetch_bitmart_data($market) {
    // Base URL for bitmart API
    $bitmart_base_url = "https://api-cloud.bitmart.com/spot/quotation/v3/ticker?symbol=" . $market;
    try {
        // Make the API request
        $response = file_get_contents($bitmart_base_url);
        if ($response === FALSE) {
            echo "Error: bitmart API request failed.\n";
            return null;
        }
        // Decode the JSON response as an object
        $data = json_decode($response);
        // Check if the response contains the expected data
        if ($data->code === 1000 && !empty($data->data)) {
            $result = $data->data; // Access the data object directly
            // Extract price and volume
            return [
                'price' => (float)($result->last ?? 0), // Last price
                'volume' => (float)($result->qv_24h ?? 0)  // Volume
            ];
        } else {
            echo "Error: bitmart API request failed, error: " . ($data->message ?? 'Unknown error') . "\n";
            return null;
        }
    } catch (Exception $e) {
        echo "Error fetching bitmart data: " . $e->getMessage() . "\n";
        return null;
    }
}
