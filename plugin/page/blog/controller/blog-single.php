<?php

define( 'WIDE', true);

$reqArticle = explode('/', $request['args']);

$user = new User;
$user->setup();
$tools = new Tools;

if ($user->get('permission') >= $page->get('p_edit')) {
    $canEdit = true;
} else {
    $canEdit = false;
}

$btnEditUrl = ABS_ROOT . $request['current'] . '/panel/' . $reqArticle[0] . '/';

try {
    $article = $blog->getArticle($reqArticle[0], $canEdit);
} catch (Exception $e) {
    if ($e->getMessage() == 'INVALID_DATA')
        $this->go( $request['current'] );
    $error = '{@BLOG_' . $e->getMessage() . '}';
}

if ($article['slug'] !== $reqArticle[1] and empty($error)) {
    $this->go($request['current'] . '/' . $article['id'] . '/' . $article['slug']);
}

if (!empty($article['title'])) {
    define('TITLE', $article['title']);
}

if ( $_POST['send'] == 1 and $_POST['method'] == 'add' and empty($error) and $user->auth() ) {
    if ($_POST['articleId'] === $article['id']) {
        try {
            $blog->addComment($_POST['content'], $user->get('id'), $_POST['articleId']);
            $success = '{@BLOG_' . 'COMMENT_ADDED' . '}';
            $_POST['content'] = '';
        } catch (Exception $e) {
            $error = '{@BLOG_' . $e->getMessage() . '}';
        }
    } else {
        $error = '{@BLOG_' . 'UNKNOW_ARTICLE' . '}';
    }
} else if ( $_POST['send'] == 1 and $_POST['method'] == 'remove' and empty($error) and $user->auth() ) {
    try {
        $blog->deleteComment($_POST['commentId'], $article['id']);
        $success = '{@BLOG_' . 'COMMENT_REMOVED' . '}';
    } catch (Exception $e) {
        $error = '{@BLOG_' . $e->getMessage() . '}';
    }
}

include ( PATH_PLUGIN . 'view/blog-single.php');
$content = $html;
