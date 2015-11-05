<?php
namespace Pingaroo\Utilities;

class SystemUtility {
   
    /**
     * Returns an array with the registered namespaces
     * 
     * @return array
     */
    public static function getNamespaces() {
        $arNamespaces = array();
        
        foreach(get_declared_classes() as $name) {
            $arNamespaces[]= $name;
        }        
        
        return $arNamespaces;
    } 
    
    public static function getSqlNowDate() {
        return date('Y-m-d H:i:s');
    }
    
    public static function log($data, $withDelimiter = false) {
        $nl = '
';
	$delimiter =  $withDelimiter ? $nl . '=====================' . $nl : '';
	
        $logsDir = defined('LOGS_DIR') ? LOGS_DIR : __DIR__;
        
        $logFile = $logsDir . '\\' . basename($_SERVER['SCRIPT_FILENAME'], '.php') . '_' . date('Y_m_d') . '.log';
        $logData = date('H:i:s') . ' ' . $data . $nl;

        file_put_contents($logFile, $delimiter . $logData, FILE_APPEND);
    }
}