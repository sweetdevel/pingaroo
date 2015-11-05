<?php

namespace Pingaroo\Classes;

use GuzzleHttp\Client;

/**
 * Dependencies:
 * Guzzle ^6.1
 */
class TinyCURL {
    private $url;
    private $statusCode;
    private $headers;
    private $body;
    private $exception;
    private $options;
    
    /**
     * Performs curl using Guzzle Client.
     * 
     * @param string $method
     * @param string $url
     * @param array $data
     * @param string $proxy
     * @param array $headers
     * @param array $auth
     * @return boolean
     */
    public function exec($method, $url, $data, $proxy = '', $headers = [], $auth = []) {
        $this->resetAttributes();
        
        try {
            $method = strtolower($method);
            
            $this->options = [
                'headers' => ['User-Agent' => ''] + $headers
                , 'query'   => $data
                , 'proxy'   => $proxy
                , 'timeout' => 20
            ];

            if(!empty($auth)){
                $this->options = array_merge($this->options, ['auth' => [$auth[0], $auth[1]]]); 
            }        
           
            $client = new Client();
            $res = $client->request($method, $url, $this->options);
            
            $this->url = $url;
            $this->statusCode = $res->getStatusCode();
            $this->headers = $res->getHeaders();
            $this->body = $res->getBody();
            
            return true;
        } catch (\Exception $exception) {
            $this->resetAttributes();
            $this->exception = $exception->getMessage();
            return false;
        }
        
    }
    
    /**
     * Resets the private attributes
     */
    private function resetAttributes() {
        $this->url = null;
        $this->statusCode = null;
        $this->header = null;
        $this->body = null;
        $this->exception = null;
        $this->options = null;
    }
    
    public function getUrl() {
        return $this->url;
    }
    
    public function getStatusCode() {
        return $this->statusCode;
    }
    
    public function getHeaders($arKeys = array()) {
        if(empty($arKeys)) {
            return $this->headers;
        } else {
            $arHeaders = array();
            
            foreach($arKeys as $key) {
                $arHeaders[$key] = isset($this->headers[$key])
                                    ? $this->headers[$key] : null; 
            }
            
            return $arHeaders;
        }

    }
    
    public function getBody() {
        return $this->body;
    }
    
    public function getException() {
        return $this->exception;
    }
    
    public function getOptions() {
        return $this->options;
    }
}