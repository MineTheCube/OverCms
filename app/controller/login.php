<?php

if (REQUEST_ARGS)
    go(REQUEST_SLUG);

$user = new User;
$user->setup();

if ($user->auth())
    go();

if (POST) {
    try {
        $user->login($_POST['username'], $_POST['password'], ($_POST['remember'] == 'true'));
        $memberPage = $page->getPage(array('type' => 'native', 'type_data' => 'member'));
        respond(true, 'LOGIN_SUCCESSFUL', $memberPage['slug']);
    } catch (Exception $e) {
        respond($e);
    }
}

include (VIEW.'login'.EX);
