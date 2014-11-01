<?php

$html = file_get_contents(THEMES . THEME . '/views/login/login' . EXT);

$matchError = $tools->textBetween($html, '%ALERT_ERROR%');
$alertError = $matchError[1];

$html = str_replace('%TOKEN%', $token, $html);

if ( !empty( $error ) ) {
    $html = str_replace( $matchError[0], str_replace( '%MESSAGE%', $error, $alertError ) , $html );
} else {
    $html = str_replace( $matchError[0], '' , $html );
}