<?php

$this->model('page');

$page = new Page;
$page->setup($request['slug']);

$user = $this->model('user');
$user->setup();

if ($user->auth()) {
    $user->logout();
}

$this->go();

   /**********************************************/
  /***//* $this->construct($page, $request); *//***/
   /**********************************************/

