<?php

$user = new User;
$tools = new Tools;

if (isset($_POST['member'])) {
    if (empty($_POST['member'])) {
        $this->go( $request['slug'] );
    } else {
        $this->go( $request['slug'] . '/' . $_POST['member'] );
    }
}

$viewer = new User;
$viewer->setup();
$userAuth = $viewer->auth();

$userFound = true;
if ( empty( $request['args'] ) ) {
    $user_exist = $user->setup();
    if ($user_exist === false) {
        $alert = '{@' . 'USER_NEED_SEARCH' . '}';
        $userFound = false;
    }
} else {
    $user_req = explode('/', $request['args']);
    $username = $user_req[0];
    if ( !empty( $user_req[1] ) ) {
        $this->go( $request['slug'] . '/' . $user_req[0] );
    }
    $user_exist = $user->setup($username);
    if ($user_exist === false) {
        $alert = '{@' . 'USER_UNKNOWN' . '}';
        $userFound = false;
    } else {
        define( 'TITLE', '{@PROFIL_OF} ' . $user->get('username') );
    }
}

if ( $_POST['send'] == 1 and $_POST['method'] == 'add' and empty($alert) and $viewer->auth() ) {
    if ($_POST['profilId'] === $user->get('id')) {
        try {
            $user->addStatus($_POST['content'], $viewer->get('id'), $_POST['profilId']);
            $success = '{@' . 'STATUS_ADDED' . '}';
            $_POST['content'] = '';
        } catch (Exception $e) {
            $error = '{@' . $e->getMessage() . '}';
        }
    } else {
        $error = '{@' . 'UNKNOW_USER' . '}';
    }
} else if ( $_POST['send'] == 1 and $_POST['method'] == 'remove' and empty($alert) and $viewer->auth() ) {
    try {
        $user->deleteStatus($_POST['statusId'], $user->get('id'), true);
        $success = '{@' . 'STATUS_REMOVED' . '}';
    } catch (Exception $e) {
        $error = '{@' . $e->getMessage() . '}';
    }
}

$content = $page->get('content');
include ( VIEW . 'member.php');
$content .= $html;
