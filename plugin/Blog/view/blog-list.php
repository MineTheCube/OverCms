<?php

defined('IN_ENV') or die;

$parser = new Parser;
$parser->loadFile($template . 'blog-list' . EXT, array(
    'p edit',
    'article' => array('normal article', 'hidden article'),
    'previous',
    'pagination',
    'next'
));

// After articles
if ($displaypage == 1) {
    $parser->previous->parse(array(
        'url' => '#',
        'disabled' => 'disabled'
    ));
} else {
    $parser->previous->parse(array(
        'url' => ABS_ROOT . REQUEST_CURRENT . '/page/' . ($displaypage-1) . '/',
        'disabled' => ''
    ));
}

if ($displaypage == $pages) {
    $parser->next->parse(array(
        'url' => '#',
        'disabled' => 'disabled'
    ));
} else {
    $parser->previous->parse(array(
        'url' => ABS_ROOT . REQUEST_CURRENT . '/page/' . ($displaypage+1) . '/',
        'disabled' => ''
    ));
}

// Edit
$parser->p_edit->parseIf($canEdit, array('url add article' => $p_edit_url));


if ($pages > 5 and $displaypage > $pages-3 and $displaypage >= 5) {
    for($i = $pages-4; $i <= $pages; $i++) {
        $url = ABS_ROOT . REQUEST_CURRENT . '/page/' . ($i) . '/';
        $parser->pagination->add(array(
            'num page' => $i,
            'url' => $url,
            'disabled' => ($i == $displaypage ? 'disabled' : '')
        ));
    }
} else if ($pages > 5 and $displaypage >= 3) {
    for($i = $displaypage-2; $i <= $displaypage+2; $i++) {
        $url = ABS_ROOT . REQUEST_CURRENT . '/page/' . ($i) . '/';
        $parser->pagination->add(array(
            'num page' => $i,
            'url' => $url,
            'disabled' => ($i == $displaypage ? 'disabled' : '')
        ));
    }
} else {
    for($i = 1; $i <= $pages; $i++) {
        $url = ABS_ROOT . REQUEST_CURRENT . '/page/' . ($i) . '/';
        $parser->pagination->add(array(
            'num page' => $i,
            'url' => $url,
            'disabled' => ($i == $displaypage ? 'disabled' : '')
        ));
        if ($i == 5)
            break;
    }
}

if (!hasFlash()) {
    $page_member = $page->getPage(array('type' => 'native', 'type_data' => 'member' ));
    $page_member = ABS_ROOT . $page_member['slug'] . '/';
    foreach( $articles as $article ) {
        $rows = $blog->getComments( $article['id'] );
        $comments = count($rows);
        $author = new User;
        $userStillExists = $author->setup( $article['author_id'], 'id' );
        if (!$userStillExists) {
            $blog->deleteUser($article['author_id']);
        } else {
            $url_article = ABS_ROOT . REQUEST_CURRENT . '/' . $article['id'] . '/' . $article['slug'] . '/';
            if ($article['state'] == 1) {
                $parser->normal_article->remove();
                $parser->hidden_article->add();
            } else {
                $parser->hidden_article->remove();
                $parser->normal_article->add();
            }
            $url_picture = $article['picture'];
            if (empty($url_picture)) {
                $url_picture = '{PLUGIN_ASSETS}default-article.png';
            }
            $date = reldate($article['date']);
            $urlAuthor = $page_member . $author->get('username') . '/';
            $parser->article->add(array(
                'TITLE'       => e($article['title']),
                'DATE'        => $date,
                'AUTHOR'      => $author->get('username'),
                'URL_AUTHOR'  => $urlAuthor,
                'URL_ARTICLE' => $url_article,
                'URL_PICTURE' => $url_picture,
                'CONTENT'     => $article['content'],
                'COMMENTS'    => $comments,
                'S'           => ($comments > 1 ? 's' : '' )
            ));
        }
    }
}

$content = $parser->render();