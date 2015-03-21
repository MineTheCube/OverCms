<?php

defined('IN_ENV') or die;

require_once $path . 'model/blog' . EXC;
$blog = new Blog;

$req = head(explode('/', REQUEST_ARGS));

if (empty($req) or $req == 'page') {
    $controller = 'blog-list.php';
} else if ($req == 'panel') {
    $controller = 'panel.php';
} else if (is_numeric($req)) {
    $controller = 'blog-single.php';
} else {
    go(REQUEST_CURRENT);
}

require_once $path . 'controller/' . $controller;

return $content;