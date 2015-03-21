<?php

$user = new User;
$user->setup();

if (REQUEST_ARGS) {
    $redirect = true;
    $args = explode('/', $request['args']);
    if (ctype_digit($args[0]) and ctype_alnum($args[1]) and strlen($args[1]) == 20) {
        $user_recovery = new User;
        $user_exists = $user_recovery->setup($args[0], 'id');
        if ($user_exists) {
            $result = $user_recovery->validateToken($args[1]);
            if ($result) {
                $loginPage = $page->getPage(array('type' => 'native', 'type_data' => 'login'));
                respond(true, 'MAIL_CONFIRMED', $loginPage['slug']);
                go($loginPage['slug']);
            }
        }
    }
    if ($redirect)
        go(REQUEST_CURRENT);
}

if ($user->auth())
    go();

$config = new Config;
$needCaptcha = $config->get('user.settings.captcha_register', true);

if ($needCaptcha) {
    $captcha = $this->package('captcha');
    if ($captcha)
        $captcha = new Captcha;
    else {
        respond(false, '{@ERROR_MISSING_PACKAGE}'.'Captcha');
        $needCaptcha = false;
    }
}

if (POST and !hasFlash()) {
    if (!$needCaptcha or $captcha->check($_POST['captcha'])) {
        try {
            $register = $user->create( $_POST['username'], $_POST['email'], $_POST['password'], $_POST['confirm-password'] ); 
        } catch (Exception $e) {
            respond($e);
        }
        if ( $register and !hasFlash()) {
            if ($config->get('user.settings.verifymail', false)) {
                respond(true, 'PLEASE_CONFIRM_MAIL');                
            } else {
                $loginPage = $page->getPage(array('type' => 'native', 'type_data' => 'login'));
                respond(true, 'REGISTRATION_SUCCESSFUL', $loginPage['slug']);
            }
        }
    } else {
        respond(false, 'INVALID_CAPTCHA');
    }
}

if ($needCaptcha)
    $captchaImage = $captcha->get();

include ( VIEW . 'register.php');
