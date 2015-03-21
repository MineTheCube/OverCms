<?php

defined('IN_ENV') or die;

if ($event->pluginName !== $pluginName)
    return;

db()->create('cms_blog_comments', array(

    Table::id(),

    Table::string('type'),
    Table::text('content'),

    Table::int('author_id'),
    Table::int('post_id'),
    Table::int('date'),
    Table::int('state')

))->create('cms_blog_posts', array(

    Table::id(),

    Table::string('title'),
    Table::string('slug'),
    Table::text('bbcode'),
    Table::text('html'),
    Table::text('picture'),
    
    Table::int('author_id'),
    Table::int('date'),
    Table::int('state'),

));
