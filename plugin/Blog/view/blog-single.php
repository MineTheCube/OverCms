<?php

defined('IN_ENV') or die;

$parser = new Parser;
$parser->loadFile($template . 'blog-single' . EXT, array(
    'btn edit',
    'hidden article',
    'user not connected',
    'user connected',
    'comments' => 'remove comment',
    // 'remove comment'
));

$parser->btn_edit->parseIf($canEdit, array(
    'url edit article' => $btnEditUrl
));

// Articles
$page_member = $page->getPage(array('type' => 'native', 'type_data' => 'member' ));
$page_member = ABS_ROOT . $page_member['slug'] . '/';
$author = new User;
$author->setup( $article['author_id'], 'id' );


if ($article['state'] == 1) {
    $content = $parser->hidden_article->parse();
} else {
    $content = $parser->normal_article->parse();
}

$content = $parser->bind(array(
    'title'       => e($article['title']),
    'date'        => reldate($article['date']),
    'author'      => $author->get('username'),
    'url author'  => $page_member . $author->get('username'),
    'url article' => $url_article,
    'url picture' => $article['picture'],
    'content'     => $article['html']
));

$parser->user_connected->parseIf($user->auth());
$parser->user_not_connected->parseIf(!$user->auth());

// Comments
$parser->bind('article id', $article['id']);
$comments = $blog->getComments( $article['id'] );

foreach($comments as $comment) {
    
    $commentAuthor = new User;
    $userStillExists = $commentAuthor->setup( $comment['author_id'], 'id' );
    
    if (!$userStillExists) {
        $blog->deleteUser($comment['author_id']);
    } else {

        $parser->remove_comment->addIf(($canEdit or $commentAuthor->get('id') === $user->get('id')), array('COMMENT_ID' => $comment['id']));
        
        $parser->comments->add(array(
            'COMMENT_USER'     => $commentAuthor->get('username'),
            'URL_USER_PICTURE' => $commentAuthor->get('picture'),
            'URL_USER'         => $page_member . $commentAuthor->get('username') . '/',
            'COMMENT'          => nl2br(htmlspecialchars( $comment['content'] )),
            'COMMENT_DATE'     => reldate($comment['date'])
        ));

    }
}

$content = $parser->render();
