<?php

class User {

    private $id;
    private $username;
    private $password;
    private $email;
    private $date_creation;
    private $permission;
    private $picture_url;
    
    private $auth;
    private $picture;
    
    const REGEX_USERNAME = '/^[a-zA-Z0-9_]+$/';
    
    public function __construct() {
    }

    public function setup($username = null, $method = 'username') {
        
        $app = new App;
        $session = new Session;
        
        if (empty($username)) {
            if ($session->get('username') !== false) {
                $username = $session->get('username');
                $this->auth = true;
            } else {
                
                $canAutoLogin = false;
                if (isset ($_COOKIE['6d08b1ef564521cd891bfaad4b3001ab'], $_COOKIE['5596058f6b09e484c949bc79b1d78008'], $_COOKIE['b953e16cc92aea25727a626512196c8e'])) {
                    
                    $password = $app->decrypt($_COOKIE['6d08b1ef564521cd891bfaad4b3001ab']);
                    $id = $app->decrypt($_COOKIE['5596058f6b09e484c949bc79b1d78008']);
                    $username = $app->decrypt($_COOKIE['b953e16cc92aea25727a626512196c8e']);
                    
                    if (ctype_digit($id) and preg_match(self::REGEX_USERNAME, $username)) {
                        $rows = $app->query('SELECT * FROM cms_users where id = ? AND username = ?', array($id, $username))->fetchAll(PDO::FETCH_ASSOC);
                        if (count($rows) === 1) {
                            $autologin = $rows[0];
                            if ($password === hash('sha256', $autologin['password']) and $autologin['id'] === $id and $autologin['username'] === $username) {
                                // Seems legit
                                $session->set('username', $autologin['username']);
                                $canAutoLogin = true;
                            }
                        }
                    }
                }
                
                if ($canAutoLogin === false) {   
                    if (isset($_COOKIE['6d08b1ef564521cd891bfaad4b3001ab']) or isset($_COOKIE['5596058f6b09e484c949bc79b1d78008']) or isset($_COOKIE['b953e16cc92aea25727a626512196c8e'])) {    
                        setCookie('6d08b1ef564521cd891bfaad4b3001ab', null, -1, '/');
                        setCookie('5596058f6b09e484c949bc79b1d78008', null, -1, '/');
                        setCookie('b953e16cc92aea25727a626512196c8e', null, -1, '/');
                    }
                    $this->auth = false;
                    $this->id = 0;
                    $this->username = "";
                    $this->password = "";
                    $this->email = "";
                    $this->date_creation = 0;
                    $this->permission = 0;
                    $this->picture_url = "";
                    $this->picture = HTTP_ROOT . AVATAR . '0' . '.png';
                    return false;
                }
            }
        }
        
        // Get database
        $rows = $app->query('SELECT * FROM cms_users where '.$method.' = ?', array($username))->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($rows) == 1) {
            $user = $rows[0];
            foreach($user as $key => $value) {
                $this->$key = $value;
            }
            // Update variables
            $picture_url = $this->picture_url;
            if ( !empty( $picture_url ) ) {
                $userPicture = $picture_url;
            } else if ( file_exists( AVATAR . $this->id . '.png' ) ) {
                $userPicture = HTTP_ROOT . AVATAR . $this->id . '.png';
            } else if ( file_exists( AVATAR . $this->id . '.jpg' ) ) {
                $userPicture = HTTP_ROOT . AVATAR . $this->id . '.jpg';
            } else if ( file_exists( AVATAR . $this->id . '.jpeg' ) ) {
                $userPicture = HTTP_ROOT . AVATAR . $this->id . '.jpeg';
            } else {
                $userPicture = HTTP_ROOT . AVATAR . '0' . '.png';
            }
            $this->picture = $userPicture;
            return true;
        } else {
            return false;
        }
    }
    
    public function get($index) {
        if (isset($this->$index)) {
            return $this->$index;
        } else {
            return false;
        }
    }
    
    public function auth() {
        return $this->auth;
    }
    
    public function create($username, $email, $password, $confirmpassword) {
    
        $app = new App;
        
        if ($password != $confirmpassword) {
            throw new Exception('PASSWORD_NOT_THE_SAME'); 
            return false;
        }
        if (strlen($username) < 3 OR strlen($username) > 16) {
            throw new Exception('USERNAME_LENGHT'); 
            return false;
        }
        if (!preg_match(self::REGEX_USERNAME, $username)) {
            throw new Exception('USERNAME_INCORRECT'); 
            return false;
        }
        if (strlen($password) < 5 OR strlen($password) > 32) {
            throw new Exception('PASSWORD_LENGHT'); 
            return false;
        }
        if (!preg_match('/^[[:alnum:][:punct:]]{3,32}@[[:alnum:]-.$nonASCII]{3,32}\.[[:alpha:].]{2,5}$/', $email)) {
            throw new Exception('EMAIL_INCORRECT'); 
            return false;
        }
        $rows = $app->query('SELECT id FROM cms_users WHERE username = ?', $username)->fetchAll(PDO::FETCH_ASSOC);
        if (count($rows) !== 0) {
            throw new Exception('ALREADY_USERNAME'); 
            return false;
        }
        $rows = $app->query('SELECT id FROM cms_users WHERE email = ?', $email)->fetchAll(PDO::FETCH_ASSOC);
        if (count($rows) !== 0) {
            throw new Exception('ALREADY_EMAIL'); 
            return false;
        }
        
        
        $max_req = $app->query('SELECT MAX(id) as MAX FROM cms_users WHERE 1');
        $max = $max_req->fetch();
        $id = $max['MAX'] + 1;
        
        $password = hash('sha256', $password . '^#$');
        
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
        $this->date_creation = time();
        $this->permission = 1;
        $this->picture_url = "";
        
        $this->auth = true;
        $this->picture = HTTP_ROOT . AVATAR . '0' . '.png';
        
        $parameters = array (
            '0' => $this->id,
            '1' => $this->username,
            '2' => $this->password,
            '3' => $this->email,
            '4' => $this->date_creation,
            '5' => $this->permission,
            '6' => $this->picture_url
        );

        $query_result = $app->query('INSERT INTO cms_users(id, username, password, email, date_creation, permission, picture_url) VALUES (?, ?, ?, ?, ?, ?, ?)', $parameters);
        
        return true;      
    }
    
    public function login($username, $password, $remember) {
    
        $app = new App;
        $session = new Session;
        
        $rows = $app->query('SELECT id, username, password FROM cms_users WHERE username = ?', $username)->fetchAll(PDO::FETCH_ASSOC);
        $user_get = $rows[0];
        
        if (count($rows) !== 1) {
            $error = 'NO_ACCOUNT_MATCHING';
            throw new Exception($error); 
            return false;
        }
        else if ($user_get['password'] !== hash('sha256', $password . '^#$')) {
            $error = 'PASSWORD_INCORRECT';
            throw new Exception($error); 
            return false;
        } else {
            $session->regen(true);
            $session->set('username', $username);
            if ($remember === true) {
                $date = time() + 3600*24*14;
                $cookiePassword = $app->crypt( hash('sha256', $user_get['password']) );
                $cookieId =       $app->crypt(                $user_get['id']        );
                $cookieUsername = $app->crypt(                $user_get['username']  );
                setCookie('6d08b1ef564521cd891bfaad4b3001ab', $cookiePassword, $date , '/');
                setCookie('5596058f6b09e484c949bc79b1d78008', $cookieId,       $date , '/');
                setCookie('b953e16cc92aea25727a626512196c8e', $cookieUsername, $date , '/');
            }
            return true;
        }    
        
    }
    
    public function logout() {
        unset( $_SESSION['username'] );
        setCookie('6d08b1ef564521cd891bfaad4b3001ab', null, -1, '/');
        setCookie('5596058f6b09e484c949bc79b1d78008', null, -1, '/');
        setCookie('b953e16cc92aea25727a626512196c8e', null, -1, '/');
        session_regenerate_id(true);
        return true;
    }
    
    public function recovery($email) {
    
        $app = new App;
        $config = new Config;
        $page = new Page;
        
        $rows = $app->query('SELECT * FROM cms_users WHERE email = ?', $email)->fetchAll(PDO::FETCH_ASSOC);
        $user_get = $rows[0];
        
        if (count($rows) != 1) {
            $error = 'EMAIL_UNKNOW';
            throw new Exception($error); 
            return false;
        } else {
            $mail = $app->model('mail');
            $page_recovery = $page->getPage(array('type' => 'native', 'type_data' => 'recovery' ));
            $url = URL . ABS_ROOT;
            $url .= $page_recovery['slug'];
            $url .= '/' . md5( $user_get['password'] . $user_get['email'] . $user_get['date_creation'] ) . $user_get['id'] . '/';
            $link = '<a href="' . $url . '">' . $url . '</a>';
            $toEmail = $email;
            $fromEmail = $config->get('user->info->email');
            $fromName = $config->get('user->info->name');
            $subject = Translate::get('EMAIL_RESET_SUBJECT');
            $message = Translate::get('EMAIL_RESET_INSTRUCTIONS');
            $message = str_replace('%link%', $link, $message);
            $mail->send($toEmail, $fromEmail, $fromName, $subject, nl2br($message), true);
            return true;
        }    
        
    }
    
    public function update($user_id, $type, $valueA = null, $valueB = null, $valueC = null) {
    
        if ($type == 'password') {
            if ($valueA != $valueB) {
                $error = 'PASSWORD_NOT_THE_SAME';
                throw new Exception($error); 
                return false;
            } else if (strlen($valueA) < 5 OR strlen($valueA) > 32) {
                throw new Exception('PASSWORD_LENGHT'); 
                return false;
            } else {
                $password = hash('sha256', $valueA . '^#$');
                $app = new App;
                $app->query('UPDATE cms_users SET password = ? WHERE id = ?', array($password, $user_id));
                return true;
            }
        } else if ($type == 'avatar') {
        
            if (!is_array( $valueA ) ) {
                
                $src = $valueA;
                if (strpos($src, 'http://') === false) {
                    throw new Exception('INVALID_URL');
                    return false;
                }
                
                $file_ext = strrchr($src, '.');

                // check if its allowed or not
                $whitelist = array(
                    ".jpg",
                    ".jpeg",
                    ".png"
                );

                if (!(in_array($file_ext, $whitelist))) {
                    throw new Exception('WRONG_PICTURE_FORMAT');
                    return false;
                }

                if (!(@fopen($src, 'r'))) {
                    throw new Exception('PICTURE_DOESNT_EXIST');
                    return false;
                }
                
                $pic = exif_imagetype ( $src );
                
                if (!($pic == 2 or $pic == 3)) {
                    throw new Exception('WRONG_PICTURE_FORMAT');
                    return false;
                }

                $imageinfo = getimagesize($src);

                if ($imageinfo['mime'] != 'image/gif' && $imageinfo['mime'] != 'image/jpeg' && $imageinfo['mime'] != 'image/jpg' && $imageinfo['mime'] != 'image/png') {
                    throw new Exception('NOT_A_PICTURE');
                    return false;
                }

                if ($imageinfo[0] > 300 OR $imageinfo[1] > 300) {
                    throw new Exception('PICTURE_SIZE');
                    return false;
                }
                
                $app = new App;
                $app->query('UPDATE cms_users SET picture_url = ? WHERE id = ?', array($src, $user_id));
                
                if ( file_exists( AVATAR . $user_id . '.png' )) { unlink( AVATAR . $user_id . '.png'  ); }
                if ( file_exists( AVATAR . $user_id . '.jpg' )) { unlink( AVATAR . $user_id . '.jpg'  ); }
                if ( file_exists( AVATAR . $user_id . '.jpeg')) { unlink( AVATAR . $user_id . '.jpeg' ); }
            
            
            } else {
        
                $filename = $valueA['name'];
                $filetype = $valueA['type'];
                $filename = strtolower($filename);
                $filetype = strtolower($filetype);

                // check if contain php and kill it
                $pos = strpos($filename, 'php');

                if (!($pos === false)) {
                    throw new Exception('NOT_A_PICTURE');
                    return false;
                }

                // get the file ext
                $file_ext = strrchr($filename, '.');

                // check if its allowed or not
                $whitelist = array(
                    ".jpg",
                    ".jpeg",
                    ".png"
                );

                if (!(in_array($file_ext, $whitelist))) {
                    throw new Exception('WRONG_PICTURE_FORMAT');
                    return false;
                }

                // check upload size
                if ($valueA["size"] > 100*1024) {
                    throw new Exception('PICTURE_FILE_SIZE');
                    return false;
                }

                // check upload type
                $pos = strpos($filetype, 'image');
                if ($pos === false) {
                    throw new Exception('NOT_A_PICTURE');
                    return false;
                }

                $imageinfo = getimagesize($valueA['tmp_name']);

                if ($imageinfo['mime'] != 'image/gif' && $imageinfo['mime'] != 'image/jpeg' && $imageinfo['mime'] != 'image/jpg' && $imageinfo['mime'] != 'image/png') {
                    throw new Exception('NOT_A_PICTURE');
                    return false;
                }

                if ($imageinfo[0] > 300 OR $imageinfo[1] > 300) {
                    throw new Exception('PICTURE_SIZE');
                    return false;
                }

                // check double file type (image with comment)
                if (substr_count($filetype, '/') > 1) {
                    throw new Exception('NOT_A_PICTURE');
                    return false;
                }
                
                if ( file_exists( AVATAR . $user_id . '.png' )) { unlink( AVATAR . $user_id . '.png'  ); }
                if ( file_exists( AVATAR . $user_id . '.jpg' )) { unlink( AVATAR . $user_id . '.jpg'  ); }
                if ( file_exists( AVATAR . $user_id . '.jpeg')) { unlink( AVATAR . $user_id . '.jpeg' ); }
                
                $app = new App;
                $app->query('UPDATE cms_users SET picture_url = "" WHERE id = ?', array($user_id));
                
                if (move_uploaded_file($valueA['tmp_name'], AVATAR . $user_id . $file_ext)) {
                    return true;
                }
                else {
                    throw new Exception('UNKNOW_ERROR');
                    return false;
                }
            
            }
        
        }
        
    }
}