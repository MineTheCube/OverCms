<?php

defined('IN_ENV') or die;

Html::listen();

if ($depend['JSONAPI']) {

    $servers = $depend['JSONAPI'];

    foreach ($servers as $name => $JSONAPI) {

        echo '<h3>'.$name.'</h3>';

        $players = head($JSONAPI->call("getPlayerCount"));
        $slots = head($JSONAPI->call("getPlayerLimit"));

        if ($players['is_success'] and $slots['is_success']) {
            echo renderFlash(true, 'Le serveur est en ligne: '.$players['success'].'/'.$slots['success'], null, false);
        } else {
            echo renderFlash(false, 'Le serveur est hors ligne.', null, false);
        }

    }

} else {
    echo renderFlash(false);
}


return Html::output();