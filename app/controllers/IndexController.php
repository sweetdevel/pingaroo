<?php

use Phalcon\Mvc\Controller;
use App\Validations\CountryValidation;
use App\Managers\FormReflectionManager;
use Phalcon\Mvc\Model\Transaction\Manager as TransactionManager;

class IndexController extends BaseController {
    public function indexAction() {
        // ...
    }
	
	public function notFoundAction() {
		$msg = '404 - Not found.';
		$msg .= '<br /><br />Try the ';
		$msg .= '<a href="http://' . $_SERVER['HTTP_HOST'] . $this->config->application->baseUri .  'index">index</a> page.';
		
		echo $msg;
		
		$this->view->disable();
	}
}