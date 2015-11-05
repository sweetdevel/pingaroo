<?php

namespace Pingaroo\Helpers;

use Pingaroo\Utilities\SystemUtility;
use Pingaroo\Managers\AjaxControllerManager;

/**
 * Description of BatchManager
 *
 * @author Marius
 */
class BatchHelper {
    private static function createCommand($config, $request) {
        $jsonRequest = json_encode($request);         
        if(PHP_OS == 'WINNT') {
           $jsonRequest = str_replace('"', '\\\\\\"', (string)$jsonRequest);                
        }

        $workerURL = AjaxControllerManager::generateRequestURL(
                        $config, 
                        'worker', 
                        $jsonRequest
                    );

        $cmd = "echo file_get_contents('" . $workerURL . "');";
        $cmd .= "Sleep(10);";
        
        return $cmd;
    }
    
    public static function create($config, $batchId, $urlId, array $proxyIds) {
        SystemUtility::log('BATCH START', true);
        
        $lastIndex = count($proxyIds) - 1;
        foreach($proxyIds as $index=>$proxyId) {
            $jsonRequest = array(
                'action' => 'pingCreate',
                'args' => array(
                    'batchId' => $batchId,
                    'urlId' => $urlId,
                    'proxyId' => $proxyId,
                    'updateBatch' => ($index == $lastIndex) ? '1' : '0',
                )
            );
            
            $cmd = self::createCommand($config, $jsonRequest);
            
            SystemUtility::log('ASYNC SCRIPT CALL: ' . $cmd);
            
            SystemUtility::runAsyncCommand($config, $cmd);
        }
        
        SystemUtility::log('BATCH END');
    }
    
    public static function reload($config, $batch) {        
        $pings = $batch->getPing()?: array();

        SystemUtility::log('BATCH RELOAD START', true);
        
        foreach($pings as $ping) {
            #+TEMP CODE - FOR UI REFRESHING FASTER
            # Reset ping
            $ping->httpCode = PingHelper::HTTP_CODE_IN_PROGRESS;
            $ping->duration = 0;
            $ping->error = '';
            $ping->save();
            #-TEMP CODE
            
            $jsonRequest = array(
                'action' => 'pingReload',
                'args' => array(
                    'id' => $ping->id
                )
            );
            
            $cmd = self::createCommand($config, $jsonRequest);
            
            SystemUtility::log('ASYNC SCRIPT CALL: ' . $cmd);
        
            #var_dump($cmd); exit;
            SystemUtility::runAsyncCommand($config, $cmd);
        }
        
        SystemUtility::log('BATCH RELOAD END');        
    }
            
}
