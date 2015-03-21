<?php

$content = file_get_contents(TEMPLATE . CURRENT_TEMPLATE . '/views/register' . EXT);
$content = strip_comments($content);

if ($needCaptcha) {
    $content = str_replace('%CAPTCHA%', $captchaImage, $content);
    $content = str_replace('%IF_CAPTCHA%', '', $content);
} else {
    $content = str_remove_where($content, '%IF_CAPTCHA%');
}
