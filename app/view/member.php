<?php

$content = file_get_contents(TEMPLATE . CURRENT_TEMPLATE . '/views/member' . EXT);
$content = strip_comments($content);

if ($user->get('username') == $viewer->get('username'))
    $content = str_remove($content, '%USER_EDIT%');
else
    $content = str_remove_where($content, '%USER_EDIT%');

$content = str_replace('%USER_AVATAR%', $user->get('picture'), $content);

$profil = $user->get('profil');
if (!empty($profil))
    $profil = json_decode($profil);
else
    $profil = new StdClass();
if (empty($profil->birthday))
    $profil->birthday = '{@UNKNOW}';
else {
    $birthday = explode('-', $profil->birthday);
    $birthday = ltrim($birthday[2], '0') . ' {@'.date('F', mktime(0, 0, 0, $birthday[1], 1, 2000)).'}';
    $profil->birthday = $birthday;
}
if (empty($profil->gender))
    $profil->gender = '{@UNKNOW}';
else
    $profil->gender = '{@' . strtoupper($profil->gender) . '}';
if (empty($profil->city))
    $profil->city = '{@UNKNOW}';
if (empty($profil->country))
    $profil->country = '{@UNKNOW}';

$content = str_replace_array($content, array(
    '%USERNAME%',
    '%DATE_CREATION%',
    '%BIRTHDAY%',
    '%GENDER%',
    '%CITY%',
    '%COUNTRY%'
), array(
    $user->get('username'),
    reldate($user->get('date_creation')),
    $profil->birthday,
    $profil->gender,
    $profil->city,
    $profil->country
));


if ($userAuth) {
    $content = str_remove_where($content, '%USER_NOT_CONNECTED%');
    $content = str_remove($content, '%USER_CONNECTED%');
} else {
    $content = str_remove_where($content, '%USER_CONNECTED%');
    $content = str_remove($content, '%USER_NOT_CONNECTED%');
}


// Comments
if ($userFound) {
    $content = str_replace('%PROFIL_ID%', $user->get('id'), $content);
    $commentHtml = str_between($content, '%STATUS%');
    $removeComment = str_between($content, '%REMOVE_STATUS%');
    $comments = $user->getStatus($user->get('id'));

    foreach ($comments as $comment) {
        $currentComment = $commentHtml;
        
        $commentAuthor = new User;
        $commentAuthor->setup($comment['author_id'], 'id');
        
        if ($viewer->get('permission') >= $page->get('p_edit') or $commentAuthor->get('id') === $viewer->get('id') or $viewer->get('id') === $user->get('id')) {
            $currentComment = str_replace( '%REMOVE_STATUS%', '', $currentComment );
            $currentComment = str_replace( '%STATUS_ID%', $comment['id'], $currentComment );
        } else {
            $currentComment = str_replace( '%REMOVE_STATUS%'.$removeComment.'%REMOVE_STATUS%', '', $currentComment );
        }
        
        $currentComment = str_replace( '%STATUS_USER%', $commentAuthor->get('username'), $currentComment );
        $currentComment = str_replace( '%URL_USER_PICTURE%', $commentAuthor->get('picture'), $currentComment );
        $currentComment = str_replace( '%URL_USER%', '/' .REQUEST_CURRENT . '/' . $commentAuthor->get('username') . '/', $currentComment );
        $currentComment = str_replace( '%STATUS_CONTENT%', nl2br(htmlspecialchars( $comment['content'] )), $currentComment );
        $currentComment = str_replace( '%STATUS_DATE%', reldate($comment['date']), $currentComment );
        $displayComments .= $currentComment;
    }
    $content = str_replace('%STATUS%'.$commentHtml.'%STATUS%', $displayComments, $content);
    $content = str_replace('%NO_USER_FOUND%', '', $content);
} else {
    $content = str_remove_where($content, '%NO_USER_FOUND%');
}
