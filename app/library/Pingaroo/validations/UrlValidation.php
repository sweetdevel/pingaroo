<?php

namespace Pingaroo\Validations;

use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Regex as RegexValidator;
use Phalcon\Validation\Validator\Url as UrlValidator;

class UrlValidation extends Validation
{
    public function initialize()
    {
        /*
        $this->add(
           'id', new RegexValidator(array(
           'pattern' => '/^[0-9]{1,}$/',
           'message' => 'Invalid indentifier'
        )));
        
        $this->add(
            'address',
            new PresenceOf(
                array(
                    'message' => 'The name is required'
                )
            )
        );
        */
        
        $this->add('address', new UrlValidator(array(
            'message' => 'The :field must be a url'
        )));
    }
}

?>