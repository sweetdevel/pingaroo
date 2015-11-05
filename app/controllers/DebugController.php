<?php

use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Model\Transaction\Manager as TransactionManager;
use Phalcon\Mvc\Model\Query\Builder;


class DebugController extends BaseController {
    public function indexAction() {
        set_time_limit(0);
        
        $this->createProxies();
        
        $this->view->disable();
    }
    
    private function loadProxyJson() {
        return json_decode(file_get_contents(ROOT_PATH . '/proxies.json'));
    }

    private function createProxies() {
        $json = $this->loadProxyJson();
        foreach($json as $entry) {
            $country = Country::findFirst(array("code = '{$entry->country}'"));
            if(!$country) {
                $country = new Country;
                $country->code = strtoupper($entry->country);
                $country->name = $entry->country;
                $country->save();
            }
            
            $proxy = Proxy::findFirst(array("address = '{$entry->url}'"));
            if(!$proxy) {
                $proxy = new Proxy;
                $proxy->countryId = $country->id;
                $proxy->address = $entry->url;
                $proxy->save();
                
                echo '<br />Proxy created: ' . $entry->url;
            } else {
                echo '<br />Proxy exists...';
            }
        }
    }
   
}