<?php

namespace Pingaroo\Validations;

use Phalcon\Validation;
use Phalcon\Validation\Validator\Regex as RegexValidator;

class CountryValidation extends Validation
{
    public function initialize()
    {
        /*
        $this->add(
           'id', new RegexValidator(array(
           'pattern' => '/^[0-9]{1,}$/',
           'message' => 'Invalid indentifier'
        )));
        */
        
        $this->add(
           'code', new RegexValidator(array(
           'pattern' => '/^.{1,5}$/',
           'message' => 'The :field length must have between 1 and 5 characters.'
        )));
        
        $this->add(
           'name', new RegexValidator(array(
           'pattern' => '/^.{1,255}$/',
           'message' => 'The :field length must have between 1 and 255 characters.'
        )));         
    }
}

?>