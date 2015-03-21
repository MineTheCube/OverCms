<?php

$content = file_get_contents(TEMPLATE . CURRENT_TEMPLATE . '/views/login' . EXT);
$content = strip_comments($content);
