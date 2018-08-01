<?php
/**
 * Recapo <http://recapo.de>
 *
 * @link      http://github.com/lherich/recapo.de
 * @copyright Copyright (c) 2014 Lucas Herich <info@recapo.de>
 * @license   MIT License <http://recapo.de/license>
 */

namespace Recapo;

/**
 * Bootloader for general stuff
 */
class Recapo
{
    /**
     * @const string
     */
    const VERSION = '1.3';
    
    /**
     * @var array
     */
    protected $_conf;
    
    /**
     * @var \Slim\Slim
     */
    protected $_slim;
    
    public function __construct(array $pConf = array())
    {
        /**
         * define some constants
         */
        define('SALT', isset($pConf['salt']) ? $pConf['salt'] : '');
        define('LIFETIME', isset($pConf['lifetime']) ? $pConf['lifetime'] : 120);
        define('MODE', isset($pConf['mode']) ? $pConf['mode'] : 'development');
        define('VERSION', self::VERSION);
        
        /**
         * merge basic configuration
         */
        $this->_conf = array_merge(static::getDefaultConf(), $pConf);
        
        /**
         * merge pathes
         */
        $this->_conf['path'] = array_merge(
            static::getDefaultPathes(
                $this->_conf['domain'],
                $this->_conf['url_path'],
                $this->_conf['absolute_path']
            ), 
            isset($pConf['path']) ? $pConf['path'] : array()
        );
        
        /**
         * merge database settings
         */
        $this->_conf['db'] = array_merge(
            static::getDefaultDb(), 
            isset($pConf['db']) ? $pConf['db'] : array()
        );
        $this->_conf['db']['dsn'] = $this->_conf['db']['type'].':dbname='.$this->_conf['db']['dbname'].';host='.$this->_conf['db']['host'];
        
        /**
         * merge variables for the view
         */
        $this->_conf['view_vars'] = array_merge(
            static::getDefaultViewVars($this->_conf), 
            isset($pConf['view_vars']) ? $pConf['view_vars'] : array()
        );
         
        /**
         * do some basic staff
         */
        $this->bootloader();
    }
    
    
    protected function bootloader()
    {
        /**
         * error reporting
         */
        if(MODE === 'development') {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            ini_set('html_errors', 1);
        } else {
            error_reporting(null);
            ini_set('display_errors', 0);
            ini_set('display_startup_errors', 0);
            ini_set('html_errors', 0);
        }
        
        set_include_path(get_include_path().PATH_SEPARATOR.$this->_conf['path']['app'].PATH_SEPARATOR.$this->_conf['path']['library']);

        require 'Slim/Slim.php';
        require 'helper.php';

        \Slim\Slim::registerAutoloader();
        
        /**
         * construct
         */
        $this->_slim = new \Slim\Slim($this->_conf);
        
        /**
         * error
         */
        \Recapo\Helper\Errorhandler::$_app = $this->_slim;
        $this->_slim->error(array('\Recapo\Helper\Errorhandler', 'hardErrorhandler'));
        set_error_handler(array('\Recapo\Helper\Errorhandler', 'softErrorhandler'));
        set_exception_handler(array('\Recapo\Helper\Errorhandler', 'hardErrorhandler'));
        register_shutdown_function(array('\Recapo\Helper\Errorhandler', 'fatalErrorhandler'));

        /**
         * session
         */
        if (session_id() === '') {
            session_start();
        }
        session_cache_limiter(false);
        
        /**
         * View
         */
        $this->_slim->view(new \Slim\Views\Twig());
        $_view = $this->_slim->view();
        $_view->parserDirectory = $this->_conf['path']['library'];
        $_view->twigTemplateDirs = array($this->_conf['path']['app']);
        $_view->parserOptions = array(
            'debug' => $this->_conf['debug'],
            'cache' => (MODE === 'development')  ? false : $this->_conf['path']['tmp'],
        );
        $_view->parserExtensions = array(
            new \Slim\Views\TwigExtension(),
            new \Recapo\Views\TwigRecapoExtension(),
        );
        
        
        $_slim = &$this->_slim;
        /**
         * database
         */
        $_slim->container->singleton('db', 
            function () use ($_slim)
            {
                $db = $_slim->settings['db'];
                $tmp = new \Recapo\Database\PdoExtended($db['dsn'], $db['user'], $db['pass'], array(\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
                if (MODE === 'development') {
                    $tmp->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                } else {
                    $tmp->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_SILENT);
                }
                
                return $tmp;
            }
        );
        
        /**
         * Slim Hooks
         */
        $_slim->hook('slim.before', function () use ($_slim)
            {
                $_view = $_slim->view();
                foreach ($_slim->settings['view_vars'] as $key => $value) {
                    $_view->set($key, $value);
                }
            }
        );

        $_slim->hook('slim.before.dispatch', function () use ($_slim)
            {
                $_name = $_slim->router()->getCurrentRoute()->getName();
                $_view = $_slim->view();
                if (isset($_slim->routes[$_name]['callback'])) {
                    $_view->set('CURRENT_ROUTE', $_slim->urlFor($_name));
                    $_view->set('CURRENT_NAME', $_name);
                    $_view->set('CURRENT_NAME_UNDERSCORE', str_replace('/', '_', $_name));
                }
            }
        );
    }
    
    
    
    protected static function getDefaultConf()
    {
        return array(
            'mode'                => MODE,
            'debug'               => false,
            'lifetime'            => LIFETIME, // in minutes
                    
            'VERSION'                 => VERSION,
            'CURRENT_ROUTE'           => '/notfound',
            'CURRENT_NAME'            => '/notfound',
            'CURRENT_NAME_UNDERSCORE' => '_notfound',
    
            'domain'              => 'http://localhost', // domain
            'url_path'            => '/', // everthing after the domain
            'absolute_path'       => '', // the absolute path within the directory structure
            
            'tpl_extension'        => '.twig',
            
            'cookies.lifetime'    => LIFETIME.' minutes',
            'cookies.domain'      => null,
            'cookies.path'        => '/',
            'cookies.secure'      => true,
            'cookies.httponly'    => true,
            'cookies.secret_key'  => md5(md5(LIFETIME).md5(SALT)), // if the lifetime changes within the settings the cookie should be eaten by the cookie monster
            'cookies.cipher'      => MCRYPT_RIJNDAEL_256,
            'cookies.cipher_mode' => MCRYPT_MODE_CBC,
            
            'path'                => array(),
            'db'                  => array()
        );
    }
    
    protected static function getDefaultPathes(
        $pDomain, $pUrl_path, $pAbsolutePath)
    {
        return array(
            'app'      => $pAbsolutePath.'app'.DIRECTORY_SEPARATOR,
            'library'  => $pAbsolutePath.'library'.DIRECTORY_SEPARATOR,
            'tmp'      => $pAbsolutePath.'tmp'.DIRECTORY_SEPARATOR,
            'web'      => $pAbsolutePath.'web'.DIRECTORY_SEPARATOR,

            'domain'   => $pDomain,
            'url_path' => $pUrl_path,
            'base'     => $pDomain.$pUrl_path,
            
            'css'      => $pUrl_path.'css/',
            'img'      => $pUrl_path.'img/',
            'vendor'   => $pUrl_path.'vendor/'
        );
    }
    
    protected static function getDefaultDb()
    {
        return array(
            'dsn'       => null,
            'type'      => 'mysql',
            'host'      => 'localhost',
            'dbname'    => 'recapo',
            'user'      => 'root',
            'pass'      => ''
        );
    }
    
    protected static function getDefaultViewVars($pConf)
    {
        return array(
            'WEB'                     => $pConf['path']['web'],
            'URLPATH'                 => $pConf['path']['url_path'],
            'CSS'                     => $pConf['path']['css'],
            'IMG'                     => $pConf['path']['img'],
            'VENDOR'                  => $pConf['path']['vendor'],
            'BASE'                    => $pConf['path']['base'],
            'DOMAIN'                  => $pConf['path']['domain'],

            'VERSION'                 => VERSION,
            'CURRENT_ROUTE'           => $pConf['CURRENT_ROUTE'],
            'CURRENT_NAME'            => $pConf['CURRENT_NAME'],
            'CURRENT_NAME_UNDERSCORE' => $pConf['CURRENT_NAME_UNDERSCORE'],
        );
    }
    
    
    public static $_app = null;
    public static $_ID = 0;

    public function parseXml($pXmlFile)
    {
        $_db = $this->_slim->db;
        $_view = $this->_slim->view;

        // lazy xml loading
        $xml = simplexml_load_file($pXmlFile);
        $json = json_encode($xml);
        $data = json_decode($json, true);
        if (isset($data['errors'])) {
            $this->parseXmlErrors($data['errors']['error']);
        }

        if (isset($data['conditions'])) {
            $this->parseXmlConditions($data['conditions']);
        }
        if (isset($data['routes']['route'])) {
            if (!isset($data['routes']['route'][0]) || !is_array($data['routes']['route'][0]) && trim($data['routes']['route'][0])) {
                // if is string convert to 0-array
                $data['routes']['route'] = array(0 => $data['routes']['route']);
            }
            if (is_array($data['routes']['route'])) {
                $this->parseXmlRoutes($data['routes']['route']);
            }
        }
        if (isset($data['routes']['file'])) {
            if (!is_array($data['routes']['file']) && trim($data['routes']['file'])) {
                // if is string convert to 0-array
                $data['routes']['file'] = array(0 => $data['routes']['file']);
            }
            if (is_array($data['routes']['file'])) {
                $this->parseXmlFile($data['routes']['file']);
            }
        }
        unset($data);
    }

    public function parseXmlErrors($pData)
    {
        \Recapo\Helper\Errorhandler::addExceptions($pData);
        unset($pData);
    }

    public function parseXmlConditions($pData)
    {
        \Slim\Route::setDefaultConditions(\Slim\Route::getDefaultConditions() + $pData);
        unset($pData);
    }

    public function parseXmlFile($pData)
    {
        foreach ($pData as $file) {
            $this->parseXml($this->_conf['path']['app'].$file);
        }
    }

    public function parseXmlRoutes($pData)
    {
        $_app = &$this->_slim;
        // init db connection (so other classes can use it)
        $_db = $_app->db;
        $_view = $_app->view;

        $pDataCount = count($pData);
        if ($pDataCount == 8) {
            var_dump($pData);
            exit();
        }

        for ($i = 0; $i < $pDataCount; $i++) {
            if (!isset($pData[$i]['name'], $pData[$i]['route'], $pData[$i]['method'], $pData[$i]['path'])) {
                throw new \InvalidArgumentException('A node for "name", "route", "method" or "path" in route '.print_r($pData, true).$pDataCount.'is missing.');
            }
            $tmp = explode('/', $pData[$i]['name']);
            $this->_conf['routes'][$pData[$i]['name']] = array(
                    'id' => self::$_ID,
                    'name' => $pData[$i]['name'],
                    'route' => $pData[$i]['route'],
                    'method' => $pData[$i]['method'],
                    'path' => $pData[$i]['path'],
                    'this' => array_pop($tmp),
                    'sql' => array(),
                    'tpl' => array(),
                    'js' => array(),
                    'middlewares' => array(0 => new \Recapo\Middleware\DefaultMiddleware()),
                    'callback' => null,
                );

            // set pointer for easy usage
            $_route = &$this->_conf['routes'][$pData[$i]['name']];

            // set Callback
            $_route['callback'] = self::getCallbackRoute($pData[$i], $_route);

            // add a route for every JS file
            if (isset($pData[$i]['js'])) {
                if (!is_array($pData[$i]['js']) && trim($pData[$i]['js'])) {
                    // if is string convert to 0-array
                    $pData[$i]['js'] = array(0 => $pData[$i]['js']);
                }
                if (is_array($pData[$i]['js'])) {
                    self::setJavascriptRoutes($pData[$i]['js'], $_route);
                }
            }
            // add Middlewares and add the route to Slim
            if (isset($pData[$i]['middleware'])) {
                if (!is_array($pData[$i]['middleware']) && trim($pData[$i]['middleware'])) {
                    // if is string convert to 0-array
                    $pData[$i]['middleware'] = array(0 => $pData[$i]['middleware']);
                }
                if (is_array($pData[$i]['middleware'])) {
                    foreach ($pData[$i]['middleware'] as $middleware) {
                        $newMiddleware = '\Recapo\Middleware\\'.$middleware;
                        $newMiddleware = new $newMiddleware();
                        $newMiddleware->setApplication($_app);
                        $newMiddleware->setNextMiddleware($_route['middlewares'][0]);
                        array_unshift($_route['middlewares'], $newMiddleware);
                    }
                    $_app->$_route['method']($_route['route'], array($_route['middlewares'][0], 'call'), $_route['callback'])->name($_route['name']);
                } else {
                    $_app->$_route['method']($_route['route'], $_route['callback'])->name($_route['name']);
                }
            } else {
                $_app->$_route['method']($_route['route'], $_route['callback'])->name($_route['name']);
            }

            // garbage collector
            unset($pData[$i]);
            self::$_ID++;
        }
        unset($pData);

        // set the routes configuration array for further use
        if (is_array($_app->__get('routes'))) {
            $_app->__set('routes', $_app->__get('routes') + $this->_conf['routes']);
        } else {
            $_app->__set('routes', $this->_conf['routes']);
        }

    //print_r($this->_conf['routes']);

        // set not found route
        if (isset($_app->routes['/notfound']['callback'])) {
            $_app->notFound($_app->routes['/notfound']['callback']);
        }
    }

    public function setJavascriptRoutes(&$_node, &$_route)
    {
        $_app = &$this->_slim;
        foreach ($_node as $jsFile) {
            $_jsroute = '/js/'.$_route['path'].((substr($_route['path'], -1) == '/') ? '' : '/').$jsFile.'.js';
            $_jsname  = '/js/'.$_route['path'].((substr($_route['path'], -1) == '/') ? '' : '/').$jsFile;
            $_jspath  = $_app->settings['path']['app'].$_route['path'].$jsFile.'.js';

            $_app->get($_jsroute, function () use ($_app, $_jspath) {
                $_app->response->headers->set('Content-Type', 'text/javascript');
                print file_get_contents($_jspath);
            })->name($_jsname);
        }
    }

    public function getCallbackRoute(&$_node, &$_route)
    {
        $_app = &$this->_slim;

        return function () use (&$_app, &$_node, &$_route) {
            $_params = func_get_args();
            $_params = array_shift($_params); // make sure to put "array(0=>$this->getParams())" in Slim/Route.php at line 462
            $_this   = &$_route['this'];
            $_db     = &$_app->db;
            $_view   = &$_app->view;

            // for what?
            if ($_route['name'] == '/notfound') {
                $_route['middlewares'][0]->call();
            }

            if (isset($_node['php'])) {
                // PHP file

                // load SQL files and prepare SQL queries
                if (isset($_node['sql'])) {
                    // if is string convert to 0-array
                    if (!is_array($_node['sql'])) {
                        $_node['sql'] = array(0 => $_node['sql']);
                    }

                    foreach ($_node['sql'] as $sqlFile) {
                        $_route['sql'][$sqlFile] = $_db->prepare(file_get_contents($_app->settings['path']['app'].$_route['path'].$sqlFile.'.sql'));
                    }
                }

                // load TPL file pathes
                if (isset($_node['tpl'])) {
                    // if is string convert to 0-array
                    if (!is_array($_node['tpl'])) {
                        $_node['tpl'] = array(0 => $_node['tpl']);
                    }

                    foreach ($_node['tpl'] as $tplFile) {
                        $_route['tpl'][$tplFile] = $_route['path'].$tplFile.$_app->settings['tpl_extension'];
                    }
                }

                // execute PHP file
                require_once $_app->settings['path']['app'].$_route['path'].$_node['php'].'.php';
            } else {
                // display TPL
                if (is_array($_node['tpl'])) {
                    $_node['tpl'] = array_pop($_node['tpl']);
                }
                if (isset($_node['tpl'])) {
                    $_view->display($_route['path'].$_node['tpl'].$_app->settings['tpl_extension']);
                }
            }
        };
    }

    public function run()
    {
        $this->_slim->run();
    }
}
