<?php

namespace Pingaroo\Helpers;

use Pingaroo\Utilities\SystemUtility;
use Pingaroo\Utilities\NetUtility;

/**
 * Description of PingManager
 *
 * @author Marius
 */
class PingHelper {
    const HTTP_CODE_IN_PROGRESS = 1;
    
    public static function create($batchId, $urlId, $proxyId, $updateBatch) {
        SystemUtility::log('PING START', true);        
        SystemUtility::log('GATHERING DATA');        
        
        # Update batch here in case something crashes below
        if($updateBatch) {
            SystemUtility::log('THIS IS THE LAST PING IN BATCH');
            $batch = \Batch::findFirst();
            $batch->updatedAt = SystemUtility::getSqlNowDate();
            $batch->save();
        }
        
        $url = \Url::findFirst($urlId);
        $proxy = \Proxy::findFirst($proxyId);
        
        # Create new ping
        $ping = new \Ping;
        $ping->batchId = $batchId;
        $ping->proxyId = $proxyId;
        $ping->httpCode = self::HTTP_CODE_IN_PROGRESS;
        $ping->duration = 0;
        $ping->error = '';
        $ping->save();
          
        self::doPing($ping, $url, $proxy);
        
        SystemUtility::log('PING END');
        
        return $ping;
    }
    
    public static function reload($id, $silent = true) {
        SystemUtility::log('PING RELOAD START', true);        
        SystemUtility::log('GATHERING DATA');
        
        if(!is_numeric($id)) {
            if(!$silent) {
               echo 'Identifier not numeric.';                
            }
            
            $result = false;
        } else {
            $ping = \Ping::findFirst($id);
            if(!$ping) {
                if(!$silent) {
                    echo 'Ping not found.'; 
                }
                $result = false;
                
            } else {
                # Reset ping
                $ping->httpCode = self::HTTP_CODE_IN_PROGRESS;
                $ping->duration = 0;
                $ping->error = '';
                $ping->save();

                $batch = $ping->getBatch();
                $url = $batch->getUrl();
                $proxy = $ping->getProxy();
                        
                $result = self::doPing($ping, $url, $proxy);
            }
        }
        
        SystemUtility::log('PING RELOAD END', true);
        
        return $result;
    }
    
    public static function doPing($ping, $url = null, $proxy = null, $silent = true) {
        if(!$url) {
            $batch = $ping->getBatch();
            $url = $batch->getUrl();
        }
        
        if(!$proxy) {
            $proxy = $ping->getProxy();
        }
                
        # Do actual ping-request
        SystemUtility::log('CURLING URL: ' . $url->address);
        SystemUtility::log('WITH PROXY: ' . $proxy->address);
        $pingInfo = NetUtility::ping($url->address, $proxy->address);
        SystemUtility::log('PING RESPONSE: ' . print_r($pingInfo, true));
        
        # Update ping
        $ping->httpCode = $pingInfo['httpCode'];
        $ping->duration = $pingInfo['duration'];
        $ping->error = $pingInfo['error'];
        $ping->updatedAt = SystemUtility::getSqlNowDate();
        $ping->save();

        if(!$silent){
            var_dump($pingInfo);
        }
        
        return $pingInfo;
    }
}
