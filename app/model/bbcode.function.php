<?php

function bbcode2html($bbcode) {
    // $bbcode = str_replace('[bbcode]', '', $bbcode);
    
    $bbtags = array(
        '[h1]' => '<h1>','[/h1]' => '</h1>',
        '[bigtitle]' => '<h1>','[/bigtitle]' => '</h1>',
        '[h2]' => '<h2>','[/h2]' => '</h2>',
        '[title]' => '<h2>','[/title]' => '</h2>',
        '[h3]' => '<h3>','[/h3]' => '</h3>',
        '[subtitle]' => '<h3>','[/subtitle]' => '</h3>',

        '[p]' => '<p>','[/p]' => '</p>',
        '[left]' => '<p style="text-align:left;margin: 0;">','[/left]' => '</p>',
        '[right]' => '<p style="text-align:right;margin: 0;">','[/right]' => '</p>',
        '[center]' => '<p style="text-align:center;margin: 0;">','[/center]' => '</p>',
        '[justify]' => '<p style="text-align:justify;margin: 0;">','[/justify]' => '</p>',

        '[bold]' => '<span style="font-weight:bold;">','[/bold]' => '</span>',
        '[italic]' => '<span style="font-style: italic;">','[/italic]' => '</span>',
        '[underline]' => '<span style="text-decoration:underline;">','[/underline]' => '</span>',
        '[b]' => '<span style="font-weight:bold;">','[/b]' => '</span>',
        '[i]' => '<span style="font-style: italic;">','[/i]' => '</span>',
        '[u]' => '<span style="text-decoration:underline;">','[/u]' => '</span>',

        '[list]' => '<ul>','[/list]' => '</ul>',
        '[ul]' => '<ul>','[/ul]' => '</ul>',
        '[ol]' => '<ol>','[/ol]' => '</ol>',
        '[list_item]' => '<li>','[/list_item]' => '</li>',
        '[li]' => '<li>','[/li]' => '</li>',

        '[*]' => '<li>','[/*]' => '</li>',
        '[code]' => '<code>','[/code]' => '</code>',
        '[pre]' => '<pre>','[/pre]' => '</pre>'    
    );

    $bbcode = str_ireplace(array_keys($bbtags), array_values($bbtags), $bbcode);

    $bbextended = array(
        "/\[url](.*?)\[\/url]/i" => "<a href=\"http://$1\" title=\"$1\">$1</a>",
        "/\[url=(.*?)\](.*?)\[\/url\]/i" => "<a href=\"$1\" title=\"$1\">$2</a>",
        "/\[email=(.*?)\](.*?)\[\/email\]/i" => "<a href=\"mailto:$1\">$2</a>",
        "/\[mail=(.*?)\](.*?)\[\/mail\]/i" => "<a href=\"mailto:$1\">$2</a>",
        "/\[img\]([^[]*)\[\/img\]/i" => "<img src=\"$1\" alt=\" \" />",
        "/\[image\]([^[]*)\[\/image\]/i" => "<img src=\"$1\" alt=\" \" />",
        "/\[image_left\]([^[]*)\[\/image_left\]/i" => "<img src=\"$1\" alt=\" \" style=\"float: left\" />",
        "/\[image_right\]([^[]*)\[\/image_right\]/i" => "<img src=\"$1\" alt=\" \" style=\"float: right\" />",
        "/\[youtube\]([^[]*)\[\/youtube\]/i" => "<div style=\"position: relative;display: block;height: 0;padding: 0;overflow: hidden;padding-bottom: 56.25%;\"><iframe style=\"position: absolute;top: 0;bottom: 0;left: 0;width: 100%;height: 100%;border: 0;\" src=\"//www.youtube.com/embed/$1\" frameborder=\"0\" allowfullscreen></iframe></div>"
    );

    foreach($bbextended as $match=>$replacement){
        $bbcode = preg_replace($match, $replacement, $bbcode);
    }
  
    $html = nl2br( $bbcode );
    $html = str_replace( '</li><br />', '</li>', $html);
    $html = str_replace( '</p><br />', '</p>', $html);
    $html = str_replace( '</h1><br />', '</h1>', $html);
    $html = str_replace( '</h2><br />', '</h2>', $html);
    $html = str_replace( '</h3><br />', '</h3>', $html);
    return $html;
}