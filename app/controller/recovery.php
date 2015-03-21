<?php

$user = new User;
$user->setup();
if ($user->auth())
    go();


if (REQUEST_ARGS) {
    $redirect = true;
    $args = explode('/', $request['args']);
    if (ctype_digit($args[0]) and ctype_alnum($args[1]) and strlen($args[1]) == 20) {
        $user_recovery = new User;
        $user_exists = $user_recovery->setup($args[0], 'id');
        if ($user_exists) {
            $result = $user_recovery->validateToken($args[1]);
            if ($result) {
                respond(true, 'NEW_PASSWORD_SENDED');
                $redirect = false;
                $_POST['email'] = $user_recovery->get('email');
                $user_recovery->generatePassword($args[0]);
            }
        }
    }
    if ($redirect)
        go(REQUEST_CURRENT);
}


if (POST) {
    try {
        $recovery = $user->recovery($_POST['email']); 
        respond(true, 'RECOVERY_SUCCESSFUL');
    } catch (Exception $e) {
        respond($e);
    }
}


include ( VIEW.'recovery'.EX);
