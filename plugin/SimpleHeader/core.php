<?php

defined('IN_ENV') or die;

$desc = $page->get('desc');

$http_path = HTTP_ROOT . $template;

$theme_config = parse_ini_file($template . 'config.ini');
$min = (int) $theme_config['rand_min'];
$max = (int) $theme_config['rand_max'];

$style = Html::file($template.'style.css', array('PICTURE' => '"'.HTTP_ROOT.$template.'img/'.rand($min, $max).'.jpg"'));
Html::CSS($style);

$content = Html::file($template.'template.htm');
$content = strip_comments($content);

if (empty($desc))
    $content = str_remove_where($content, '%IF_DESC%');
else 
    $content = str_remove($content, '%IF_DESC%');


$content = str_replace('%DESC%', $desc, $content);

return $content;