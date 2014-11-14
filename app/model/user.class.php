<?php

class User {

    private $id;
    private $username;
    private $password;
    private $email;
    private $date_creation;
    private $permission;
    private $picture_url;
    private $profil;
    private $token;
    
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
                $rows = $app->query('SELECT * FROM cms_users where username = ?', array($username))->fetchAll(PDO::FETCH_ASSOC);
                if (count($rows) == 0) {
                    $this->logout();
                } else {
                    $this->auth = true;
                }
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
                    $this->profil = "";
                    $this->token = "";
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
        $config = new Config;
        
        $user_id = $id;
        $user_username = $username;
        $user_password = $password;
        $user_email = $email;
        $user_date_creation = time();
        $user_permission = 1;
        $user_picture_url = "";
        $user_profil = "";
        if ($config->get('user->settings->verifymail', false)) {
            $token = new StdClass();
            $token->key = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 20);
            $token->type = 'confirm-mail'; 
            $token->last = time(); 
            $user_token = json_encode($token);
            
            $page = new Page;
            $mail = new Mail;
            $page_register = $page->getPage(array('type' => 'native', 'type_data' => 'register' ));
            $url = URL . ABS_ROOT;
            $url .= $page_register['slug'];
            $url .= '/' . $user_id . '/' . $token->key;
            $link = '<a href="' . $url . '">' . $url . '</a>';
            $subject = Translate::get('EMAIL_CONFIRMATION_SUBJECT');
            $message = Translate::get('EMAIL_CONFIRMATION_INSTRUCTIONS');
            $message = str_replace('%link%', $link, $message);
            $mail->send($email, $subject, nl2br($message), true);

        } else {
            $user_token = '';
        }
        
        $parameters = array (
            '0' => $user_id,
            '1' => $user_username,
            '2' => $user_password,
            '3' => $user_email,
            '4' => $user_date_creation,
            '5' => $user_permission,
            '6' => $user_picture_url,
            '7' => $user_profil,
            '8' => $user_token
        );

        $query_result = $app->query('INSERT INTO cms_users(id, username, password, email, date_creation, permission, picture_url, profil, token) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)', $parameters);
        
        return true;      
    }
    
    public function login($username, $password, $remember) {
    
        $app = new App;
        $session = new Session;
        
        $rows = $app->query('SELECT id, username, password, token FROM cms_users WHERE username = ?', $username)->fetchAll(PDO::FETCH_ASSOC);
        $user_get = $rows[0];
        $token = json_decode($user_get['token']);
        if (empty($token)) $token = new StdClass;
        
        if (count($rows) !== 1) {
            $error = 'NO_ACCOUNT_MATCHING';
            throw new Exception($error); 
            return false;
        } else if ($user_get['password'] !== hash('sha256', $password . '^#$')) {
            $error = 'PASSWORD_INCORRECT';
            throw new Exception($error); 
            return false;
        } else if (!empty($token->type) and $token->type !== 'recovery') {
            $error = 'MAIL_NOT_CONFIRMED';
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
    
    public function validateToken($token) {
        if ($this->id == 0)
            return false;
        $userToken = json_decode($this->token);
        if (empty($userToken))
            return false;
        if (!empty($userToken->key) and $userToken->key == $token) {
            $app = new App;
            $result = $app->query('UPDATE cms_users SET token = "" WHERE id = ?', array($this->id));
            return $result;
        }
        return false;
    }
    
    public function generatePassword($user_id) {
    
        $page = new Page;
        $app = new App;
        $mail = new Mail;
        $user = new User;
        
        $user->setup($user_id, 'id');
        if ($user->get('id') == 0)
            return false;
    
        $page_login = $page->getPage(array('type' => 'native', 'type_data' => 'login' ));
        $url = URL . ABS_ROOT;
        $url .= $page_login['slug'] . '/';
        $password = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);
        $link = '<a href="' . $url . '">' . $url . '</a>';
        $toEmail = $user->get('email');
        $subject = Translate::get('EMAIL_NEW_PASSWORD_SUBJECT');
        $message = Translate::get('EMAIL_NEW_PASSWORD_INSTRUCTIONS');
        $message = str_replace('%password%', $password, $message);
        $message = str_replace('%link%', $link, $message);
        $mail->send($toEmail, $subject, nl2br($message), true);
        $password = hash('sha256', $password . '^#$');
        $app->query('UPDATE cms_users SET password = ? WHERE id = ?', array($password, $user->get('id')));
        return true;
    }
    
    public function recovery($email) {
    
        $page = new Page;
        $app = new App;
        $mail = new Mail;
        
        $rows = $app->query('SELECT * FROM cms_users WHERE email = ?', $email)->fetchAll(PDO::FETCH_ASSOC);
        $user_get = $rows[0];
        $token = json_decode($user_get['token']);
        if (empty($token)) $token = new StdClass;
        
        if (count($rows) != 1) {
            $error = 'EMAIL_UNKNOW';
            throw new Exception($error); 
            return false;
        } else if (!empty($token->type) and $token->type !== 'recovery') {
            $error = 'MAIL_NOT_CONFIRMED';
            throw new Exception($error);
            return false;
        } else if (!empty($token->last) and $token->last > (time()-300)) {
            $error = 'TOO_RECENT_RECOVERY';
            throw new Exception($error);
            return false;
        } else {
            $page_recovery = $page->getPage(array('type' => 'native', 'type_data' => 'recovery' ));
            $url = URL . ABS_ROOT;
            $url .= $page_recovery['slug'];
            $token = new StdClass();
            $token->key = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 20);
            $token->type = 'recovery';
            $token->last = time();
            $user_token = json_encode($token);
            $url .= '/' . $user_get['id'] . '/' . $token->key;
            $link = '<a href="' . $url . '">' . $url . '</a>';
            $toEmail = $email;
            $subject = Translate::get('EMAIL_RESET_SUBJECT');
            $message = Translate::get('EMAIL_RESET_INSTRUCTIONS');
            $message = str_replace('%link%', $link, $message);
            $mail->send($toEmail, $subject, nl2br($message), true);
            $app->query('UPDATE cms_users SET token = ? WHERE id = ?', array($user_token, $user_get['id']));
            return true;
        }    
        
    }
    
    public function update($user_id, $type, $valueA = null, $valueB = null, $valueC = null, $valueD = null) {
        if (!is_array($valueA))
            $valueA = trim($valueA);
        $valueB = trim($valueB);
        $valueC = trim($valueC);
        $valueD = trim($valueD);
        
        if ($type == 'password') {
            $app = new App;
            $rows = $app->query('SELECT id, username, password FROM cms_users WHERE id = ?', $user_id)->fetchAll(PDO::FETCH_ASSOC);
            $user_get = $rows[0];
            if ($user_get['password'] != hash('sha256', $valueA . '^#$')) {
                $error = 'OLD_PASSWORD_INCORRECT';
                throw new Exception($error); 
                return false;
            }
            if ($valueC != $valueB) {
                $error = 'PASSWORD_NOT_THE_SAME';
                throw new Exception($error); 
                return false;
            } else if (strlen($valueB) < 5 OR strlen($valueB) > 32) {
                throw new Exception('PASSWORD_LENGHT'); 
                return false;
            } else {
                $password = hash('sha256', $valueB . '^#$');
                $app = new App;
                $app->query('UPDATE cms_users SET password = ? WHERE id = ?', array($password, $user_id));
                return true;
            }
        } else if ($type == 'email') {
            $app = new App;
            $rows = $app->query('SELECT id, username, password FROM cms_users WHERE email = ?', $valueA)->fetchAll(PDO::FETCH_ASSOC);
            $user_get = $rows[0];
            if ($valueA != $valueB) {
                $error = 'EMAIL_NOT_THE_SAME';
                throw new Exception($error); 
                return false;
            } else if (!preg_match('/^[[:alnum:][:punct:]]{3,32}@[[:alnum:]-.$nonASCII]{3,32}\.[[:alpha:].]{2,5}$/', $valueA)) {
                throw new Exception('EMAIL_INCORRECT'); 
                return false;
            } else if (count($user_get) > 0) {
                throw new Exception('ALREADY_EMAIL'); 
                return false;
            } else {
                $config = new Config;
                $mail = new Mail;
                $app = new App;
                if ($config->get('user->settings->verifymail', false)) {
                    $token = new StdClass();
                    $token->key = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 20);
                    $token->type = 'new-mail'; 
                    $token->last = time(); 
                    $user_token = json_encode($token);
                    
                    $page = new Page;
                    $page_register = $page->getPage(array('type' => 'native', 'type_data' => 'register' ));
                    $url = URL . ABS_ROOT;
                    $url .= $page_register['slug'];
                    $url .= '/' . $user_id . '/' . $token->key;
                    $link = '<a href="' . $url . '">' . $url . '</a>';
                    $subject = Translate::get('EMAIL_CONFIRMATION_SUBJECT');
                    $message = Translate::get('EMAIL_CONFIRMATION_INSTRUCTIONS');
                    $message = str_replace('%link%', $link, $message);
                    $mail->send($valueA, $subject, nl2br($message), true);
                    $app->query('UPDATE cms_users SET token = ? WHERE id = ?', array($user_token, $user_id));

                }
                $app->query('UPDATE cms_users SET email = ? WHERE id = ?', array($valueA, $user_id));
                return true;
            }
        } else if ($type == 'profil') {
            $app = new App;
            $profil = $app->query('SELECT profil FROM cms_users WHERE id = ?', $user_id)->fetchAll(PDO::FETCH_ASSOC);
            $profil = $profil[0]['profil'];
            if (!empty($profil))
                $profil = json_decode($profil);
            else
                $profil = new StdClass();
            $placeholder = Translate::get('BIRTHDAY_PLACEHOLDER', 'YYYY/MM/DD');
            if (!empty($valueA) or $valueA == $placeholder) {
                if (!preg_match('/^(19|20)[0-9]{2}\/[0-9]{2}\/[0-9]{2}$/', $valueA) or strtotime($valueA) >= time()) {
                    $error = 'INCORRECT_BIRTHDAY';
                    throw new Exception($error); 
                    return false;
                } else {
                    $date = explode('/', $valueA);
                    if (checkdate($date[1], $date[2], $date[0])) {
                        $profil->birthday = implode('-', $date);
                    } else {
                        $error = 'INCORRECT_BIRTHDAY';
                        throw new Exception($error); 
                        return false;
                    }
                }
            } else {
                unset($profil->birthday);
            }
            if (!empty($valueB)) {
                if ($valueB == 'man') {
                    $profil->gender = 'man';
                    $update = true;
                } else if ($valueB == 'woman') {
                    $profil->gender = 'woman';
                }
            } else {
                unset($profil->gender);
            }
            if (!empty($valueC)) {
                if (!preg_match('/^[\p{L}- ]{3,30}$/u', $valueC)) {
                    $error = 'INCORRECT_CITY';
                    throw new Exception($error); 
                    return false;
                } else {
                    $profil->city = $valueC;
                }
            } else {
                unset($profil->city);
            }
            if (!empty($valueD)) {
                if (!preg_match('/^[\p{L}- ]{3,30}$/u', $valueD)) {
                    $error = 'INCORRECT_COUNTRY';
                    throw new Exception($error); 
                    return false;
                } else {
                    $profil->country = $valueD;
                }
            } else {
                unset($profil->country);
            }
            $json = json_encode($profil);
            $result = $app->query('UPDATE cms_users SET profil = ? WHERE id = ?', array($json, $user_id));
            if ($result) {
                return true;
            } else {
                return false;
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
                return true;
            
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
  
    public function getStatus($profil_id) {
        
        $app = new App;
        
        if (!is_numeric($profil_id) and !ctype_digit($profil_id) or $profil_id <= 0) {
            throw new Exception('INVALID_DATA'); 
            return false;
        }
        
        // Get database
        $comments = $app->query('SELECT * FROM cms_user_status WHERE type="comment" AND state = 0 AND profil_id = ? ORDER BY date DESC', $profil_id);
        return $comments;
    }

    public function addStatus($content, $author_id, $profil_id, $date = 0, $state = 0) {
    
        $content = trim($content);
    
        if (strlen($content) < 10 OR strlen($content) > 500) {
            throw new Exception('STATUS_LENGHT'); 
            return false;
        }
        
        if (substr_count( $content, "\n" ) > 5) {
            throw new Exception('TOO_MUCH_NEWLINE'); 
            return false;
        }

        if (!is_numeric($author_id) or $author_id <= 0) {
            throw new Exception('INVALID_DATA'); 
            return false;
        }

        if ((!is_numeric($date) or $date <= 0) and $date !== 0) {
            throw new Exception('INVALID_DATA'); 
            return false;
        }
        
        $app = new App;
        
        $rows = $app->query('SELECT id FROM cms_users WHERE id = ?', $profil_id)->fetchAll(PDO::FETCH_ASSOC);
        $userExist = count($rows);

        if ($userExist !== 1) {
            throw new Exception('UNKNOW_USER'); 
            return false;
        }
        
        $lastPost_req = $app->query('SELECT MAX(date) as LAST FROM cms_user_status WHERE author_id = ?', $author_id);
        $lastPost = $lastPost_req->fetch();
        $lastPost = $lastPost['LAST'];

        if ($lastPost > time()-60) {
            throw new Exception('TOO_RECENT_STATUS'); 
            return false;
        }
        
        $max_req = $app->query('SELECT MAX(id) as MAX FROM cms_user_status WHERE 1');
        $max = $max_req->fetch();
        $id = $max['MAX'] + 1;
        
        if ($date == 0) {
            $date = time();
        }
            
        $array = array(
            'id' => $id,
            'type' => 'comment',
            'author_id' => $author_id,
            'profil_id' => $profil_id,
            'content' => $content,
            'state' => $state,
            'date' => $date
        
        );
        
        $parameters = array();
        foreach($array as $key => $value) {
            $parameters[] = $value;
            if ( isset($query1) ) {
                $query1 .= ', ' . $key;
                $query2 .= ', ?';
            } else {
                $query1 .= $key;
                $query2 .= '?';
            }
        }
 
        $query_result = $app->query('INSERT INTO cms_user_status('.$query1.') VALUES ('.$query2.')', $parameters);
        return true;

    }
  
    public function deleteStatus($id, $profil_id, $checkUserPerm = false) {
        $app = new App;
        $rows = $app->query('SELECT * FROM cms_user_status WHERE id = ?', $id)->fetchAll(PDO::FETCH_ASSOC);
        if (count($rows) !== 1) {
            throw new Exception('UNKNOW_STATUS'); 
            return false;
        }
        
        $query = $rows[0];
        if ($query['profil_id'] != $profil_id) {
            throw new Exception('UNKNOW_STATUS'); 
            return false;
        }
        
        if ($checkUserPerm) {
            $user = new User;
            $page = new Page;
            $user->setup();
            $page->setup();
            if ($user->get('permission') < $page->get('p_edit') and $user->get('id') != $query['profil_id'] and $user->get('id') != $query['author_id']) {
                throw new Exception('NO_PERMISSION'); 
                return false;
            }
        }
        
        $rows = $app->query('DELETE FROM cms_user_status WHERE id = ?', $id);
        if ($rows->rowCount() == 1) {
            return true;
        } else {
            throw new Exception('UNKNOW_STATUS'); 
            return false;
        }
    }

}