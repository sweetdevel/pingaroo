<?php

namespace Pingaroo\Classes;

/**
 * Use this class to route resources dinamically only for micro apps
 *
 * @author Marius
 */
class MicroRouter {
    public static function resource(&$app, $path, $controller) {
        $collection = new \Phalcon\Mvc\Micro\Collection();
        $collection->setHandler(new $controller());
       
        // index
        $collection->get($path, 'indexAction');

        // show?
        $collection->get("{$path}/search/{id}", 'searchAction');

        // edit?
        $app->get("{$path}/{id:[0-9]+}", 'editAction');

        // store
        $app->post($path, 'storeAction');

        // update
        $app->put("{$path}/{id:[0-9]+}", 'updateAction');

        // delete
        $app->delete("{$path}/{id:[0-9]+}", 'deleteAction');
        
        $app->mount($collection);
    }
}
