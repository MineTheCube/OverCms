<?php

$html = file_get_contents(THEMES . CURRENT_THEME . '/views/register/register' . EXT);

$matchError = $tools->textBetween($html, '%ALERT_ERROR%');
$alertError = $matchError[1];

$matchSuccess = $tools->textBetween($html, '%ALERT_SUCCESS%');
$alertSuccess = $matchSuccess[1];

$html = str_replace('%TOKEN%', $token, $html);
$html = str_replace('%CAPTCHA%', $captchaImage, $html);

if ( !empty( $error ) ) {
    $html = str_replace( $matchError[0], str_replace( '%MESSAGE%', $error, $alertError ) , $html );
    $html = str_replace( $matchSuccess[0], '', $html );
    $html = str_replace( '%HIDE_WHEN_SUCCESS%', '' , $html );
} else if ( !empty( $success ) ) {
    $html = str_replace( $matchSuccess[0], str_replace( '%MESSAGE%', $success, $alertSuccess ) , $html );
    $html = str_replace( $matchError[0], '', $html );
    $html = str_replace ( '<input ', '<input disabled="disabled" ', $html );
    $match = $tools->textBetween($html, '%HIDE_WHEN_SUCCESS%');
    $html = str_replace( $match[0], '', $html );
} else {
    $html = str_replace( $matchError[0], '', $html );
    $html = str_replace( $matchSuccess[0], '', $html );
    $html = str_replace( '%HIDE_WHEN_SUCCESS%', '' , $html );
}
