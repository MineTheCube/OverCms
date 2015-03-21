<?php

$content = file_get_contents(TEMPLATE . CURRENT_TEMPLATE . '/views/account' . EXT);
$content = strip_comments($content);

$content = str_replace('%USER_AVATAR%', $user->get('picture'), $content);

if (!empty($profil->birthday))
    $content = str_replace('%BIRTHDAY%', str_replace('-', '/', $profil->birthday), $content);
else
    $content = str_replace('%BIRTHDAY%', '', $content);

if ($profil->gender == 'man')
    $content = str_replace('%SELECTED_MAN%', 'selected="selected"', $content);
if ($profil->gender == 'woman')
    $content = str_replace('%SELECTED_WOMAN%', 'selected="selected"', $content);

$content = str_replace('%SELECTED_MAN%', '', $content);
$content = str_replace('%SELECTED_WOMAN%', '', $content);
$content = str_replace('%CITY%', $profil->city, $content);
$content = str_replace('%COUNTRY%', $profil->country, $content);
