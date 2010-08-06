<?
/**
 * Main framework class file.
 * Copyright (c) 2010, Djalma Oliveira (djalmaoliveira@gmail.com)
 * All rights reserved.
 * @license license.txt
 */
 
 
/**
 * Liber is a main class of framework.
 * 
 * How to setup configuration params:
 * <code>Liber::conf('APP_MODE', 'PROD');</code>
 * and to get params:
 * <code>Liber::conf('APP_MODE');</code>
 * There are many params that can be used:
 * <pre>
 * BASE_PATH        ->  Internal path to Framework;
 * APP_PATH         ->  Internal path to application dir;
 * APP_URL          ->  URL to access aplication (i.e. http://www.domain.com);
 * APP_ROOT         ->  Application path (i.e. /www/app/public_html/);
 * APP_MODE         ->  Values: 'DEV' for development (default) and 'PROD' for production mode;
 * DB_LAYER         ->  Class name used to manipulate the access to database, using BasicDb class by default;
 * TEMPLATE_ENGINE  ->  Class name used to manipulate a Template system, using TemplateEngine class by default;
 * ASSETS_DIR       ->  Default name to assets dir into web public access, APP_ROOT/assets/ by default;
 * LOG_PATH         ->  Default name to log dir where is stored log files,  APP_PATH/log/ by default;
 * CACHE_PATH       ->  Default name to cache dir where is stored cached files, APP_PATH/cache/ by default;
 * PAGE_NOT_FOUND   ->  Application Controller name of default for page not found,  'NotFoundController' by default;
 * SYS_ERROR        ->  Application Controller name of default for system error message,  'SysErrorController' by default;
 * LANG             ->  Application language, used to some system messages. See BASE_PATH/i18n. 'en' by default;
 * </pre>
 * By default all <i>paths</i> used, must have a final slash '/', like "/my/log/dir/".
 * @author Djalma Oliveira (djalmaoliveira@gmail.com)
 * @package core
 * @since 1.0
 */
class Liber {

    /**
    *   Framework version
    */
    const VERSION = '1.0b';


    /**
    *   Store all configurations of application.
    *   Format: $config['config'] = Array - Basic configurations, index are the same properties of Object.
    *           $config['route'] = Array - Configuration about how controller/action/module are invoke.
    *   @var Array
    */
    protected static $aConfig = Array(
                                    'BASE_PATH'         => '',      
                                    'APP_PATH'          => '',      
                                    'APP_URL'           => '',       
                                    'APP_ROOT'          => '',       
                                    'APP_MODE'          => 'DEV',    
                                    'DB_LAYER'          => 'BasicDb',      
                                    'TEMPLATE_ENGINE'   => 'TemplateEngine',      
                                    'ASSETS_DIR'        => 'assets',      
                                    'LOG_PATH'          => 'log/',      
                                    'CACHE_PATH'        => 'cache/',
                                    'PAGE_NOT_FOUND'    => 'NotFoundController',
                                    'SYS_ERROR'         => 'SysErrorController',
                                    'LANG'              => 'en'     
                                );
    /**
     * Route format:    $r[URI][METHOD] = Array('ControllerName', 'Action', 'ModuleName');
                        $r[URI][METHOD] = '/othet/route';
                        METHOD can be: *, get, post, put, delete;
                        When to use *, the others for the same URI will be skipped;
        uri             => the relative path of resource like: /article/firstArticle ;
        ControllerName  => the file name (without extension) of controller inside 'controller/' dir;
        Action          => the method name of controller;
                           when it is a "*", means that the next segment after ControllerName is a called action ;
        ModuleName      => the module name inside 'APP_PATH/module/' dir;
                            it means that the controller will be called from this module;
    *  Route examples:  $r['/admin/user']['*'] = Array('UserAdmin', 'index', 'Admin');
    *                   $r['/admin/user']['*'] = Array('UserAdmin', '*', 'Admin');
    *                   $r['/blog']['*'] = Array('Blog', 'index');
    Avoid this routes (don't works or works the wrong way):
        /blog/:id::name:   -> params together;
        /blog/post/:post:  -> get all, but depends of declared order;
        /blog/post/a:post: -> the previous route get this pattern;
        /blog/a:id:-:mes:  -> allowed only one param per segment;
    */   
    
