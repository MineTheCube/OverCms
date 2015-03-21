<?php

if (REQUEST_ARGS)
    go(REQUEST_SLUG);

$user = new User;
$user->setup();

if (!$user->auth()) {
    $pageLogin = $page->getPage(array('type' => 'native', 'type_data' => 'login'));
    go($pageLogin['slug']);
}

if (POST_METHOD === 'updateAvatar') {
    if (empty($_POST['avatar_url']))
        $data = $_FILES['avatar_file'];
    else
        $data = $_POST['avatar_url'];
    try {
        $r = $user->update($user->get('id'), 'avatar', $data); 
        respond($r);
    } catch (Exception $e) {
        respond($e);
    }
} else if (POST_METHOD === 'updatePassword') {
    try {
        $r = $user->update( $user->get('id'), 'password', $_POST['old-password'], $_POST['password'], $_POST['confirm-password'] ); 
        respond($r);
    } catch (Exception $e) {
        respond($e);
    }
} else if (POST_METHOD === 'updateEmail') {
    try {
        $r = $user->update( $user->get('id'), 'email', $_POST['email'], $_POST['confirm-email'] ); 
        if ($r and with(new Config)->get('user.settings.verifymail', false)) {
            $user->logout();
            $pageLogin = $page->getPage(array('type' => 'native', 'type_data' => 'login'));
            setFlash(true, 'PLEASE_CONFIRM_NEW_MAIL');
            ajax(true, 'PLEASE_CONFIRM_NEW_MAIL', $pageLogin['slug']);
            go($pageLogin['slug']);
        } else {
            respond($r);
        }
    } catch (Exception $e) {
        respond($e);
    }
} else if (POST_METHOD === 'updateProfil') {
    try {
        $r = $user->update( $user->get('id'), 'profil', $_POST['birthday'], $_POST['gender'], $_POST['city'], $_POST['country'] ); 
        respond($r);
    } catch (Exception $e) {
        respond($e);
    }
}


// refresh user
$user->setup();
$profil = json_decode($user->get('profil'));

include (VIEW.'account'.EX);
