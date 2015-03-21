<?php

defined('IN_ENV') or die;

require_once $path.'class/JSONAPI'.EXC;

$config = json_decode($config);

$servers = array();

if (is_object($config) and !empty($config))
    foreach ($config->servers as $c)
        $servers[$c->name] = new JSONAPI($c->ip, $c->port, $c->username, $c->password);

return $servers;
