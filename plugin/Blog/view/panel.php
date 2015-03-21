<?php

defined('IN_ENV') or die;

$content = file_get_contents( $template . 'panel' . EXT);
$content = strip_comments($content);

// Before articles

if ($edit === false) {
    $search = array(
        '%ARTICLE_TITLE%',
        '%ARTICLE_CONTENT%',
        '%ARTICLE_PICTURE%',
        '%ARTICLE_AUTHOR_ID%',
        '%ARTICLE_DATE%',     
        '%ARTICLE_HOUR%',     
        '%FORM_METHOD%',      
        '%ARTICLE_ID%',       
        '%BLOG_ACTION%', 
        '%BTN_ACTION%',
        '%TEXT_EDITOR%'
    );
    $replace = array(
        $_POST['title'],
        $_POST['bbcode'],
        $_POST['picture'],
        $user->get('id'),
        date('Y-m-d', time()),
        date('H:i', time()),
        'add',
        0,
        'BLOG_ADD',
        'SAVE',
        with(new App)->getTextEditor('bbcode', $_POST['bbcode'])
    );
    $content = str_replace($search, $replace, $content);
    $content = str_remove_where($content, '%OPTIONS%');
} else {
    $search = array(
        '%ARTICLE_TITLE%',
        '%ARTICLE_CONTENT%',
        '%ARTICLE_PICTURE%',
        '%ARTICLE_AUTHOR_ID%',
        '%ARTICLE_DATE%',
        '%ARTICLE_HOUR%',
        '%ARTICLE_ID%',
        '%FORM_METHOD%',
        '%BLOG_ACTION%',
        '%BTN_ACTION%',
        '%OPTIONS%',
        '%TEXT_EDITOR%'
    );
    $replace = array(
        $blog->get('title'),
        $blog->get('bbcode'),
        $blog->get('picture'),
        $blog->get('author_id'),
        date('Y-m-d', $blog->get('date')),
        date('H:i', $blog->get('date')),
        $edit,
        'edit',
        'BLOG_EDIT',
        'EDIT',
        '',
        with(new App)->getTextEditor('bbcode', $blog->get('bbcode'))
    );
    $content = str_replace($search, $replace, $content);
}

if ($hideForm === true) {
    $content = str_remove_where($content, '%HIDE_FORM%');
} else {
    $content = str_replace( '%HIDE_FORM%', '', $content);
}
