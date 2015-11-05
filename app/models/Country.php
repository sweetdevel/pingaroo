<?php

use Phalcon\Mvc\Model;

class Country extends BaseModel
{   
    # Initializations
    public function initialize()
    {
        # Table name
        $this->setSource('countries');
        
        # Relationships
        $this->hasMany('id', Proxy::class, 'countryId');
    }
    
    # Declare each column for PHP versions < 5.4 to save memory
    /*
    public $id;
    public $code;
    public $name;
    public $createdAt;
    public $updatedAt;
    */
}
