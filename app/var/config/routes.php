<?php

return array(
    '/:controller' => array(
        'params' => array(
            'controller' => 1
        )
    ),
    '/:controller/:action' => array(
        'params' => array(
            'controller' => 1,
            'action' => 2
        ),
        'name' => 'home',
        'via'  => array('GET', 'POST', 'HEAD', 'OPTIONS'),
        'hostname' => 'localhost'
    ),
    '/:controller/:action/:params' => array(
        'params' => array(
            'controller' => 1,
            'action' => 2,
            'params' => 3
        ),
        'name' => 'home',
        'via'  => array('GET', 'POST', 'HEAD', 'OPTIONS'),
        'hostname' => 'localhost'
    )    
);