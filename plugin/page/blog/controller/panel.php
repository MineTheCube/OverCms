<?php

$user = new User;
$user->setup();
$tools = new Tools;

if ($user->get('permission') < $page->get('p_edit') or !$user->auth() ) {
    $this->go( $request['current'] );
}

$edit_article = explode('/', $request['args']);

$hideForm = false;
$edit = false;
if ( is_numeric( $edit_article[1] ) and $edit_article[1] >= 1 ) {
    $result = $blog->setup( $edit_article[1] );
    if ($result !== true) {
        $error = '{@' . 'BLOG_UNKNOW_ARTICLE' . '}';
        $hideForm = true;
    } else {
        $edit = $edit_article[1];
    }
}

if ( $_POST['send'] == 1 and $_POST['method'] == 'add' ) {
    $date = strtotime( $_POST['date'] . ' ' . $_POST['hour'] );
    if ($date === false) {
        $error = '{@' . 'BLOG_INVALID_DATE' . '}';
    } else {
        try {
            $blog->addArticle($_POST['bbcode'], $_POST['title'], $user->get('id'), '', $_POST['state'], $date);
            $success = '{@BLOG_' . 'ARTICLE_ADDED' . '}';
            $hideForm = true;
        } catch (Exception $e) {
            $error = '{@BLOG_' . $e->getMessage() . '}';
        }
    }
} else if ( $_POST['send'] == 1 and $_POST['state'] == '2' ) {
    try {
        $blog->deleteArticle($_POST['id']);
        $success = '{@BLOG_' . 'ARTICLE_REMOVED' . '}';
        $hideForm = true;
    } catch (Exception $e) {
        $error = '{@BLOG_' . $e->getMessage() . '}';
        $hideForm = true;
    }
} else if ( $_POST['send'] == 1 and $_POST['method'] == 'edit' ) {
    $date = strtotime( $_POST['date'] . ' ' . $_POST['hour'] );
    if ($date === false) {
        $error = '{@' . 'BLOG_INVALID_DATE' . '}';
    } else {
        try {
            $blog->editArticle($_POST['id'], $_POST['bbcode'], '', $_POST['title'], 0, $date, $_POST['state']);
            $success = '{@BLOG_' . 'ARTICLE_EDITED' . '}';
        } catch (Exception $e) {
            $error = '{@BLOG_' . $e->getMessage() . '}';
        }
    }
}

include ( PATH_PLUGIN . 'view/panel.php');
$content = $html;
