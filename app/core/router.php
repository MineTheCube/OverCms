<?php

/*
 * ==============================
 * =           ROUTER           =
 * ==============================
 */

// INITIALIZE
/* ============================== */
$page = new Page;
/* ============================== */


// CHECK IF URL IS CORRECT
/* ============================== */
// Get requested url
if (URL_REWRITE)
    $request_uri = substr(urldecode($_SERVER['REQUEST_URI']), 1);
else
    $request_uri = $_GET['page'];
// Remove root from uri
if (ROOT) {
    $pos = strpos($request_uri, ROOT);
    if ($pos === 0) {
        $request_uri = substr($request_uri, strlen(ROOT));
    }
}
// Add trailing slash
if ( substr($request_uri, -1) != '/' && !empty($request_uri) ) {
    header('Location:' . $_SERVER['REQUEST_URI'] . '/' );
    exit();
}
$request_uri = substr( $request_uri, 0, -1);
/* ============================== */


// THROW EVENT
/* ============================== */
$event = new Event;
$event->request_uri = $request_uri;
$event->args = explode('/', $request_uri);
EventManager::fire('onNewRequest', $event);
if ($event->isCancelled()) {
    if (DEBUG)
        echo '<!-- Debug: Event onNewRequest cancelled -->';
    exit();
}
/* ============================== */


// GET PAGE AND ARGUMENTS
/* ============================== */
$url_parts = explode('/', $request_uri );
if ( $page->slugExist($url_parts[1], true) and $page->slugExist($url_parts[0], false, true) ) {
    $request['parent'] = $url_parts[0];
    $request['slug'] = $url_parts[1];
    unset($url_parts[0], $url_parts[1]);
    $request['args'] = implode('/', $url_parts);
    $request['current'] = $request['parent'] . '/' . $request['slug'];
} else if ( $page->slugExist($url_parts[0], false) ) {
    $request['parent'] = null;
    $request['slug'] = $url_parts[0];
    unset($url_parts[0]);
    $request['args'] = implode('/', $url_parts);
    $request['current'] = $request['slug'];
} else if ( empty($request_uri) and $page->getHomePage('slug') !== false ) {
    $request['parent'] = null;
    $request['slug'] = $page->getHomePage('slug');
    $request['args'] = null;
    $request['current'] = $request['slug'];
} else {
    $page_error = $page->getPage(array('type' => 'native', 'type_data' => 'error' ));
    go( $page_error['slug'] . '/404');
}
/* ============================== */


/* ============================== */
define('REQUEST_PARENT', $request['parent']);
define('REQUEST_SLUG', $request['slug']);
define('REQUEST_CURRENT', $request['current']);
define('REQUEST_ARGS', $request['args']);
/* ============================== */


   /*******************************/
  /***/ $this->start($request); /***/
   /******************************/

