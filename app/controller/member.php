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

if ( empty( $request['args'] ) ) {
    $user_exist = $user->setup();
    if ($user_exist === false) {
        $error = '{@' . 'USER_NEED_SEARCH' . '}';
    }
} else {
    $user_req = explode('/', $request['args']);
    $username = $user_req[0];
    if ( !empty( $user_req[1] ) ) {
        $this->go( $request['slug'] . '/' . $user_req[0] );
    }
    $user_exist = $user->setup($username);
    if ($user_exist === false) {
        $error = '{@' . 'USER_UNKNOWN' . '}';
    } else {
        define( 'TITLE', '{@PROFIL_OF} ' . $user->get('username') );
    }
}

$content = $page->get('content');
include ( VIEW . 'member.php');
$content .= $html;
