<?php

namespace Pingaroo\Validations;

use Phalcon\Validation;
use Phalcon\Validation\Validator\Regex as RegexValidator;
use Phalcon\Validation\Validator\Url as UrlValidator;

class BatchValidation extends Validation
{
    public function initialize()
    {
        $this->add(
           'name', new RegexValidator(array(
           'pattern' => '/^.{1,45}$/',
           'message' => 'The :field length must have between 1 and 45 characters.'
        )));
                
        $this->add(
           'address', new RegexValidator(array(
           'pattern' => '/^[0-9]{1,}$/',
           'message' => 'The :field must be numeric.'
        )));
             
    }
}

?>