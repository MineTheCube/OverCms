<?php

$html = file_get_contents( $theme . 'blog-list' . EXT);

// Before articles

preg_match('/%ALERT%(.*?)%ALERT%/s', $html, $match);
$alertError = $match[1];
$html = str_replace( $match[0], '', $html );

preg_match('/%P_EDIT%(.*?)%P_EDIT%/s', $html, $match);
$p_edit = $match[1];
$html = str_replace( $match[0], '', $html );

// Articles
preg_match('/%ARTICLE%(.*?)%ARTICLE%/s', $html, $match);
$htmlArticle = $match[1];
$html = str_replace( $match[0], '', $html );

// After articles

preg_match('/%PREVIOUS%(.*?)%PREVIOUS%/s', $html, $match);
$previous = $match[1];
$html = str_replace( $match[0], '', $html );
if ($displaypage == 1) {
    $previous = str_replace( '%URL%', '#', $previous );
    $previous = str_replace( '%DISABLED%', 'disabled', $previous );
} else {
    $previous = str_replace( '%DISABLED%', '', $previous );
    $previous = str_replace( '%URL%', ABS_ROOT . REQUEST_CURRENT . '/page/' . ($displaypage-1) . '/', $previous );
}

preg_match('/%PAGINATION%(.*?)%PAGINATION%/s', $html, $match);
$pagination = $match[1];
$html = str_replace( $match[0], '', $html );

preg_match('/%NEXT%(.*?)%NEXT%/s', $html, $match);
$next = $match[1];
$html = str_replace( $match[0], '', $html );

if ($displaypage == $pages) {
    $next = str_replace( '%URL%', '#', $next );
    $next = str_replace( '%DISABLED%', 'disabled', $next );
} else {
    $next = str_replace( '%DISABLED%', '', $next );
    $next = str_replace( '%URL%', ABS_ROOT . REQUEST_CURRENT . '/page/' . ($displaypage+1) . '/', $next );
}

// Replacement

if ($canEdit) {
    $html = str_replace( '%P_EDIT_GOES_HERE%', $p_edit , $html );
    $html = str_replace( '%URL_ADD_ARTICLE%', $p_edit_url , $html );
} else {
    $html = str_replace( '%P_EDIT_GOES_HERE%', '' , $html );
}

$navpage = $previous;
if ($pages > 5 and $displaypage >= $pages-5 and $displaypage >= 5) {
    for($i = $pages-4; $i <= $pages; $i++) {
        $url = ABS_ROOT . REQUEST_CURRENT . '/page/' . ($i) . '/';
        $navpage .= str_replace( '%NUM_PAGE%', $i, str_replace( '%URL%', $url, str_replace( '%DISABLED%', ($i == $displaypage ? 'disabled' : ''), $pagination ) ) );
    }
} else if ($pages > 5 and $displaypage >= 3) {
    for($i = $displaypage-2; $i <= $displaypage+2; $i++) {
        $url = ABS_ROOT . REQUEST_CURRENT . '/page/' . ($i) . '/';
        $navpage .= str_replace( '%NUM_PAGE%', $i, str_replace( '%URL%', $url, str_replace( '%DISABLED%', ($i == $displaypage ? 'disabled' : ''), $pagination ) ) );
    }
} else {
    for($i = 1; $i <= $pages; $i++) {
        $url = ABS_ROOT . REQUEST_CURRENT . '/page/' . ($i) . '/';
        $navpage .= str_replace( '%NUM_PAGE%', $i, str_replace( '%URL%', $url, str_replace( '%DISABLED%', ($i == $displaypage ? 'disabled' : ''), $pagination ) ) );
        if ($i == 5)
            break;
    }
}
$navpage .= $next;

if ( !empty( $error ) ) {
    $html = str_replace( '%ALERT_GOES_HERE%', str_replace( '%MESSAGE%', $error, $alertError ) , $html );
    $html = str_replace( '%PAGINATION_GOES_HERE%', '' , $html );
    $html = str_replace( '%ARTICLE_GOES_HERE%', '', $html );
} else {
    $html = str_replace( '%ALERT_GOES_HERE%', '' , $html );
    $html = str_replace( '%PAGINATION_GOES_HERE%', $navpage , $html );
    // List articles
    $page_member = $page->getPage(array('type' => 'native', 'type_data' => 'member' ));
    $page_member = ABS_ROOT . $page_member['slug'] . '/';
    foreach( $articles as $article ) {
        $rows = $blog->getComments( $article['id'] )->fetchAll(PDO::FETCH_ASSOC);
        $comments = count($rows);
        $author = new User;
        $author->setup( $article['author_id'], 'id' );
        $url_article = ABS_ROOT . REQUEST_CURRENT . '/' . $article['id'] . '/' . $article['slug'] . '/';
        $current_article = $htmlArticle;
        if ($article['state'] == 1) {
            preg_match('/%NORMAL_ARTICLE%(.*?)%NORMAL_ARTICLE%/s', $current_article, $match);
            $current_article = str_replace( $match[0], '', $current_article );
            $current_article = str_replace( '%HIDDEN_ARTICLE%', '', $current_article );
        } else {
            preg_match('/%HIDDEN_ARTICLE%(.*?)%HIDDEN_ARTICLE%/s', $current_article, $match);
            $current_article = str_replace( $match[0], '', $current_article );
            $current_article = str_replace( '%NORMAL_ARTICLE%', '', $current_article );
        }
        $date = $tools->reldate($article['date']);
        $urlAuthor = $page_member . $author->get('username') . '/';
        $current_article = str_replace( '%TITLE%',       $article['title'],           $current_article );
        $current_article = str_replace( '%DATE%',        $date,                       $current_article );
        $current_article = str_replace( '%AUTHOR%',      $author->get('username'),    $current_article );
        $current_article = str_replace( '%URL_AUTHOR%',  $urlAuthor,                  $current_article );
        $current_article = str_replace( '%URL_ARTICLE%', $url_article,                $current_article );
        $current_article = str_replace( '%CONTENT%',     $article['html'],            $current_article );
        $current_article = str_replace( '%COMMENTS%',    $comments,                   $current_article );
        $current_article = str_replace( '%S%',           ($comments > 1 ? 's' : '' ), $current_article );
        $display_articles .= $current_article;
    }
    // End articles
    $html = str_replace( '%ARTICLE_GOES_HERE%', $display_articles, $html );
}

