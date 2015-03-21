<?php

defined('IN_ENV') or die;

if ($event->pluginName !== $pluginName)
    return;

db()->drop('plugin_blog_comments')
    ->drop('plugin_blog_posts');