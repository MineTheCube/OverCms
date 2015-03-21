<?php

$page = new Page;
$page->setup();

$user = new User;
$user->setup();

$parentRestrict = false;
$parentId = (int) $page->get('parent_id');

if ($parentId !== 0) {
    $parentPage = new Page;
    $parentPage->setup($parentId, 'id', 'public');
    if (!$user->canView($parentPage)) {
        $parentRestrict = true;
    }
}

if (!$user->canView($page) or $parentRestrict) {
    if (!$user->auth()) {
        $page_login = $page->getPage(array('type' => 'native', 'type_data' => 'login' ));
        go($page_login['slug']);
    } else {
        $page_error = $page->getPage(array('type' => 'native', 'type_data' => 'error' ));
        go($page_error['slug'] . '/403');
    }
}

   /******************************************/
  /***/ $this->construct($page, $request); /***/
   /******************************************/

