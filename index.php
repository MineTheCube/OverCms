<?php

/*
 *
 *    OverCmsOv                                              sOverCms                                    
 *   CmsOverCmsO  rC        sO   CmsOverCmsO  rCmsOverCm    rCmsOverCm   erCmsOverCmsOve  msOverCmsOve   
 *  ver       Cms ve        Cm  ver           ver     erC  Over          Ove   sOv    sO  rC             
 *  sO        erC sO        er  sOverCmsOve   sO       v   ms            ms     ms    Cm  verCmsOver     
 *  Cm        Ove Cm        Ov  CmsOverCmsOv  Cm           rC            rC     rC    er   OverCmsOver   
 *  erC       msO  er      ms   erC           er           ver           ve     ve    Ov            Ov   
 *  OverCmsOverC    rCmsOve      verCmsOver   Ov            OverCmsOve   sO     sO    ms  erCmsOverCms   
 *   sOverCmsOv       rCm         OverCmsOve  ms             sOverCms    Cm     Cm    rC  OverCmsOver    
 *
 *
 */



/*
 * --------------------------------
 *           Let's begin
 * --------------------------------
 *
 */
$timestart = microtime(true);
header('Content-Type: text/html; charset=utf-8');
@set_include_path(dirname(__FILE__));


/*
 * --------------------------------
 *        Define Constants
 * --------------------------------
 *
 */
// HTTP
define('URL', (isset($_SERVER['https']) ? 'https' : 'http') . '://' . (empty($_SERVER['HTTP_HOST']) ? $_SERVER['SERVER_NAME'] : $_SERVER['HTTP_HOST']));
$root = substr(dirname($_SERVER['PHP_SELF']), 1) == '' ? '' : substr(dirname($_SERVER['PHP_SELF']), 1).'/';
define('ROOT', (strpos($root, "index.php/") !== false ? substr($root, 0, strpos($root, "index.php/")) : $root));
define('HTTP_ROOT', '/' . ROOT);
define('PHP_ROOT', dirname(__FILE__) . (substr(dirname(__FILE__), -1) === '/' ? '' : '/'));
define('IN_ENV', true);
// Paths
define('APP', 'app/');
define('CORE', APP . 'core/');
define('DATA', APP . 'data/');
define('CONTROLLER', APP . 'controller/');
define('MODEL', APP . 'model/');
define('PACKAGE', APP . 'package/');
define('VIEW', APP . 'view/');
define('LANG', APP . 'lang/');
define('PLUGIN', 'plugin/');
define('FILES', 'files/');
define('AVATAR', FILES . 'user/avatar/');
define('INSTALL', 'install/');
define('TEMPLATE', 'template/');
define('ADMIN', 'admin/');
// Shortcuts
define('EX', '.php');
define('EXC', '.class.php');
define('EXF', '.function.php');
define('EXT', '.htm');
define('CONFIG', 'config.cfg.php');
// Unique Key
define('UNIQUE_KEY', md5($_SERVER['GATEWAY_INTERFACE'].$_SERVER['SERVER_ADDR'].$_SERVER['SERVER_SOFTWARE'].'#%*'));


/*
 * --------------------------------
 *       Remove Magic Quotes
 * --------------------------------
 *
 */
if (function_exists('get_magic_quotes_gpc') and get_magic_quotes_gpc()) {
    $process = array(&$_GET, &$_POST, &$_COOKIE);
    while (list($key, $val) = each($process)) {
        foreach ($val as $k => $v) {
            unset($process[$key][$k]);
            if (is_array($v)) {
                $process[$key][stripslashes($k)] = $v;
                $process[] = &$process[$key][stripslashes($k)];
            } else {
                $process[$key][stripslashes($k)] = stripslashes($v);
            }
        }
    }
    unset($process, $key, $val, $k, $v);
}


/*
 * --------------------------------
 *         Initialization
 * --------------------------------
 *
 */
require_once ( MODEL . 'helpers' . EXF );
require_once ( MODEL . 'config' . EXC );
$config = new Config;
if ($config->get('user.settings.debug', false)) {
    define('DEBUG', true);
    error_reporting(E_ALL & ~E_NOTICE);
    @ini_set('display_errors', 1);
} else {
    define('DEBUG', false);
    error_reporting(0);
    @ini_set('display_errors', 0);
}
define('DATABASE', $config->get('database.type', 'sqlite'));
define('LANGUAGE', $config->get('user.settings.language', 'en_US'));
define('CURRENT_TEMPLATE', str_replace(' ', '_', $config->get('user.settings.template')));
define('DATE_FORMAT', $config->get('user.settings.date_format', 'd/m/Y \- H:i'));
date_default_timezone_set($config->get('user.settings.timezone', 'Europe/London'));
$urlrewrite = $config->get('user.settings.urlrewrite', false);
if ($urlrewrite) {
    define('URL_REWRITE', true);
    define('ABS_ROOT', '/' . ROOT);
} else {
    define('URL_REWRITE', false);
    define('ABS_ROOT', '/?page=' . ROOT);
}


/*
 * --------------------------------
 *            Init App
 * --------------------------------
 *
 */
require_once (CORE . 'app' . EXC);
$app = new App;
$app->init();
$app->loadModels();
$app->refreshPlugins();


/*
 * --------------------------------
 *          Init Models
 * --------------------------------
 *
 */
$session = new Session;
$session->start();
Translate::addPath(LANG);


/*
 * --------------------------------
 *         Analyse request
 * --------------------------------
 *
 */
define('AJAX', $_POST['ajax'] === "1");
if (strtoupper($_SERVER['REQUEST_METHOD']) === 'POST') {
    define('POST_METHOD', ((ctype_alnum($_POST['method']) and !empty($_POST['method'])) ? $_POST['method'] : false));
    if ($app->checkToken()) {
        define('POST', true);
        define('GET', false);
    } else {
        define('POST', false);
        define('GET', true);
        define('POST_INVALID_TOKEN', true);
        respond(false, 'INVALID_TOKEN', '$');
    }
} else {
    define('POST', false);
    define('POST_METHOD', false);
    define('GET', true);
}


   /***********************/
  /***/ $app->router(); /***/
   /***********************/


