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
        if ($_POST['remember'] == 'on') {
            $remember = true;
        } else {
            $remember = false;
        }
        try {
            $auth = $user->login( $_POST['username'], $_POST['password'], $remember ); 
        } catch (Exception $e) {
            $error = '{@' . $e->getMessage() . '}';
        }
        if ( $auth and empty( $error ) ) {
            $page_member = $page->getPage(array('type' => 'native', 'type_data' => 'member' ));
            $this->go( $page_member['slug'] );
            exit();
        }
    } else {
        $error = '{@' . 'INVALID_TOKEN' . '}';
    }
}

$token = $this->getToken();

$content = $page->get('content');
include ( VIEW . 'login.php');
$content .= $html;
