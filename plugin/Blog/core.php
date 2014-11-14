<?php

require_once $path . 'model/blog' . EXC;
$blog = new Blog;

$req = (strpos(REQUEST_ARGS, '/') == false ? REQUEST_ARGS : current(explode('/', REQUEST_ARGS)));

if ( empty( $req ) or $req == 'page' ) {
    $controller = 'blog-list.php';
    define( 'NOBODY', true);
} else if ( $req == 'panel' ) {
    $controller = 'panel.php';
} else if ( is_numeric($req) ) {
    $controller = 'blog-single.php';
    define( 'NOBODY', true);
} else {
    $this->go( REQUEST_CURRENT );
}

$app = new App;
$user = new User;
$user->setup();
$tools = new Tools;

if (empty($theme) or $theme === false)
    $theme = $path . 'template/';

require_once $path . 'controller/' . $controller;

$output = array(
    'content' => $html,
    'translation' => 'lang/',
    'default_language' => 'fr_FR'
);