<?php

use Phalcon\Mvc\Model;

class Proxy extends Model
{
    # Initializations
    public function initialize()
    {
        # Table name
        $this->setSource('proxies');
        
        # Relationships
        $this->hasMany('id', Ping::class, 'proxyId');
        $this->hasOne('countryId', Country::class, 'id');
    }
    
    # Declare each column for PHP versions < 5.4 to save memory
    /*
    public $id;
    public $countryId;
    public $address;
    public $createdAt;
    public $updatedAt;
    */
}
