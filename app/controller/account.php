<?php

if ( !empty( $request['args'] ) ) {
    $this->go( $request['slug'] );
}

$user = new User;
$user->setup();
$tools = new Tools;

if (!$user->auth()) {
    $app = new App;
    $page_login = $page->getPage(array('type' => 'native', 'type_data' => 'login' ));
    $app->go( $page_login['slug'] );
}

if ( $_POST['send_avatar'] == 1 ) {
    if ($this->checkToken(true)) {
        if (empty($_POST['avatar_url'])) {
            $data = $_FILES['avatar_file'];
        } else {
            $data = $_POST['avatar_url'];
        }

        try {
            $picture = $user->update( $user->get('id'), 'avatar', $data ); 
        } catch (Exception $e) {
            $error = '{@' . $e->getMessage() . '}';
        }
        if ( $picture and empty( $error ) ) {
            $success = '{@' . 'MODIFICATION_SUCCESSFUL' . '}';
        }
    } else {
        $error = '{@' . 'INVALID_TOKEN' . '}';
    }
} else if ( $_POST['send_password'] == 1 ) {
    if ($this->checkToken(true)) {
        try {
            $password = $user->update( $user->get('id'), 'password', $_POST['password'], $_POST['confirm-password'] ); 
        } catch (Exception $e) {
            $error = '{@' . $e->getMessage() . '}';
        }
        if ( $password and empty( $error ) ) {
            $success = '{@' . 'MODIFICATION_SUCCESSFUL' . '}';
        }
    } else {
        $error = '{@' . 'INVALID_TOKEN' . '}';
    }
}

$token = $this->getToken();

// refresh user picture
$user->setup();

$content = $page->get('content');
include ( VIEW . 'account.php');
$content .= $html;
