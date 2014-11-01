<?php

$html = file_get_contents(THEMES . THEME . '/views/account/account' . EXT);

$html = str_replace( '%USER_AVATAR%', $user->get('picture') , $html );

$matchError = $tools->textBetween($html, '%ALERT_ERROR%');
$alertError = $matchError[1];

$matchSuccess = $tools->textBetween($html, '%ALERT_SUCCESS%');
$alertSuccess = $matchSuccess[1];

$html = str_replace('%TOKEN%', $token, $html);

if ( !empty( $error ) ) {
    $html = str_replace( $matchError[0], str_replace( '%MESSAGE%', $error, $alertError ) , $html );
    $html = str_replace( $matchSuccess[0], '' , $html );
} else if ( !empty($success) ) {
    $html = str_replace( $matchSuccess[0], str_replace( '%MESSAGE%', $success, $alertSuccess ) , $html );
    $html = str_replace( $matchError[0], '' , $html );
} else {
    $html = str_replace( $matchError[0], '' , $html );
    $html = str_replace( $matchSuccess[0], '' , $html );
}