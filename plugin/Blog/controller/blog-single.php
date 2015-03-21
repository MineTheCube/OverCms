<?php

defined('IN_ENV') or die;

$reqArticle = explode('/', REQUEST_ARGS);

$canEdit = $user->canEdit($page);

$btnEditUrl = ABS_ROOT . REQUEST_CURRENT . '/panel/' . $reqArticle[0] . '/';

try {
    $article = $blog->getArticle($reqArticle[0], $canEdit);
} catch (Exception $e) {
    if ($e->getMessage() == 'INVALID_DATA')
        go( REQUEST_CURRENT );
    respond('BLOG', $e);
}

if ($article['slug'] !== $reqArticle[1] and empty($error)) {
    go(REQUEST_CURRENT . '/' . $article['id'] . '/' . $article['slug']);
}

if (!empty($article['title'])) {
    define('TITLE', $article['title']);
}

if (POST_METHOD === 'add' and !hasFlash() and $user->auth()) {
    if ($_POST['articleId'] === $article['id']) {
        try {
            $blog->addComment($_POST['content'], $user->get('id'), $_POST['articleId']);
            respond(true, 'BLOG_COMMENT_ADDED', '$');
            $_POST['content'] = '';
        } catch (Exception $e) {
            respond('BLOG', $e);
        }
    } else {
        respond(false, 'BLOG_UNKNOW_ARTICLE');
    }
} else if (POST_METHOD === 'remove' and !hasFlash() and $user->auth()) {
    try {
        $blog->deleteComment($_POST['commentId'], $article['id'], true);
        respond(true, 'BLOG_COMMENT_REMOVED', '$');
    } catch (Exception $e) {
        respond('BLOG', $e);
    }
}

include ( $path . 'view/blog-single.php');