    /**
        Route definitions.
        @var Array        
    */                                
    public    static $aRoute    = Array();        

    /**
    *   Database configuration
    *   @var Array
    */
    public    static $aDbConfig = Array('DEV'=>Array(), 'PROD' => Array());    

    /**
    *   Database layer instance
    *   @var Object
    */
    protected static $_db;

    /**
    *   Cache instance
    *   @var Object
    */
    protected static $_cache;

    /**
    *   Current controller instance
    *   @var Controller
    */
    protected static $_controller;

    /**
    *   Returns a database object instance.
    *   @return BasicDb based class,  the database singleton, auto create if the singleton has not been created yet.
    */
    public static function &db() {
        if ( is_object(self::$_db) ) { return self::$_db; }
        
        $c = Liber::conf('DB_LAYER');
        if (  $c == 'BasicDb' ) {
            self::loadClass($c);
        } else {
            self::loadClass(Liber::conf('DB_LAYER'), 'APP');
        }

        self::$_db = call_user_func($c .'::getInstance');
        return self::$_db;
    }

    /**
    *   Returns a cache instance.
    *   @param string $cacheType Cache type: file.
    *   @return Cache Object.
    */
    public static function cache($class='FileCache') {
        if ( !isset(self::$_cache) ) {
            self::load($class, Liber::conf('BASE_PATH').'cache/');
            self::$_cache = new $class;
        }
        return self::$_cache;
    }


    /**
    *   Start and execute application.
    */
    public static function run() {
        
        // error handler function
        switch (self::conf('APP_MODE')) {
            case 'DEV':
                error_reporting(E_ALL | E_STRICT);
                function LiberHandler() {
                    echo Liber::loadClass('Log', true)->toPopUp( func_get_args() );  
                    die();
                } 
                set_error_handler("LiberHandler");
                set_exception_handler('LiberHandler');
            break;
            
            case 'PROD':
                error_reporting(-1);    
                function LiberHandler() {
                    $args = func_get_args();
                    unset($args[4]);
                    Liber::loadClass('Log', true)->add( "\n".implode("\n",  $args) );
                    Liber::loadController(Liber::conf('SYS_ERROR'), true)->index();
                    die();
                }
                set_error_handler("LiberHandler");
                set_exception_handler('LiberHandler');
            break;
        }
        
        self::loadClass('Input');
        self::processRoute();
    }


    /**
    *   Complete the basic configuration.
    *
    */
    public static function setup() {
        self::conf('APP_ROOT'  , dirname($_SERVER['SCRIPT_FILENAME']).'/');
        self::conf('APP_URL'   , ((self::isSSL())?'https':'http').'://'.$_SERVER['HTTP_HOST'].str_replace('//','/',  dirname($_SERVER['SCRIPT_NAME']).'/') );
    }

    
    /**
    *   Load and set application configurations.
    *   @param $aConfig Array - configurations params.
    */
    public static function loadConfig( $aConfig ) {
        // set config values
        self::$aConfig = array_merge(self::$aConfig,  $aConfig['configs']);
        
        self::$aDbConfig  = &$aConfig['dbconfig'];
        self::$aRoute     = &$aConfig['routes'];
        self::setup();
    }


    /**
    *   Set/add config params.
    *   Can be added new params if necessary.
    *   @param $p String - param name
    *   @param $v String - param value
    *   @return mixed
    */
    public static function conf($p, $v=null) {
        if (  $v === NULL ) {
            return isset(self::$aConfig[$p])?self::$aConfig[$p]:null;
        } 
        elseif( empty ($v) ) {
            self::$aConfig[$p] = NULL;
        } else {
            self::$aConfig[$p] = $v;
        }
    }

        
    /**
     * Imports the definition of class(es) and tries to create an object/a list of objects from the class.
     * 
     * @param string $className Name of the class to be imported
     * @param string $path Path to the class file
     * @param bool $createObj Determined whether to create object(s) of the class
     * @return mixed returns true|false by default. If $createObj is TRUE, it creates and return the Object of the class name passed in.
     */
    public static function load($className, $path, $createObj=FALSE){
        $ret = true;
        if ( !class_exists($className)  ) {
            if ( file_exists($path . $className.'.php') ) {
                $ret =  (include $path . $className.'.php')!=1?false:true;
            } else {
                $ret = false;
            }
	    }

        if ( $createObj ) {
            return new $className;
        }
        return $ret;
    }
    
    
    /**
    *   Detect and return the current configuration about which context should be loaded.
    *   @param $context string - Module Name
    *   @param $create boolean - if it must return a object
    *   @return Array
    */
    protected static function prepareLoad($context, $create) {
        $out = Array('context'=>$context, 'create'=>$create);
        if ( is_bool($context) ) {
            $out['create'] = $context;
            $out['context'] = Liber::conf('BASE_PATH');
        } elseif ( $context == 'APP' ) {
            $out['context'] = Liber::conf('APP_PATH');
        } else {
            $out['context'] = Liber::conf('APP_PATH').'module/'.$context.'/';
        }
        return $out;
    }


