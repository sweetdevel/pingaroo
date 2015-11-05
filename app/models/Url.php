<?php

use Phalcon\Mvc\Model;

class Url extends BaseModel
{
    # Initializations
    public function initialize()
    {        
        # Table name
        $this->setSource('urls');
        
        # Relationships
        $this->hasMany('id', Batch::class, 'urlId');
    }
    
    # Declare each column for PHP versions < 5.4 to save memory
    /*    
    public $id;
    public $address;
    public $createdAt;
    public $updatedAt;
    */
}
