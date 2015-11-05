<?php

namespace Pingaroo\Managers;

/**
 * Preserves the old parameteres from the previous request in flash message format
 */
class FormReflectionManager {
    const SESSION_KEY = '_reflectionSessionData';
    const DELIMITER = '}{-}{';
    
    private $flashObj;
    private $cache;        
    
    /**
     * Sets the internat flash object
     * @param Phalcon\Flash\Session $flash
     */
    public function __construct($flash) {
        $this->flashObj = $flash;
    }
    
    /**
     * Adds the array to flash buffer for later reflection
     * 
     * @param array $data
     */
    public function add(array $data) {
        foreach($data as $key=>$value) {
           $this->flashObj->message($this::SESSION_KEY, $key . $this::DELIMITER . $value);            
        }
    }
    
    /**
     * Returns the old value by key from the previously saved data in flash buffer
     * 
     * @param int|string $key
     * @return null|string
     */
    public function get($key) {
        if(array_key_exists($key, $this->getCache())) {
            return $this->getCache()[$key];
        }
        return null;
    }
    
    /**
     * Alias for getCache()
     * 
     * @return array
     */
    public function all() {
        return $this->getCache();
    }
    
    /**
     * Returns the existing cached array if it already set
     * Returns the array from the previous saved buffer
     * 
     * @return array
     */
    private function getCache() {
        if($this->cache){
            return $this->cache;
        } else {
            return $this->buildCache();
        }
    }
    
    /**
     * Creates an associative array from the flash buffer
     * And saves it the $cache attribute
     * 
     * @return array
     */
    private function buildCache() {
        $this->cache = array();
        $data = $this->flashObj->getMessages($this::SESSION_KEY, false);
        
        if($data) {
            foreach($data as $message) {
                $keyValuePair = explode($this::DELIMITER, $message);
                $this->cache[$keyValuePair[0]] = $keyValuePair[1];
            }
        }
        
        return $this->cache;
    }
}

