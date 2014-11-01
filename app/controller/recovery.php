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