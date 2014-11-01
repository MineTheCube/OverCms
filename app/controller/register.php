<?php

if ( !empty( $request['args'] ) ) {
    $this->go( $request['slug'] );
}

$user = new User;
$user->setup();
$tools = new Tools;

if ($user->auth()) {
    $this->go();
}

if ( isset( $_POST['send'] ) ) {
    if ($this->checkToken(true)) {
        try {
            $register = $user->create( $_POST['username'], $_POST['email'], $_POST['password'], $_POST['confirm-password'] ); 
        } catch (Exception $e) {
            $error = '{@' . $e->getMessage() . '}';
        }
        if ( $register and empty( $error ) ) {
            $success = '{@' . 'REGISTRATION_SUCCESSFUL' . '}';
        }
    } else {
        $error = '{@' . 'INVALID_TOKEN' . '}';
    }
}

$token = $this->getToken();

$content = $page->get('content');
include ( VIEW . 'register.php');
$content .= $html;