    /**
    *   Import files of context.
    *   Context can be a Module path or APP_PATH (default).
    *   @param  $className string
    *   @param  $contextORcreateObj mixed - name of module or boolean 
    *   @param  $createObj boolean
    *   @return mixed - object or boolean
    */
    protected static function loadContext($className, $contextORcreateObj='', $createObj=false) {
        $path = Liber::conf('APP_PATH');    
        if ( is_bool($contextORcreateObj) ) {
            $createObj = $contextORcreateObj;
        } else {
            if ( !empty($contextORcreateObj) ) {
                $path = (($contextORcreateObj[0]=='/')?'':$path).$contextORcreateObj;
            }            
        }
        return self::load($className, $path, $createObj);
    }


    /**
    *   Imports the definition of Controller class. Class file is located at <b>APP_PATH/controller/</b> or <b>APP_PATH/ModuleName/controller/</b>
    *   @param  $cName string
    *   @param  $context  - name of module or boolean
    *   @param  $createObj boolean
    *   @return mixed - object or boolean
    */
    public static function loadController( $cName, $context='', $createObj=false ) {
        if ( is_bool($context) ) {
            $createObj = $context;
            $context   = 'controller/';
        } elseif ( empty($context) ) {
            $context = 'controller/';
        } else {
            $context = ($context[0] == '/'?$context.'controller/':'module/'.$context.'/controller/');            
        }
        return self::loadContext($cName, $context, $createObj);
    }

    /**
    *   Imports the definition of Model class. Class file is located at <b>APP_PATH/model/</b> or <b>APP_PATH/ModuleName/model/b>
    *   @param  $modelName string
    *   @param  $context  - name of module or boolean
    *   @param  $createObj boolean
    *   @return mixed - object or boolean
    */
    public static function loadModel( $modelName, $context='', $createObj=false ) {
        if ( is_bool($context) ) {
            $createObj = $context;
            $context = 'model/';
        } elseif ( empty($context) ) {
            $context = 'model/';
        } else {
            $context = 'module/'.$context.'/model/';            
        }
        return self::loadContext($modelName, $context, $createObj);
    }
        
    /**
    *   Imports the definition of one Class. Class file is located at <b>BASE_PATH/class/</b> or <b>$context/class/</b> 
    *   @param  $className string
    *   @param  $context  - name of module or boolean
    *   @param  $createObj boolean
    *   @return mixed - object or boolean
    */
    public static function loadClass( $className, $context=false, $createObj=false ) {
        $out        = self::prepareLoad($context, $createObj);
        $context    = &$out['context'];
        $createObj  = &$out['create'];
        return self::load($className, $context.'class/', $createObj);
    }

    /**
    *   Imports the definition of functions Helper file. File is located at <b>BASE_PATH/helper/</b>  or <b>$context/helper/</b>
    *   @param  $helperName string
    *   @param  $context  - name of module or boolean
    *   @param  $createObj boolean
    *   @return mixed - object or boolean
    */
    public static function loadHelper( $helperName, $context=false) {
        static $loaded = Array();
        // avoid to include already included file
        $i = $context.'helper/'.$helperName; 
        if ( !isset($loaded[$i]) ) {
            $out        = self::prepareLoad($context, false);
            $loaded[$i] = true;
            return self::load($helperName, $out['context'].'helper/'); // Class not exists, so don't need to create
        } 
    }

