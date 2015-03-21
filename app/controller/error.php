<?php

if (REQUEST_ARGS === '404') {

    header("HTTP/1.0 404 Not Found");
    $content = '<h2>{@PAGE_404}</h2>';
    $content .= '<br>';
    $content .= '<p>{@PAGE_404_DESC}</p>';

} else if (REQUEST_ARGS === '403') {

    header('HTTP/1.1 403 Forbidden');
    $content = '<h2>{@PAGE_403}</h2>';
    $content .= '<br>';
    $content .= '<p>{@PAGE_403_DESC}</p>';

} else {

    header('HTTP/1.1 500 Internal Server Error');
    $content = '<h2>{@PAGE_500}</h2>';
    $content .= '<br>';
    $content .= '<p>{@PAGE_500_DESC}</p>';

}
