<?php

use Phalcon\Mvc\Model;

class Batch extends BaseModel
{
    # Initializations
    public function initialize()
    {
        # Table name
        $this->setSource('batches');
        
        # Relationships
        $this->hasOne('urlId', Url::class, 'id');
        $this->hasMany('id', Ping::class, 'batchId');
    }
    
    # Declare each column for PHP versions < 5.4 to save memory
    /*
    public $id;
    public $urlId;
    public $createdAt;
    public $updatedAt;
    */
}
