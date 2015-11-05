<?php

use Phalcon\Mvc\Model;

class Ping extends BaseModel
{   
    # Initializations
    public function initialize()
    {
        # Table name
        $this->setSource('pings');
        
        # Relationships
        $this->hasOne('batchId', Batch::class, 'id');
        $this->hasOne('proxyId', Proxy::class, 'id');
    }
    
    # Declare each column for PHP versions < 5.4 to save memory
    /*
    public $id;
    public $batchId;
    public $proxyId;
    public $httpCode;
    public $duration;
    public $createdAt;
    public $updatedAt;
    */
}
