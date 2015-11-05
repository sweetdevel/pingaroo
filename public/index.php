<?php

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(__FILE__)));
}

error_reporting(E_ALL);
date_default_timezone_set('Europe/Bucharest');


try {

    # Register Composer autoloader
    include_once ROOT_PATH . '/app/var/Bootstrap.php';
    
    // Handle the request
    $application = new Bootstrap(new \Phalcon\DI\FactoryDefault());
    echo $application->run();

} catch (Exception $e) {
     echo "Exception: ", $e->getMessage();
}
