<?php

return array(
    /**
     *
     */
    'application'  => require_once(ROOT_PATH . '/app/var/config/application.php'),

    /**
     *
     */
    'database'     => require_once(ROOT_PATH . '/app/var/config/database.php'),

    /**
     *
     */
    'cache'        => require_once(ROOT_PATH . '/app/var/config/cache.php'),

    /**
     *
     */
    'routes'       => require_once(ROOT_PATH . '/app/var/config/routes.php'),
    
    /**
     * Miscellaneous
     */
    'misc'       => require_once(ROOT_PATH . '/app/var/config/misc.php'),    
);