<?php

Class Session {
    
    public function start() {
        session_set_cookie_params (0, HTTP_ROOT, '', isset($_SERVER["HTTPS"]), true);
        session_start();
        $_SESSION['lastPage'] = $_SESSION['currentPage'];
        $_SESSION['currentPage'] = $_SERVER['REQUEST_URI'];
    }
    
    public function regen($delete_old_session = false) {
        return session_regenerate_id($delete_old_session);
    }
    
    public function get($index) {
        if (isset($_SESSION[$index])) {
            return $_SESSION[$index];
        } else {
            return false;
        }
    }
    
    public function set($index, $value) {
        $_SESSION[$index] = $value;
        return true;
    }
    
    public function delete() {
        session_destroy();
        return true;
    }
    
}