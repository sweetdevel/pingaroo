<?php

namespace Pingaroo\Utilities;

use Pingaroo\Classes\TinyCURL;

class NetUtility {
    /**
     * Performs a standard CURL.
     * This is a wrapper method for curl_exec.
     * 
     * @param string $url
     * @param array $options
     * @return array
     * 
     * @throws \Exception
     */
    public static function curl($url, $options = []) {

        $options = array_merge(
            array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => $url
            ),
            $options
        );

        $curl = curl_init();
        curl_setopt_array($curl, $options);

        $response = curl_exec($curl);
        $info = curl_getinfo($curl);

        $arResult = array(
            'response' => $response,
            'httpCode' => $info['http_code'],
            'error' => curl_error($curl),
            'info' => $info
        );
        
        curl_close($curl);
        
        return $arResult;
    }
    
    public static function ping($url, $proxyUrl) {
        $start = microtime(true);
        
        try {
            $tinyCURL = new TinyCURL;
            
            # THE ACTUAL CURL
            $httpCode = ($tinyCURL->exec('get', $url, 'id=1', $proxyUrl))
                            ? $tinyCURL->getStatusCode() : 0;
            
            $stop = microtime(true);

            return array(
                'httpCode' => $httpCode,
                'duration' => ($stop - $start),
                'error' => $tinyCURL->getException()
            );
        } catch (\Exception $ex) {
            return array(
                'httpCode' => 0,
                'duration' => (microtime(true) - $start),
                'error' => $ex->getMessage()
            );
        }
    }
}