<?php

$html = file_get_contents(THEMES . THEME . '/views/member/member' . EXT);

$matchUser = $tools->textBetween($html, '%USER_EDIT%');
$userEdit = $matchUser[1];

$current_user = new User;
$current_user->setup();

if ($user->get('username') == $current_user->get('username')) {
    $html = str_replace( $matchUser[0], $userEdit, $html );
} else {
    $html = str_replace( $matchUser[0], '', $html );
}

if ( strpos('%USER_LIST%', $html) !== false and false) {
    $userlist_req = $this->query('SELECT username FROM users WHERE 1');
    $userrows = array();
    while($row = $userlist_req->fetch() ) {
        $userrows[] = $row['username'];
    }
    $userlist = json_encode($userrows);
    $html = str_replace( '%USER_LIST%', $userlist, $html );
} else {
    $html = str_replace( '%USER_LIST%', '', $html );
}

$html = str_replace( '%USER_AVATAR%', $user->get('picture') , $html );

$matchError = $tools->textBetween($html, '%ALERT_ERROR%');
$alertError = $matchError[1];

$matchSuccess = $tools->textBetween($html, '%ALERT_SUCCESS%');
$alertSuccess = $matchSuccess[1];

$html = str_replace( '%USERNAME%', $user->get('username') , $html );
$html = str_replace( '%DATE_CREATION%', $tools->reldate( $user->get('date_creation') ) , $html );

$html = str_replace('%TOKEN%', $token, $html);

if ( !empty( $error ) ) {
    $html = str_replace( $matchError[0], str_replace( '%MESSAGE%', $error, $alertError ) , $html );
    $html = str_replace( $matchSuccess[0], '' , $html );
    $match = $tools->textBetween($html, '%HIDE_WHEN_ERROR%');
    $html = str_replace( $match[0], '', $html );
} else if ( !empty( $success ) ) {
    $html = str_replace( $matchSuccess[0], str_replace( '%MESSAGE%', $success, $alertSuccess ) , $html );
    $html = str_replace( $matchError[0], '' , $html );
    $html = str_replace( '%HIDE_WHEN_ERROR%', '' , $html );
} else {
    $html = str_replace( $matchSuccess[0], '' , $html );
    $html = str_replace( $matchError[0], '' , $html );
    $html = str_replace( '%HIDE_WHEN_ERROR%', '' , $html );
}