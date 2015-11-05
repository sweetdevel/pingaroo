<?php

namespace Pingaroo\Validations;

use Phalcon\Validation;
use Phalcon\Validation\Validator\Regex as RegexValidator;
use Phalcon\Validation\Validator\Url as UrlValidator;

class ProxyValidation extends Validation
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
           'countryId', new RegexValidator(array(
           'pattern' => '/^[0-9]{1,}$/',
           'message' => 'The :field must be numeric.'
        )));
        
        $this->add('address', new UrlValidator(array(
            'message' => 'The :field must be a url'
        )));      
    }
}

?>