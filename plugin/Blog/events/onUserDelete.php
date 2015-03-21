<?php

defined('IN_ENV') or die;

$id = (int) $event->id;

db()->delete('plugin_blog_posts')->where('author_id', $id)->run();
db()->delete('plugin_blog_comments')->where('author_id', $id)->run();
