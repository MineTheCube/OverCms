<?php

$html = file_get_contents(THEMES . CURRENT_THEME . '/views/member/member' . EXT);

$matchUser = $tools->textBetween($html, '%USER_EDIT%');
$userEdit = $matchUser[1];

if ($user->get('username') == $viewer->get('username')) {
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

$matchInfo = $tools->textBetween($html, '%ALERT_INFO%');
$alertInfo = $matchInfo[1];

$profil = $user->get('profil');
if (!empty($profil))
    $profil = json_decode($profil);
else
    $profil = new StdClass();
if (empty($profil->birthday))
    $profil->birthday = '{@UNKNOW}';
else {
    $profil->birthday = str_replace('-', '/', $profil->birthday);
}
if (empty($profil->gender))
    $profil->gender = '{@UNKNOW}';
else
    $profil->gender = '{@' . strtoupper($profil->gender) . '}';
if (empty($profil->city))
    $profil->city = '{@UNKNOW}';
if (empty($profil->country))
    $profil->country = '{@UNKNOW}';
    
$html = str_replace( '%USERNAME%', $user->get('username'), $html );
$html = str_replace( '%DATE_CREATION%', $tools->reldate( $user->get('date_creation') ) , $html );
$html = str_replace( '%BIRTHDAY%', $profil->birthday, $html );
$html = str_replace( '%GENDER%', $profil->gender, $html );
$html = str_replace( '%CITY%', $profil->city, $html );
$html = str_replace( '%COUNTRY%', $profil->country, $html );

$html = str_replace('%TOKEN%', $token, $html);

if ($userAuth) {
    $remove = $tools->textBetween($html, '%USER_NOT_CONNECTED%');
    $html = str_replace($remove[0], '', $html);
} else {
    $remove = $tools->textBetween($html, '%USER_CONNECTED%');
    $html = str_replace($remove[0], '', $html);
}

// Comments
if ($userFound) {
    $html = str_replace( '%PROFIL_ID%', $user->get('id'), $html );
    $matchComment = $tools->textBetween($html, '%STATUS%', '%STATUS%');
    $commentHtml = $matchComment[1];
    $matchRemoveComment = $tools->textBetween($html, '%REMOVE_STATUS%', '%REMOVE_STATUS%');
    $removeComment = $matchRemoveComment[1];

    $comments = $user->getStatus( $user->get('id') );
    while($comment = $comments->fetch()) {
        $currentComment = $commentHtml;
        
        $commentAuthor = new User;
        $commentAuthor->setup( $comment['author_id'], 'id' );
        
        if ($viewer->get('permission') >= $page->get('p_edit') or $commentAuthor->get('id') === $viewer->get('id') or $viewer->get('id') === $user->get('id')) {
            $currentComment = str_replace( '%REMOVE_STATUS%', '', $currentComment );
            $currentComment = str_replace( '%STATUS_ID%', $comment['id'], $currentComment );
        } else {
            $currentComment = str_replace( $matchRemoveComment[0], '', $currentComment );
        }
        
        $currentComment = str_replace( '%STATUS_USER%', $commentAuthor->get('username'), $currentComment );
        $currentComment = str_replace( '%URL_USER_PICTURE%', $commentAuthor->get('picture'), $currentComment );
        $currentComment = str_replace( '%URL_USER%', '/' .REQUEST_CURRENT . '/' . $commentAuthor->get('username') . '/', $currentComment );
        $currentComment = str_replace( '%STATUS_CONTENT%', nl2br(htmlspecialchars( $comment['content'] )), $currentComment );
        $currentComment = str_replace( '%STATUS_DATE%', $tools->reldate($comment['date']), $currentComment );
        $displayComments .= $currentComment;
    }
    $html = str_replace( $matchComment[0], $displayComments, $html );
} else {
    $match = $tools->textBetween($html, '%NO_USER_FOUND%');
    $html = str_replace( $match[0], '', $html );
}

if ( !empty( $alert ) ) {
    $html = str_replace( $matchInfo[0], str_replace( '%MESSAGE%', $alert, $alertInfo ) , $html );
    $html = str_replace( $matchError[0], '' , $html );
    $html = str_replace( $matchSuccess[0], '' , $html );
} else if ( !empty( $error ) ) {
    $html = str_replace( $matchError[0], str_replace( '%MESSAGE%', $error, $alertError ) , $html );
    $html = str_replace( $matchSuccess[0], '' , $html );
    $html = str_replace( $matchInfo[0], '' , $html );
} else if ( !empty( $success ) ) {
    $html = str_replace( $matchSuccess[0], str_replace( '%MESSAGE%', $success, $alertSuccess ) , $html );
    $html = str_replace( $matchError[0], '' , $html );
    $html = str_replace( $matchInfo[0], '' , $html );
} else {
    $html = str_replace( $matchSuccess[0], '' , $html );
    $html = str_replace( $matchError[0], '' , $html );
    $html = str_replace( $matchInfo[0], '' , $html );
}