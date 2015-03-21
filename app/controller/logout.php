<?php

$user = new User;
$user->setup();

if (!$user->auth())
    go();

$loginPage = $page->getPage(array('type' => 'native', 'type_data' => 'login'));
$user->logout();

respond(true, 'LOGOUT_SUCCESSFUL');
go($loginPage['slug']);
