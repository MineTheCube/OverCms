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


/* ============================== */
$timestart = microtime(true);
header ('Content-Type: text/html; charset=utf-8');
/* ============================== */


/* ============================== */
// Constants
define('URL', (isset($_SERVER['https']) ? 'https' : 'http') . '://' . $_SERVER['SERVER_NAME']);
$root = substr(dirname($_SERVER['PHP_SELF']), 1) == '' ? '' : substr(dirname($_SERVER['PHP_SELF']), 1).'/';
define('ROOT', (strpos($root, "index.php/") !== false ? substr($root, 0, strpos($root, "index.php/")) : $root));
define('HTTP_ROOT', '/' . ROOT);
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
define('THEMES', 'themes/');
// Shortcuts
define('EX', '.php');
define('EXC', '.class.php');
define('EXF', '.function.php');
define('EXT', '.htm');
define('CONFIG', 'config.cfg.php');
// Unique Key
define('UNIQUE_KEY', md5($_SERVER['GATEWAY_INTERFACE'].$_SERVER['SERVER_ADDR'].$_SERVER['SERVER_SOFTWARE'].'#%*'));
/* ============================== */


/* ============================== */
// Load config manually
require_once ( MODEL . 'config' . EXC );
$config = new Config;
if ($config->get('user->settings->debug', false)) {
    define('DEBUG', true);
    error_reporting(E_ALL & ~E_NOTICE);
} else {
    define('DEBUG', false);
    error_reporting(0);
}
// Define constants depending on config
define('DATABASE', $config->get('database->type', 'sqlite'));
define('LANGUAGE', $config->get('user->settings->language', 'en_US'));
define('CURRENT_THEME', $config->get('user->settings->theme', 'bootswatch'));
define('DATE_FORMAT', $config->get('user->settings->date_format', 'd/m/Y \- H:i'));
date_default_timezone_set($config->get('user->settings->timezone', 'Europe/London'));
$urlrewrite = $config->get('user->settings->urlrewrite');
if (!is_bool($urlrewrite)) $urlrewrite = false;
define('URL_REWRITE', $urlrewrite);
if (URL_REWRITE)
    define('ABS_ROOT', '/' . ROOT);
else
    define('ABS_ROOT', '/?page=' . ROOT);
/* ============================== */


/* ============================== */
// Load all models
require_once ( CORE . 'app' . EXC );
$app = new App;
$app->init();
$app->loadModels();
/* ============================== */


/* ============================== */
$session = new Session;
$session->start();
Translate::addPath(LANG);
/* ============================== */


   /***********************/
  /***/ $app->router(); /***/
   /***********************/

