<?php

$user = new User;

if (POST and isset($_POST['member'])) {
    if (empty($_POST['member'])) {
        go(REQUEST_SLUG);
    } else {
        go(REQUEST_SLUG .'/'. str_limit($_POST['member'], 50));
    }
}

$viewer = new User;
$viewer->setup();
$userAuth = $viewer->auth();

$userFound = true;
if (!REQUEST_ARGS) {
    $user_exist = $user->setup();
    if ($user_exist === false) {
        respond(false, 'USER_NEED_SEARCH');
        $userFound = false;
    }
} else {
    $username = head(explode('/', REQUEST_ARGS));
    $user_exist = $user->setup($username);
    if ($user_exist === false) {
        respond(false, 'USER_UNKNOWN');
        $userFound = false;
    } else {
        define('TITLE', '{@PROFIL_OF} ' . $user->get('username'));
    }
}

if (POST_METHOD === 'add' and !hasFlash() and $viewer->auth()) {
    if ($_POST['profilId'] === $user->get('id')) {
        try {
            $user->addStatus($_POST['content'], $viewer->get('id'), $_POST['profilId']);
            respond(true, 'STATUS_ADDED', '$');
            $_POST['content'] = '';
        } catch (Exception $e) {
            respond($e);
        }
    } else {
        respond(false, 'UNKNOW_USER');
    }
} else if (POST_METHOD === 'remove' and !hasFlash() and $viewer->auth()) {
    try {
        $user->deleteStatus($_POST['statusId'], $user->get('id'), true);
        respond(true, 'STATUS_REMOVED', '$');
    } catch (Exception $e) {
        respond($e);
    }
}

include (VIEW . 'member.php');
