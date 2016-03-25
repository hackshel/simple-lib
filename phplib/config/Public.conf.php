<?php
/**
 * @file Public.conf.php
 * @date 2010/01/01 13:22:19
 * @version $Revision: 1.4 $
 *
 **/

require_once('IDC.conf.php');

define('PROCESS_START_TIME', microtime(true)*1000);

define('SSL_BYPASS_PORT' , 8143);


define('STATIC_DOMAIN', 'apps.hximg.com');
define('PASSPORT_DOMAIN', 'https://passport.hxfilm.com');

//define('HX_COOKIE_DOMAIN', '.hxfilm.com');
define('HXSTOKEN_KEY', 'developer@hxfilm!');


define('HTDOCS_PATH', dirname(__FILE__) .'/../../');

define('LOG_PATH', HTDOCS_PATH .'../../logs');

define('PUBLIC_PATH', HTDOCS_PATH .'/phplib');
define('SMARTY_PATH', HTDOCS_PATH .'/extlib/smarty/libs');

define('PUBLIC_CONF_PATH', HTDOCS_PATH .'/conf/phplib');

#echo PUBLIC_CONF_PATH."\n";

define('PHPUI_CONF_PATH', '../');

//require_once(SMARTY_PATH .'/Smarty.class.php');

define('TEMPLATE_PATH', HTDOCS_PATH .'/templates');
define('SMARTY_TEMPLATE_DIR', TEMPLATE_PATH .'/templates');
define('SMARTY_COMPILE_DIR', TEMPLATE_PATH .'/templates_c');
define('SMARTY_CONFIG_DIR', TEMPLATE_PATH .'/config');
define('SMARTY_CACHE_DIR', TEMPLATE_PATH .'/cache');
define('SMARTY_PLUGIN_DIR', TEMPLATE_PATH .'/plugins');

#print_r( SMARTY_TEMPLATE_DIR );


define('FONT_TYPE_PATH', HTDOCS_PATH . '/../lib/fonttype/');

class PublicLibManager
{
    private $arrClasses;

    private static $instance;

    public static function getInstance()
    {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function __construct()
    {
        $this->arrClasses = array(
        'Action'                => PUBLIC_PATH .'/framework/Action.class.php',
        'ActionChain'            => PUBLIC_PATH .'/framework/ActionChain.class.php',
        'ActionController'        => PUBLIC_PATH .'/framework/ActionController.class.php',
        'ActionControllerConfig'=> PUBLIC_PATH .'/framework/ActionControllerConfig.class.php',
        'Context'                => PUBLIC_PATH .'/framework/Context.class.php',
        'Application'            => PUBLIC_PATH .'/framework/Application.class.php',


        'DBProxy'            => PUBLIC_PATH .'/DBProxy/DBProxy.class.php',
        'DBProxyWrapper'    => PUBLIC_PATH .'/DBProxy/DBProxyWrapper.class.php',
        'DBProxyConfig'        => PUBLIC_CONF_PATH .'/DBProxyConfig.class.php',
        #'bdDBProxyConfig'    => PHPUI_CONF_PATH .'/bdDBProxyConfig.class.php',

        'CLog'              => PUBLIC_PATH .'/utils/CLog.class.php',
        'Utils'                => PUBLIC_PATH .'/utils/Utils.class.php',
        'Smtp'                => PUBLIC_PATH .'/utils/Smtp.class.php',
        'CustomObject'        => PUBLIC_PATH .'/utils/CustomObject.class.php',
        'ResourceFactory'    => PUBLIC_PATH .'/utils/ResourceFactory.class.php',

        'Smarty'            => SMARTY_PATH .'/Smarty.class.php',

        'DevPassport'        => PUBLIC_PATH .'/passport/DevPassport.class.php',
        'Passport'            => PUBLIC_PATH .'/passport/Passport.php',
        'Bd_Passport_Inc'   => PUBLIC_CONF_PATH .'/Bd_Passport_Inc.class.php',

        'CommonConst'        => PUBLIC_PATH .'/common/CommonConst.class.php',

        'bdUcrypt'            => PUBLIC_PATH .'/bdUcrypt/bdUcrypt.class.php',


        'DevPassport'        => PUBLIC_PATH .'/passport/DevPassport.class.php',
        'Passport'          => PUBLIC_PATH .'/passport/Passport.php',

        'Bd_Passport_Inc'   => PUBLIC_CONF_PATH .'/Bd_Passport_Inc.class.php', 
        'Bd_Passport_Util'  => PUBLIC_PATH .'/passport/Util.php',
        'PPSApi'            => PUBLIC_PATH .'/passport/SAPI/PPSApi.class.php',
        'PPSApiConfig'      => PUBLIC_CONF_PATH .'/Bd_Passport_Inc.class.php',
        'PPNormalizeSApi'   => PUBLIC_PATH .'/passport/SAPI/PPNormalizeSApi.class.php',
        'IDCard'            => PUBLIC_PATH .'/IDCard/IDCard.class.php',
        'IDCardConfig'        => PUBLIC_CONF_PATH .'/IDCardConfig.class.php',


        'hxMemcache'        => PUBLIC_PATH .'/hxMemcache/hxMemcache.class.php',
        'hxMemcacheConfig'    => PUBLIC_CONF_PATH .'/hxMemcacheConfig.class.php',


        );
    }

    public function getPublicClassNames()
    {
        return $this->arrClasses;
    }

    public function RegisterMyClassName($className, $classPath)
    {
        $this->arrClasses[$className] = $classPath;
    }

    public function RegisterMyClasses(array $classes)
    {
        $this->arrClasses = array_merge($this->arrClasses, $classes);
    }
}

/**
 * Register user defined class into phplib's autoloader
 * @param string $className    Name of user defined class
 * @param string $classPath    File path of user defined class
 */
function RegisterMyClassName($className, $classPath)
{
    $PublicClassName = PublicLibManager::getInstance();
    $PublicClassName->RegisterMyClassName($className, $classPath);
}

/**
 * Register User defined classes into phplib's autoloader
 * @param array $classes    Class infos, use format: array(classname => class file path, ...)
 */
function RegisterMyClasses(array $classes)
{
    $PublicClassName = PublicLibManager::getInstance();
    $PublicClassName->RegisterMyClasses($classes);
}

function PublicLibAutoLoader($className)
{
    $PublicClassName = PublicLibManager::getInstance();
    $arrPublicClassName = $PublicClassName->getPublicClassNames();
    if (array_key_exists($className, $arrPublicClassName)) {
        require_once($arrPublicClassName[$className]);
    } else {
        $classFile = $className .'.class.php';
        //如果有多个autoloader的话，这个地方会报很多的warning�?
        //所以不直接用include_once，需要改写include_path的作用逻辑�?
        //对性能会有少许的影响�?
        //不能error_reporting(0),会使类文件里的语法等错误无法报出，影响排错�?
        $include_path = explode(':', get_include_path());
        foreach ($include_path as $path_dir) {
            $real_path = rtrim($path_dir, '/') . '/' .$classFile;
            if (file_exists($real_path)) {
                require_once($real_path);
            }
        }
    }
}

spl_autoload_register('PublicLibAutoLoader');