    /**
    *   Imports the definition of Plugin class. Class file is located at <b>BASE_PATH/plugin/</b>  or <b>$context/plugin/</b>
    *   @param  $pluginName string
    *   @param  $context  - name of module or boolean
    *   @param  $createObj boolean
    *   @return mixed - object or boolean
    */
    public static function loadPlugin( $pluginName, $context=false, $createObj=false ) {
        $out        = self::prepareLoad($context, $createObj);
        $context    = &$out['context'];
        $createObj  = &$out['create'];
        return self::load($pluginName, $context.'plugin/', $createObj);
    }

    /**
    *   Get named params from uri.
    *   @param $rule Array    
    *   @param $data Array   
    *   @return Array - Array('paramName' => 'value')
    */        
    public static function getParams($rule, $data) {
        // remove empty items
        $ruleItems = array_filter( explode('/',$rule) );
        $dataItems = array_filter( explode('/',$data) );

        $params = array();

        foreach($ruleItems as $ruleKey => $ruleValue) {
            $i = strpos($ruleValue, ':');
            if  ( $i === false ) { continue; }
            $size = strlen($ruleValue);

            // check if the chunk is a key
            if (preg_match('/(:[a-zA-Z0-9]+:)/',$ruleValue)) {
                $f = strpos($ruleValue, ':', $i+1);
                if ( $i === 0 ) {// start at begin
                    // ended before end
                    if ( $f < $size-1 ) {
                        $te = strlen($dataItems[$ruleKey]) - ($size - $f-1);
                        $params[substr($ruleValue, $i+1, $f-$i-1)] = substr($dataItems[$ruleKey],0,$te);
                    } else {
                        $params[substr($ruleValue, $i+1, $f-1)] = $dataItems[$ruleKey];;
                    }
                    
                } else { //when not start at begin
                    // ended before end
                    if ( $f < $size-1 ) {
                        $te = strlen($dataItems[$ruleKey])-($i+($size-($f+1)));
                        $params[substr($ruleValue, $i+1, $f-$i-1)] = substr($dataItems[$ruleKey],$i, $te);;
                    } else {
                        $params[substr($ruleValue, $i+1, $f-1)] = substr($dataItems[$ruleKey],$i);;
                    }
                }
            }
        }
        return $params;
    }

    /**
    *   Search for configured route and params returning it.
    *   @param $aRoute Array
    *   @param $uri string
    *   @return boolean
    */        
    public static function parseRouteParams($aRoute, $uri) {
        foreach ( $aRoute as $km => $vm ) {
            if ( strpos($km, ':')===false ) { continue ;}
            if (  substr_count($km, '/') != substr_count($uri, '/') )  { continue; }
            $regex = preg_replace('/(:[a-zA-Z0-9]+:)/', '(.+)', $km);
            $regex = str_replace('/','\/', $regex);
            // there are some route that match ?
            if ( preg_match('/('.$regex.')/', $uri) ) {
                $out['route'] = $km;
                $out['params'] = self::getParams($km, $uri) ; 
                return $out;
            }
        }
        return false;    
    }

    /**
    *   Redirect client to another URL
    *   @param $url string
    *   @param $return boolean - if must return a url string instead of redirect client
    *   @return string
    */
    public static function redirect($url, $return=false) {
        if ( $url[0] == '/' ) {
            $url[0] = ' ';
            $url = Liber::conf('APP_URL').trim($url);
        } 
        if ($return) { return $url; }
        header("Location: $url");
        exit;
    }
    
    /**
    *   Return the requested method.
    *   @return string
    */
    public static function requestedMethod() {
        static $method;
        if  (empty($method)) {
            $method = strtolower($_SERVER['REQUEST_METHOD']);
        }
        return $method;
    }
    
    /**
    *   Detect sort of configuration from route.  
    *   If Array return it, if string, it might be a re-routing or redirect to
    *   @param $conf 
    *   @return Array
    */
    static function getRouteConf( $conf ) {

        if ( is_string($conf) ) {
            // re-routing
            if ( $conf[0]=='/' )  { 
                $option = self::getRouteMethod($conf);
                if ( is_array($option)  ) {
                    return $option ;
                } elseif ( is_string($option) ) {
                    return self::getRouteConf($option);
                }
                
            } else {
                Liber::redirect($conf);
            }
        } else {

            // normalize to 3 
            if ( count($conf) == 2 ) {
                $conf[] = '';
            }
        } 
        return $conf;
    }
 
 
    /**
    *   Detects and returns a route options from a specified route, among methods os *.
    *   @param $route
    *   @return mixed - Array | string or false if don't find.
    */
    protected static function getRouteMethod($route) {
        return isset(Liber::$aRoute[$route]['*'])?Liber::$aRoute[$route]['*']:  (isset(Liber::$aRoute[$route][self::requestedMethod()])?Liber::$aRoute[$route][self::requestedMethod()]:false);    
    }
    
