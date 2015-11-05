<?php

use Phalcon\Mvc\Model;
use Pingaroo\Utilities\SystemUtility;

class BaseModel extends Model
{
    public function beforeCreate(){
        $this->createdAt = SystemUtility::getSqlNowDate();
    }
    
    public function createOrUpdate(array $values, $id = null) {
        if($id == null) {
            $model = new self;
        } else {
            $model = self::findFirst();
            if(!$model) {
                $model = new self;
            }
        }

        foreach($values as $key=>$value) {
            $model->{$key} = trim($value);
        }

        return $model->save();
    }
}
