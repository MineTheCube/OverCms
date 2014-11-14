<?php

$pagelist = explode('/', REQUEST_ARGS);

if ( empty( $pagelist[0] ) ) {
    $displaypage = 1;
} else if ( $pagelist[0] == 'page' ) {
    if ( is_numeric( $pagelist[1] ) and $pagelist[1] >= 1 ) {
        $displaypage = $pagelist[1];
    } else {
        $app->go( REQUEST_CURRENT );
    }    
} else {
    $app->go( REQUEST_CURRENT );
}

if ($user->get('permission') >= $page->get('p_edit')) {
    $canEdit = true;
    $rows = $app->query('SELECT id FROM plugin_blog_posts WHERE 1')->fetchAll(PDO::FETCH_ASSOC);
    $pages = count($rows);
} else {
    $canEdit = false;
    $rows = $app->query('SELECT id FROM plugin_blog_posts WHERE state = 0')->fetchAll(PDO::FETCH_ASSOC);
    $pages = count($rows);
}

$p_edit_url = ABS_ROOT . REQUEST_CURRENT . '/panel/';

try {
    $articles = $blog->getList($displaypage, $canEdit);
} catch (Exception $e) {
    if ($e->getMessage() == 'INVALID_DATA')
        $app->go( REQUEST_CURRENT );
    $error = '{@BLOG_' . $e->getMessage() . '}';
}

$pages = floor($pages/5) + ($pages/5 == floor($pages/5) ? 0 : 1);

include ( $path . 'view/blog-list.php');
$content = $html;
