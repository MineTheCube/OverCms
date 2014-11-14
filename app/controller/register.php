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
                $success = '{@' . 'MAIL_CONFIRMED' . '}';
                $redirect = false;
                $_POST['username'] = $user_recovery->get('username');
                $_POST['email'] = $user_recovery->get('email');
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

$captcha = $this->package('captcha');
if ($captcha)
    $captcha = new Captcha;
else
    $error = '{@ERROR_MISSING_PACKAGE}'.'Captcha';

if ( isset($_POST['send']) and empty($error) ) {
    if ($this->checkToken(true)) {
        if ($captcha->check($_POST['captcha'])) {
            try {
                $register = $user->create( $_POST['username'], $_POST['email'], $_POST['password'], $_POST['confirm-password'] ); 
            } catch (Exception $e) {
                $error = '{@' . $e->getMessage() . '}';
            }
            if ( $register and empty( $error ) ) {
                $config = new Config;
                if ($config->get('user->settings->verifymail', false)) {
                    $success = '{@' . 'PLEASE_CONFIRM_MAIL' . '}';                
                } else {
                    $success = '{@' . 'REGISTRATION_SUCCESSFUL' . '}';
                }
            }
        } else {
            $error = '{@' . 'INVALID_CAPTCHA' . '}';
        }
    } else {
        $error = '{@' . 'INVALID_TOKEN' . '}';
    }
}

$captchaImage = $captcha->generate();
$token = $this->getToken();

$content = $page->get('content');
include ( VIEW . 'register.php');
$content .= $html;
