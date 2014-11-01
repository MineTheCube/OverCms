<?php

$html = '';

$html .= file_get_contents(THEMES . THEME . '/views/includes/' . 'overall' . EXT);
$html .= file_get_contents(THEMES . THEME . '/views/includes/' . 'navbar' . EXT);

$html .= '%HEADER%';

if ( FULLWIDE !== true)
    $html .= file_get_contents(THEMES . THEME . '/views/includes/' . 'header' . EXT);
    
if ( WIDE !== true and FULLWIDE !== true)
    $html .= file_get_contents(THEMES . THEME . '/views/includes/' . 'body-start' . EXT);

$html .= $content;

if ( WIDE !== true and FULLWIDE !== true)
    $html .= file_get_contents(THEMES . THEME . '/views/includes/' . 'body-end' . EXT);

if ( FULLWIDE !== true)
    $html .= file_get_contents(THEMES . THEME . '/views/includes/' . 'sidebar' . EXT);

$html .= file_get_contents(THEMES . THEME . '/views/includes/' . 'footer' . EXT);

echo $this->render($html, $page);
