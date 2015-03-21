<?php

defined('IN_ENV') or die;

$config = json_decode($plugin['config'], true);

if ($config['enabled'] !== 'enabled')
    return;

$event->cancelEvent();
echo Html::file($plugin['template'].'page.htm', array('MESSAGE' => $config['message']));
