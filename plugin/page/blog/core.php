<?php

Translate::addPath( PATH_PLUGIN . 'lang/', 'en_US' );

require_once PATH_PLUGIN . 'model/blog' . EXC;
$blog = new Blog;

$req = explode('/', $request['args']);

if ( empty( $req[0] ) or $req[0] == 'page' ) {
    $controller = 'blog-list.php';
} else if ( $req[0] == 'panel' ) {
    $controller = 'panel.php';
} else if ( is_numeric($req[0]) ) {
    $controller = 'blog-single.php';
} else {
    $this->go( $request['current'] );
}

$template = PATH_PLUGIN . '/view/';
$pluginConfig = new Config( PATH_PLUGIN . 'config.cfg.php' );
$themePlugins = glob(THEMES . THEME . '/plugin/*', GLOB_ONLYDIR | GLOB_MARK );
foreach($themePlugins as $themePlugin) {
    if (file_exists( $themePlugin . 'config.cfg.php')) {
        $themeConfig = new Config( $themePlugin . 'config.cfg.php' );
        if ( $pluginConfig->get('plugin->name') == 'blog' and version_compare( $pluginConfig->get('plugin->require->theme'), $themeConfig->get('plugin->version'), "<=" ) ) {
            $template = $themePlugin . 'views/';
        }
    }
}

require_once PATH_PLUGIN . 'controller/' . $controller;
$content = $html;