<?php

$page = new Page;
$page->setup($request['slug']);

$user = new User;
$user->setup();

if ( $page->get('permission') > $user->get('permission') ) {
    $app = new App;
    if (!$user->auth()) {
        $page_login = $page->getPage(array('type' => 'native', 'type_data' => 'login' ));
        $app->go( $page_login['slug'] );
    } else {
        $page_error = $page->getPage(array('type' => 'native', 'type_data' => 'error' ));
        $app->go( $page_error['slug'] . '/403' );
    }
}


   /******************************************/
  /***/ $this->construct($page, $request); /***/
   /******************************************/

