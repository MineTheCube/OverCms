<?php

defined('IN_ENV') or die;

$pagelist = explode('/', REQUEST_ARGS);

if ( empty( $pagelist[0] ) ) {
    $displaypage = 1;
} else if ( $pagelist[0] == 'page' ) {
    if ( is_numeric( $pagelist[1] ) and $pagelist[1] >= 1 ) {
        $displaypage = $pagelist[1];
    } else {
        go( REQUEST_CURRENT );
    }    
} else {
    go( REQUEST_CURRENT );
}

if ($user->canEdit($page)) {
    $canEdit = true;
    $rows = db()->select('id')->from('plugin_blog_posts')->fetchAll();
    $pages = count($rows);
} else {
    $canEdit = false;
    $rows = db()->select('id')->from('plugin_blog_posts')->where('state', 0)->fetchAll();
    $pages = count($rows);
}

$p_edit_url = ABS_ROOT . REQUEST_CURRENT . '/panel/';

try {
    $articles = $blog->getList($displaypage, $canEdit);

    foreach ($articles as $key => $article) {
        $preview = $article['bbcode'];
        $preview = str_remove($preview, array(
            '[list]', '[/list]',
            '[*]',    '[/*]',
            '[h1]',   '[/h1]',
            '[h2]',   '[/h2]',
            '[h3]',   '[/h3]',
            '[b]',    '[/b]',
            '[i]',    '[/i]',
            '[u]',    '[/u]'
        ));
        $preview = preg_replace('/\[.*\](.*)\[\/.*\]/', '', $preview);
        $preview = str_limit($preview, 100);
        $preview = substr($preview, 0, strrpos($preview, ' ')).'..';
        $articles[$key]['content'] = $preview;
    }

} catch (Exception $e) {
    if ($e->getMessage() == 'INVALID_DATA')
        go( REQUEST_CURRENT );
    respond('BLOG', $e);
}

$pages = floor($pages/5) + ($pages/5 == floor($pages/5) ? 0 : 1);
if ($pages < 1);
    $pages = 1;

include ( $path . 'view/blog-list.php');
