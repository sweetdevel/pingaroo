<?php

use Phalcon\Loader;
use Phalcon\Tag;
use Phalcon\Config;
use Phalcon\Mvc\Url;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Application;
use Phalcon\Flash\Session;
use Phalcon\Session\Adapter\Files;
use Phalcon\DI\FactoryDefault;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\Mvc\Router;
use Phalcon\Cache\Frontend\Output as CacheFront;
use Phalcon\Cache\Backend\File as CacheBackFile;
use Phalcon\Cache\Backend\Apc as CacheBackApc;


class Bootstrap
{

    private $di;

    /**
     * Constructor
     *
     * @param $di
     */
    public function __construct($di)
    {
        $this->di = $di;
    }

    /**
     * Runs the application performing all initializations
     *
     * @param $options
     *
     * @return mixed
     */
    public function run($options = array())
    {
        $loaders = array(
            'session',
            'config',
            'loader',
            'url',
            'router',
            'view',
            'db',
            'flash',
            'cache',
        );

        foreach ($loaders as $service) {
            $function = 'init' . ucfirst($service);

            if (method_exists($this, $function)) {
                $this->$function();
            }
        }

        $application = new Application();
        $application->setDI($this->di);

        if (PHP_OS == 'Linux') {
            $uri = $_SERVER['REQUEST_URI'];
        } else {
            $uri = null;
        }

        return $application->handle($uri)->getContent();
    }

    // Protected functions
    /**
     * Initializes the session
     *
     * @param array $options
     */
    protected function initSession($options = array())
    {
        $this->di['session'] = function () {
            $session = new Files();
            $session->start();
            return $session;
        };
    }

    /**
     * Initializes the config. Reads it from its location and
     * stores it in the Di container for easier access
     *
     * @param array $options
     */
    protected function initConfig($options = array())
    {
        $configFile  = require(ROOT_PATH . '/app/var/config.php');

        // Create the new object
        $config = new Config($configFile);

        // Store it in the Di container
        // Settings cones from the include
        $this->di['config'] = $config;
    }

    /**
     * Initializes the loader
     *
     * @param array $options
     */
    protected function initLoader($options = array())
    {
        $config = $this->di['config'];

        // Creates the autoloader
        $loader = new Loader();
        $loader->registerDirs(
            array(
                $config->application->controllersDir,
                $config->application->modelsDir
            )
        );
        $loader->registerNamespaces(array(
            'Pingaroo'           => $config->application->libraryDir . '/Pingaroo/',
            'Psr\Http\Message'   => $config->application->libraryDir . '/psr/http-message/src',
            'GuzzleHttp\Psr7'    => $config->application->libraryDir . '/guzzlehttp/psr7/src',
            'GuzzleHttp\Promise' => $config->application->libraryDir . '/guzzlehttp/promises/src',
            'GuzzleHttp'         => $config->application->libraryDir . '/guzzlehttp/guzzle/src',
        ));

        $loader->register();

        // Dump it in the DI to reuse it
        $this->di['loader'] = $loader;
    }

    /**
     * Initializes the baseUrl
     *
     * @param array $options
     */
    protected function initUrl($options = array())
    {
        $config = $this->di['config'];

        /**
         * The URL component is used to generate all kind of urls in the
         * application
         */
        $this->di['url'] = function () use ($config) {
            $url = new Url();
            $url->setBaseUri($config->application->baseUri);

            return $url;
        };
    }

    /**
     * Initializes the router
     *
     * @param array $options
     */
    protected function initRouter($options = array())
    {
        $config = $this->di['config'];
        $this->di['router'] = function () use ($config) {
            $router = new Router(false);
            $router->notFound(array(
                'controller' => 'index',
                'action'     => 'notFound',
            ));
            $router->removeExtraSlashes(true);

            foreach ($config['routes']->toArray() as $route => $items) {
                $route = $router->add($route, $items['params']);

                if (isset($items['name'])) {
                    $route->setName($items['name']);
                }

                if (isset($items['via'])) {
                    $route->via($items['via']);
                }

                if (isset($items['hostname'])) {
                    $route->setHostname($items['hostname']);
                }
            }

            return $router;
        };
    }

    /**
     * Initializes the view and Volt
     *
     * @param array $options
     */
    protected function initView($options = array())
    {
        $config = $this->di['config'];
        $di     = $this->di;

        /**
         * Setup the view service
         */
        $this->di['view'] = function () use ($config, $di) {
            $view = new View();
            $view->setViewsDir($config->application->viewsDir);
            $view->registerEngines(array(
                '.volt' => function ($view , $di) use ($config) {
                    $volt = new \Phalcon\Mvc\View\Engine\Volt($view , $di);
                    $volt->setOptions(array(
                        'compiledPath'      => $config->application->voltDir ,
                        'compiledSeparator' => '_',
                        'compileAlways'     => $config->application->debug
                    ));

                    return $volt;
                },
                '.phtml' => 'Phalcon\Mvc\View\Engine\Php', // Generate Template files uses PHP itself as the template engine
            ));

            return $view;
        };
    }

    /**
     * @param array $options
     */
    protected function initDb($options = array())
    {
        $config = $this->di['config'];

        $this->di['db'] = function () use ($config) {
            return new DbAdapter($config->database->toArray());
        };
    }

    /**
     * @param array $options
     */
    protected function initFlash($options = array())
    {
        $this->di['flash'] = function () {
            return new Phalcon\Flash\Session();
        };
    }

    /**
     * Initializes the cache
     *
     * @param array $options
     */
    protected function initCache($options = array())
    {
        $config = $this->di['config'];

        $this->di['viewCache'] = function () use ($config) {
            // Get the parameters
            $frontCache      = new CacheFront(array(
                'lifetime' => $config->cache->lifetime
            ));

            if (function_exists('apc_store')) {
                $cache = new CacheBackApc($frontCache);
            } else {
                $cache          = new CacheBackFile($frontCache, array(
                    'cacheDir' => $config->cache->cacheDir
                ));
            }
            return $cache;
        };

        $this->di->set('cacheData', function () use ($config) {
            $frontCache = new CacheFront(array(
                'lifetime' => 60 * 60
            ));

            $cache = new CacheBackFile($frontCache, array(
                'cacheDir' => $config->cache->cacheDir
            ));

            return $cache;
        }, true);
    }
}