    /**
    *   Process route, match and create related controller or redirect to default controller if don't match.
    */
    public static function processRoute() {
        $aRoute = &Liber::$aRoute;
        $uri    = $_SERVER['REQUEST_URI'];

        // detect subfolder uses
        if ( $_SERVER['SCRIPT_NAME'] != '/index.php' )  {
            if ( strpos($uri, 'index.php')!==false ) { // with index.php
                $uri = '/'.str_replace($_SERVER['SCRIPT_NAME'], '',  $uri);    
            } else { // without index.php
                $uri = '/'.str_replace(str_replace('index.php', '', $_SERVER['SCRIPT_NAME']), '',$uri );
            }
        }

        // cleaning uri 
        //
        if ( $_SERVER['QUERY_STRING'] != ''  )  { // detect query string
            $uri = substr($uri, 0, strpos($uri, '?'));
        }
        if ( $uri[strlen($uri)-1] == '/' and strlen($uri) > 1 ) {
            $uri[strlen($uri)-1] = ' ';
            $uri = rtrim($uri);                
        }
        $uri = str_replace('index.php','', $uri);
        $uri = str_replace('//','/', $uri);



        // Direct match, load pre-configured route (the fast way, recommended)
        $routeOption =  self::getRouteMethod($uri);
        if ( $routeOption ) { 
            $routeConf = Liber::getRouteConf( $routeOption ); 
            $c = $routeConf[0];
            $m = $routeConf[2];

            if ( Liber::loadController($c, $m) ) {
                $a = $routeConf[1]=='*'?'index':$routeConf[1];
            } else {
                Liber::loadController( Liber::conf('PAGE_NOT_FOUND') );
                $c = Liber::conf('PAGE_NOT_FOUND');
                $a = 'index';
            }

            $p = false;
        } else { 
            // trying to guess route - /controller/method/param1/param2...
            $aUri = explode('/', $uri);
            $aUri = array_filter($aUri); // remove empty values
            $seg1 = ucfirst(current($aUri));
            
            // try if match a previous segment
            $previousSegment = dirname($uri);
            if ( isset($aRoute[$previousSegment]) )  {
                $routeOption =  self::getRouteMethod($previousSegment);
                $routeConf = Liber::getRouteConf( $routeOption ); 
            } else {
                $routeConf = Array('','','');
            }
            $m = false;
            
            // detect '*' for method name
            if ( is_array($routeConf) and $routeConf[1] == '*' ) { 
                $c = &$routeConf[0];
                $m = &$routeConf[2];
                $a = basename($uri); 
                $p = false;
                Liber::loadController($c, $m); 
            } elseif ( Liber::loadController($seg1, $m) ) { // Controller exists 
                $c  = &$seg1;
                $oC = new $c;
                $a  = next($aUri);
                if ( !method_exists($oC, $a) ) {
                    prev($aUri);
                    $a = 'index';
                }
                $p = array_slice($aUri, key($aUri) );
                $m = false;
            } else {
                // trying routes with named params
                if ( $aParsed = self::parseRouteParams($aRoute, $uri) ) {
                   $routeOption =  self::getRouteMethod($aParsed['route']);
                   $routeConf   = Liber::getRouteConf( $routeOption ); 
                   $c = $routeConf[0];
                   $a = $routeConf[1];       
                   $m = $routeConf[2];                   
                   $p = $aParsed['params'];  

                   Liber::loadController($c, $m);                   
                } else {
                    Liber::loadController( Liber::conf('PAGE_NOT_FOUND') );
                    $c = Liber::conf('PAGE_NOT_FOUND');
                    $a = 'index';
                    $p = false;
                    $m = false;
                }
            }
        }

        // get instance kind of Controller and call method (action).
        self::$_controller = new $c( Array('module'=>$m, 'params'=>$p) );

        if ( empty($a) or !method_exists(self::$_controller, $a) ) {
            self::$_controller->index();        
        } else {
            self::$_controller->$a();
        }
    }

