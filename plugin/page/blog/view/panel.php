<?php

ob_start();

include ( $template . 'panel' . EXT);

$html = ob_get_contents();
ob_end_clean();

// Before articles

preg_match('/%ALERT_ERROR%(.*?)%ALERT_ERROR%/s', $html, $match);
$alertError = $match[1];
$html = str_replace( $match[0], '', $html );

preg_match('/%ALERT_SUCCESS%(.*?)%ALERT_SUCCESS%/s', $html, $match);
$alertSuccess = $match[1];
$html = str_replace( $match[0], '', $html );


if ( !empty( $error ) ) {
    $html = str_replace( '%ALERT_GOES_HERE%', str_replace( '%MESSAGE%', $error,   $alertError ) ,   $html );
} else if ( !empty( $success ) ) {
    $html = str_replace( '%ALERT_GOES_HERE%', str_replace( '%MESSAGE%', $success, $alertSuccess ) , $html );
} else {
    $html = str_replace( '%ALERT_GOES_HERE%', '' , $html );
}

if ($edit === false) {
    $html = str_replace( '%ARTICLE_TITLE%',     $_POST['title'] ,       $html );
    $html = str_replace( '%ARTICLE_CONTENT%',   $_POST['bbcode'] ,      $html );
    $html = str_replace( '%ARTICLE_PICTURE%',   $_POST['picture'] ,     $html );
    $html = str_replace( '%ARTICLE_AUTHOR_ID%', $user->get('id') ,      $html );
    $html = str_replace( '%ARTICLE_DATE%',      date('Y-m-d', time()) , $html );
    $html = str_replace( '%ARTICLE_HOUR%',      date('H:i', time()) ,   $html );
    $html = str_replace( '%FORM_METHOD%',       'add' ,                 $html );
    $html = str_replace( '%ARTICLE_ID%',        0,                      $html );
    $html = str_replace( '%BLOG_ACTION%', 'BLOG_ADD' , $html );
    $html = str_replace( '%BTN_ACTION%', 'SAVE' , $html );
    preg_match('/%OPTIONS%(.*?)%OPTIONS%/s', $html, $match);
    $html = str_replace( $match[0], '', $html );
    $html = str_replace( '%TEXT_EDITOR%', $tools->loadTextEditor( 'bbcode', $_POST['bbcode'] ), $html );
} else {
    $html = str_replace( '%ARTICLE_TITLE%',     $blog->get('title') ,               $html );
    $html = str_replace( '%ARTICLE_CONTENT%',   $blog->get('bbcode') ,             $html );
    $html = str_replace( '%ARTICLE_PICTURE%',   $blog->get('picture') ,             $html );
    $html = str_replace( '%ARTICLE_AUTHOR_ID%', $blog->get('author_id') ,           $html );
    $html = str_replace( '%ARTICLE_DATE%',      date('Y-m-d', $blog->get('date')) , $html );
    $html = str_replace( '%ARTICLE_HOUR%',      date('H:i', $blog->get('date')) ,   $html );
    $html = str_replace( '%ARTICLE_ID%',        $edit ,                             $html );
    $html = str_replace( '%FORM_METHOD%', 'edit' , $html );
    $html = str_replace( '%BLOG_ACTION%', 'BLOG_EDIT' , $html );
    $html = str_replace( '%BTN_ACTION%', 'EDIT' , $html );
    $html = str_replace( '%OPTIONS%', '', $html);
    $html = str_replace( '%TEXT_EDITOR%', $tools->loadTextEditor( 'bbcode', $blog->get('bbcode') ), $html );
}

if ($hideForm === true) {
    preg_match('/%HIDE_FORM%(.*?)%HIDE_FORM%/s', $html, $match);
    $html = str_replace( $match[0], '', $html );
} else {
    $html = str_replace( '%HIDE_FORM%', '', $html);
}
