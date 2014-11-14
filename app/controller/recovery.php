<?php

if ( !empty($request['args']) ) {
    $redirect = true;
    $args = explode('/', $request['args']);
    if (ctype_digit($args[0]) and ctype_alnum($args[1]) and strlen($args[1]) == 20) {
        $user_recovery = new User;
        $user_exists = $user_recovery->setup($args[0], 'id');
        if ($user_exists) {
            $result = $user_recovery->validateToken($args[1]);
            if ($result) {
                $success = '{@' . 'NEW_PASSWORD_SENDED' . '}';
                $redirect = false;
                $_POST['email'] = $user_recovery->get('email');
                $user_recovery->generatePassword($args[0]);
            }
        }
    }
    if ($redirect)
        $this->go( $request['current'] );
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
            $recovery = $user->recovery( $_POST['email'] ); 
        } catch (Exception $e) {
            $error = '{@' . $e->getMessage() . '}';
        }
        if ( $recovery and empty( $error ) ) {
            $success = '{@' . 'RECOVERY_SUCCESSFUL' . '}';
        }
    } else {
        $error = '{@' . 'INVALID_TOKEN' . '}';
    }
}

$token = $this->getToken();

$content = $page->get('content');
include ( VIEW . 'recovery.php');
$content .= $html;

        // echo 'truc';
        // echo '&nbsp;';