<?php

$html = file_get_contents( $theme . 'blog-single' . EXT);

preg_match('/%ALERT_ERROR%(.*?)%ALERT_ERROR%/s', $html, $matchError);
$alertError = $matchError[1];

preg_match('/%ALERT_SUCCESS%(.*?)%ALERT_SUCCESS%/s', $html, $matchSuccess);
$alertSuccess = $matchSuccess[1];

preg_match('/%BTN_EDIT%(.*?)%BTN_EDIT%/s', $html, $matchEdit);
$btnEdit = $matchEdit[1];

if ($canEdit) {
    $html = str_replace( $matchEdit[0], $btnEdit , $html );
    $html = str_replace( '%URL_EDIT_ARTICLE%', $btnEditUrl , $html );
} else {
    $html = str_replace( $matchEdit[0], '' , $html );
}

if ( !empty( $error ) ) {
    $html = str_replace( $matchError[0], str_replace( '%MESSAGE%', $error, $alertError ) , $html );
    $html = str_replace( $matchSuccess[0], '' , $html );
} else if ( !empty( $success ) ) {
    $html = str_replace( $matchSuccess[0], str_replace( '%MESSAGE%', $success, $alertSuccess ) , $html );
    $html = str_replace( $matchError[0], '' , $html );
} else {
    $html = str_replace( $matchError[0], '' , $html );
    $html = str_replace( $matchSuccess[0], '' , $html );
}

// Articles
$page_member = $page->getPage(array('type' => 'native', 'type_data' => 'member' ));
$page_member = ABS_ROOT . $page_member['slug'] . '/';
$author = new User;
$author->setup( $article['author_id'], 'id' );

if ($article['state'] == 1) {
    preg_match('/%NORMAL_ARTICLE%(.*?)%NORMAL_ARTICLE%/s', $html, $match);
    $html = str_replace( $match[0], '', $html );
    $html = str_replace( '%HIDDEN_ARTICLE%', '', $html );
} else {
    preg_match('/%HIDDEN_ARTICLE%(.*?)%HIDDEN_ARTICLE%/s', $html, $match);
    $html = str_replace( $match[0], '', $html );
    $html = str_replace( '%NORMAL_ARTICLE%', '', $html );
}

$date = $tools->reldate($article['date']);
$html = str_replace( '%TITLE%',       $article['title'],                       $html );
$html = str_replace( '%DATE%',        $date,                                   $html );
$html = str_replace( '%AUTHOR%',      $author->get('username'),                $html );
$html = str_replace( '%URL_AUTHOR%',  $page_member . $author->get('username'), $html );
$html = str_replace( '%URL_ARTICLE%', $url_article,                            $html );
$html = str_replace( '%CONTENT%',     $article['html'],                        $html );

if ($user->auth()) {
    preg_match('/%USER_NOT_CONNECTED%(.*?)%USER_NOT_CONNECTED%/s', $html, $match);
    $html = str_replace( $match[0], '', $html );
    $html = str_replace( '%USER_CONNECTED%', '', $html );
} else {
    preg_match('/%USER_CONNECTED%(.*?)%USER_CONNECTED%/s', $html, $match);
    $html = str_replace( $match[0], '', $html );
    $html = str_replace( '%USER_NOT_CONNECTED%', '', $html );
}

// Comments
$html = str_replace( '%ARTICLE_ID%', $article['id'], $html );
preg_match('/%COMMENTS%(.*?)%COMMENTS%/s', $html, $matchComment);
$commentHtml = $matchComment[1];
preg_match('/%REMOVE_COMMENT%(.*?)%REMOVE_COMMENT%/s', $html, $matchRemoveComment);
$removeComment = $matchRemoveComment[1];

$comments = $blog->getComments( $article['id'] );
while($comment = $comments->fetch()) {
    $currentComment = $commentHtml;
    
    $commentAuthor = new User;
    $commentAuthor->setup( $comment['author_id'], 'id' );
    
    if ($canEdit or $commentAuthor->get('id') === $user->get('id')) {
        $currentComment = str_replace( '%REMOVE_COMMENT%', '', $currentComment );
        $currentComment = str_replace( '%COMMENT_ID%', $comment['id'], $currentComment );
    } else {
        $currentComment = str_replace( $matchRemoveComment[0], '', $currentComment );
    }
    
    $currentComment = str_replace( '%COMMENT_USER%', $commentAuthor->get('username'), $currentComment );
    $currentComment = str_replace( '%URL_USER_PICTURE%', $commentAuthor->get('picture'), $currentComment );
    $currentComment = str_replace( '%URL_USER%', $page_member . $commentAuthor->get('username') . '/', $currentComment );
    $currentComment = str_replace( '%COMMENT%', nl2br(htmlspecialchars( $comment['content'] )), $currentComment );
    $currentComment = str_replace( '%COMMENT_DATE%', $tools->reldate($comment['date']), $currentComment );
    $displayComments .= $currentComment;
}

$html = str_replace( $matchComment[0], $displayComments, $html );