    /**
    *   Return current Controller instance.
    *   @return Controller
    */
    public static function controller() {
        return self::$_controller;
    }

    /**
    *   Check if the request is an AJAX request usually sent with JS library such as JQuery/YUI/MooTools
    *   @return bool
    */
    public static function isAjax(){
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
    }

    /**
    *  Check if the connection is a SSL connection
    *  @return bool determined if it is a SSL connection
    */
    public static function isSSL(){
        if(!isset($_SERVER['HTTPS']))
            return FALSE;
            
        //Apache
        if($_SERVER['HTTPS'] === 1) {
            return TRUE;
        }
        //IIS
        elseif ($_SERVER['HTTPS'] === 'on') {
            return TRUE;
        }
        //other servers
        elseif ($_SERVER['SERVER_PORT'] == 443){
            return TRUE;
        }
        return FALSE;
    }
}


/**
*   Class that manipulates Controllers, its creation can be relative a module.
*   All controllers must extends it and have at least the 'index' method.
*/
class Controller {

    /**
    *   Module name
    *   @var string
    */
    private $module;

    /**
    *   Params detected from uri.
    *   @var Array
    */
    private $params;
    
    /**
    *   View instance
    *   @var View
    */
    private $_view;


    /**
    *   Constructor.
    *   @param $args array
    *   $args values are:   ['module'] = 'module name' - empty for application controller
    *                       ['params'] = Array() - values of detected named params from route
    */    
    public function __construct( $args=Array('module'=>'','params'=>Array()) ) {
        $this->module = isset($args['module'])?$args['module']:'';
        $this->params = isset($args['params'])?$args['params']:Array();
        header('Content-Type: text/html; charset=utf-8'); // default values
    }

    /**
    *   Set http headers to send. See header function on PHP manual.
    *   @param $header String
    *
    */
    public function header($header=null) {
        header($header);
    }

    /**
    *   Load model from application or module if was instanced with it.
    *   @param $modelName string  - Model name
    *   @param $createObj boolean - if must create a object 
    *   @return mixed - Model object or boolean 
    */
    public function loadModel( $modelName, $createObj=false  ) {
        $module = !empty($this->module)?$this->module:'model/'; 
        return Liber::loadModel($modelName, $this->module, $createObj);
    }
    
    /**
    *   Load Class from application or module if was instanced with it.
    *   @param $className string  - Model name
    *   @param $createObj boolean - if must create a object 
    *   @return mixed - Class object or boolean     
    */
    public function loadClass( $className, $createObj=false  ) {
        $module = !empty($this->module)?$this->module.'/':'class/';
        return Liber::loadClass($className, $module, $createObj);
    }

    /**
    *   Load helper from application or module if was instanced with it.
    *   @param $helperName string  - Helper name
    *   @param $createObj boolean - if must create a object 
    *   @return mixed - Helper object or boolean     
    */
    public function loadHelper( $helperName, $createObj=false ) {
        $module = !empty($this->module)?$this->module:'';
        return Liber::loadHelper($helperName, $module, $createObj);
    }

    /**
    *   Load plugin from application or module if was instanced with it.
    *   @param $pluginName string  - Plugin name
    *   @param $createObj boolean - if must create a object 
    *   @return mixed - Plugin object or boolean     
    */
    public function loadPlugin( $pluginName, $createObj=false ) {
        $module = !empty($this->module)?$this->module:'plugin/';    
        return Liber::loadPlugin($pluginName, $module, $createObj);    
    }

    /**
    *   Get instance of View class.
    *   @param $layout_once string - Name of layout that this call will use
    *   @return View object
    */
    public function view($layout_once='') {
        if ( $this->_view == NULL ) {
            Liber::loadClass('View');
            $this->_view = new View(Array('module'=>$this->module));
        }
        $this->_view->setLayoutOnce($layout_once);
        return $this->_view;
    }

    /**
    *   Get named or unamed params from uri.
    *   @param $name string - Param name
    *   @return string
    */
    public function params( $name ) {
        return isset($this->params[$name])?urldecode($this->params[$name]):'';
    }
}

?>
