<?php

defined('IN_ENV') or die;

if (!$user->canEdit($page)) {
    go( REQUEST_CURRENT );
}

$edit_article = explode('/', REQUEST_ARGS);

$hideForm = false;
$edit = false;
if ( is_numeric( $edit_article[1] ) and $edit_article[1] >= 1 ) {
    $result = $blog->setup( $edit_article[1] );
    if ($result !== true) {
        respond(false, 'BLOG_UNKNOW_ARTICLE');
        $hideForm = true;
    } else {
        $edit = $edit_article[1];
    }
}

if (POST_METHOD === 'add') {
    $date = strtotime( $_POST['date'] . ' ' . $_POST['hour'] );
    if ($date === false) {
        respond(false, 'BLOG_INVALID_DATE');
    } else {
        try {
            $blog->addArticle($_POST['bbcode'], $_POST['title'], $user->get('id'), $_POST['picture'], $_POST['state'], $date);
            respond(true, 'BLOG_ARTICLE_ADDED');
            $hideForm = true;
        } catch (Exception $e) {
            respond('BLOG', $e);
        }
    }
} else if (POST and $_POST['state'] === '2') {
    try {
        $blog->deleteArticle($_POST['id']);
        respond(true, 'BLOG_ARTICLE_REMOVED');
        $hideForm = true;
    } catch (Exception $e) {
        respond('BLOG', $e);
        $hideForm = false;
    }
} else if (POST_METHOD === 'edit') {
    $date = strtotime( $_POST['date'] . ' ' . $_POST['hour'] );
    if ($date === false) {
        respond(false, 'BLOG_INVALID_DATE');
    } else {
        try {
            $blog->editArticle($_POST['id'], $_POST['bbcode'], $_POST['picture'], $_POST['title'], 0, $date, $_POST['state']);
            respond(true, 'BLOG_ARTICLE_EDITED');
        } catch (Exception $e) {
            respond('BLOG', $e);
        }
    }
}

include ( $path . 'view/panel.php');
