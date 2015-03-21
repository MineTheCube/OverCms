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
    const REGEX_EMAIL = '/^[0-9a-zA-Z-_\.]{3,32}\@[0-9a-zA-Z\-\.]{3,32}\.[a-zA-Z]{2,5}$/';
    
    public function __construct() {
    }

    public function setup($username = null, $method = 'username') {
        
        $app = new App;
        $session = new Session;
        
        if (empty($username)) {
            if ($session->get('userAuth') !== false) {
                list($id, $username) = explode(':', $session->get('userAuth'), 2);
                $rows = db()->select()->from('cms_users')->where('username', $username)->andWhere('id', $id)->count();
                if ($rows !== 1) {
                    $this->logout();
                } else {
                    $this->auth = true;
                }
            } else {
                
                $canAutoLogin = false;
                if (isset($_COOKIE['6d08b1ef564521cd891bfaad4b3001ab'], $_COOKIE['5596058f6b09e484c949bc79b1d78008'], $_COOKIE['b953e16cc92aea25727a626512196c8e'])) {
                    
                    $password = $app->decrypt($_COOKIE['6d08b1ef564521cd891bfaad4b3001ab']);
                    $id = $app->decrypt($_COOKIE['5596058f6b09e484c949bc79b1d78008']);
                    $username = $app->decrypt($_COOKIE['b953e16cc92aea25727a626512196c8e']);
                    
                    if (ctype_digit($id) and preg_match(self::REGEX_USERNAME, $username)) {
                        $rows = db()->select()->from('cms_users')->where('id', $id)->andWhere('username', $username)->fetchAll();
                        if (count($rows) === 1) {
                            $autologin = $rows[0];
                            if ($password === hash('sha256', $autologin['password']) and $autologin['id'] === $id and $autologin['username'] === $username) {
                                // Seems legit
                                $session->set('userAuth', $autologin['id'].':'.$autologin['username']);
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
        $rows = db()->select()->from('cms_users')->where($method, $username)->fetchAll();
        
        if (count($rows) === 1) {
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
        return (bool) $this->auth;
    }
    
    public function canView(Page $page) {
        return (
            (int) $this->get('permission')
            >=
            (int) $page->get('p_view')
        );
    }
    
    public function canEdit(Page $page) {
        return (
            (int) $this->get('permission')
            >=
            (int) $page->get('p_edit')
        );
    }
    
    public function create($username, $email, $password, $confirmpassword) {
    
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
        if (!preg_match(self::REGEX_EMAIL, $email)) {
            throw new Exception('EMAIL_INCORRECT'); 
            return false;
        }
        $rows = db()->select('id')->from('cms_users')->where('username', $username)->count();
        if ($rows !== 0) {
            throw new Exception('ALREADY_USERNAME'); 
            return false;
        }
        $rows = db()->select('id')->from('cms_users')->where('email', $email)->count();
        if ($rows !== 0) {
            throw new Exception('ALREADY_EMAIL'); 
            return false;
        }

        $event = new Event(array(
            'username' => $username,
            'password' => $password,
            'email' => $email,
            'date_creation' => time(),
            'picture_url' => '',
            'permission' => 1,
            'profil' => ''
        ));

        EventManager::fire('onUserRegister', $event);

        if ($event->isCancelled() and $event->cancelReason())
            throw new Exception($event->cancelReason());
                
        $user_username = $event->username;
        $user_password = hash('sha256', $event->password . '^#$');
        $user_email = $event->email;
        $user_date_creation = $event->date_creation;
        $user_permission = $event->permission;
        $user_picture_url = $event->picture_url;
        $user_profil = $event->profil;

        if (with(new Config)->get('user.settings.verifymail', false)) {
            $token = new StdClass();
            $token->key = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 20);
            $token->type = 'confirm-mail'; 
            $token->last = time(); 
            $user_token = json_encode($token);
            $sendMail = true;
        } else {
            $user_token = '';
            $sendMail = false;
        }
        
        $parameters = array (
            'username' => $user_username,
            'password' => $user_password,
            'email' => $user_email,
            'date_creation' => $user_date_creation,
            'permission' => $user_permission,
            'picture_url' => $user_picture_url,
            'profil' => $user_profil,
            'token' => $user_token
        );

        $user_id = db()->insert('cms_users')->with($parameters)->run()->lastInsertId();

        if ($sendMail) {
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
        }
        
        return true;      
    }
    
    public function login($username, $password, $remember = false) {
    
        $app = new App;
        $session = new Session;
        
        $rows = db()->select()->from('cms_users')->where('username', $username)->fetchAll();
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
        } else if (!empty($token->type) and $token->type === 'confirm-mail') {
            if (with(new Config)->get('user.settings.verifymail', true)) {
                $error = 'MAIL_NOT_CONFIRMED';
                throw new Exception($error);
                return false;
            } else {
                db()->update('cms_users')->with(array('token' => ''))->where('id', $user_get['id'])->save();
            }
        }

        $event = new Event(array(
            'username' => $username,
            'password' => $password,
            'remember' => $remember
        ));

        EventManager::fire('onUserLogin', $event);

        if ($event->isCancelled() and $event->cancelReason())
            throw new Exception($event->cancelReason());

        if (!empty($token) and $token->type === 'recovery')
            db()->update('cms_users')->with(array('token' => ''))->where('id', $user_get['id'])->save();
        $session->regen();
        $session->set('userAuth', $user_get['id'].':'.$user_get['username']);
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
    
    public function logout() {
        $session = new Session;
        $session->delete('userAuth');
        setCookie('6d08b1ef564521cd891bfaad4b3001ab', null, -1, '/');
        setCookie('5596058f6b09e484c949bc79b1d78008', null, -1, '/');
        setCookie('b953e16cc92aea25727a626512196c8e', null, -1, '/');
        $session->regen();
        return true;
    }
    
    public function validateToken($token) {
        if ($this->id == 0)
            return false;
        $userToken = json_decode($this->token);
        if (empty($userToken))
            return false;
        if (!empty($userToken->key) and $userToken->key == $token) {
            $result = db()->update('cms_users')->with(array('token' => ''))->where('id', $this->id)->run();
            return true;
        }
        return false;
    }

    public function getUsers($search = null) {
        if (empty($search))
            $rows = db()->select()->from('cms_users')->orderBy('permission', 'DESC')->fetchAll();
        else
            $rows = db()->select()->from('cms_users')->where('username', 'LIKE', "%$search%")->orWhere('email', 'LIKE', "%$search%")->orderBy('permission', 'DESC')->fetchAll();
        return $rows;
    }

    public function adminUpdate($id, $username, $email, $permission) {
        if (!ctype_digit($id) and !is_int($id)) return false;
        if (!ctype_digit($permission) and !is_int($permission)) return false;

        if (strlen($username) < 3 OR strlen($username) > 16) {
            throw new Exception('USERNAME_LENGHT'); 
            return false;
        }
        if (!preg_match(self::REGEX_USERNAME, $username)) {
            throw new Exception('USERNAME_INCORRECT'); 
            return false;
        }
        if (!preg_match(self::REGEX_EMAIL, $email)) {
            throw new Exception('EMAIL_INCORRECT'); 
            return false;
        }
        $rows = db()->select('id')->from('cms_users')->where('username', $username)->fetchAll();
        if (count($rows) > 1 or (count($rows) === 1 and $rows[0]['id'] !== (string) $id)) {
            throw new Exception('ALREADY_USERNAME'); 
            return false;
        }
        $rows = db()->select('id')->from('cms_users')->where('email', $email)->fetchAll();
        if (count($rows) > 1 or (count($rows) === 1 and $rows[0]['id'] !== (string) $id)) {
            throw new Exception('ALREADY_EMAIL'); 
            return false;
        }

        $user = new User;
        $user->setup();

        $row = db()->select()->from('cms_users')->where('id', $id)->fetch();
        if ($row['permission'] == 9) {
            if ($user->get('permission') <= 4) {
                throw new Exception('NO_PERMISSION');
                return false;
            }
        }

        if ($user->get('permission') <= 4 and $permission > 4) {
                throw new Exception('NO_PERMISSION');
                return false;
        }

        $result = db()->update('cms_users')->with(array(
            'username' => $username,
            'email' => $email,
            'permission' => $permission
            ))->where('id', $id)->run(true);

        return $result->rowCount() >= 1;
    }

    public function delete($id) {
        if (!ctype_digit($id) and !is_int($id)) return false;

        $row = db()->select()->from('cms_users')->where('id', $id)->fetch();
        if (empty($row))
            return false;

        if ($row['permission'] == 9) {
            throw new Exception('CANT_DELETE_USER');
            return false;
        }

        $event = new Event($row);
        $event->setCancellable(false);

        EventManager::fire('onUserDelete', $event);

        db()->delete('cms_user_status')->where('id', $id)->run();
        $result = db()->delete('cms_users')->where('id', $id)->run(true);
        if ($result->rowCount() >= 1) {
            if ( file_exists( AVATAR . $id . '.png' ) ) {
                @unlink( AVATAR . $id . '.png' );
            } else if ( file_exists( AVATAR . $id . '.jpg' ) ) {
                @unlink( AVATAR . $id . '.jpg' );
            } else if ( file_exists( AVATAR . $id . '.jpeg' ) ) {
                @unlink( AVATAR . $id . '.jpeg' );
            }
            return true;
        }
        return false;
    }
    
    public function generatePassword($user_id) {
    
        $page = new Page;
        $mail = new Mail;
        $user = new User;
        
        $result = $user->setup($user_id, 'id');
        if ($user->get('id') == 0 or $result === false)
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
        db()->update('cms_users')->with(array(
            'password' => $password
        ))->where('id', $user->get('id'))->run();
        return true;
    }
    
    public function recovery($email) {
    
        $page = new Page;
        $mail = new Mail;
        
        $rows = db()->select()->from('cms_users')->where('email', $email)->fetchAll();
        $user_get = $rows[0];
        $token = json_decode($user_get['token']);
        if (empty($token)) $token = new StdClass;
        
        if (count($rows) != 1) {
            $error = 'EMAIL_UNKNOW';
            throw new Exception($error); 
            return false;
        } else if (!empty($token->type) and $token->type === 'confirm-mail') {
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
            db()->update('cms_users')->with(array(
                'token' => $user_token
            ))->where('id', $user_get['id'])->run();
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
            $user_get = db()->select()->from('cms_users')->where('id', $user_id)->fetch();
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
                db()->update('cms_users')->with(array(
                    'password' => $password
                ))->where('id', $user_id)->run();
                return true;
            }
        } else if ($type == 'email') {
            $user_get = db()->select()->from('cms_users')->where('email', $valueA)->count();
            if ($valueA != $valueB) {
                $error = 'EMAIL_NOT_THE_SAME';
                throw new Exception($error); 
                return false;
            } else if (!preg_match(self::REGEX_EMAIL, $valueA)) {
                throw new Exception('EMAIL_INCORRECT'); 
                return false;
            } else if ($user_get > 0) {
                throw new Exception('ALREADY_EMAIL'); 
                return false;
            } else {
                if (with(new Config)->get('user.settings.verifymail', false)) {
                    $mail = new Mail;
                    $token = new StdClass();
                    $token->key = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 20);
                    $token->type = 'confirm-mail'; 
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
                    db()->update('cms_users')->with(array(
                        'token' => $user_token
                    ))->where('id', $user_id)->run();
                }
                db()->update('cms_users')->with(array(
                    'email' => $valueA
                ))->where('id', $user_id)->run();
                return true;
            }
        } else if ($type == 'profil') {
            $profil = db()->select('profil')->from('cms_users')->where('id', $user_id)->fetch();
            $profil = $profil['profil'];
            if (!empty($profil))
                $profil = json_decode($profil);
            else
                $profil = new StdClass();
            if (!empty($valueA) or $valueA == 'YYYY/MM/DD') {
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
            $result = db()->update('cms_users')->with(array(
                'profil' => $json
            ))->where('id', $user_id)->run();
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

                if ($imageinfo[0] > 300 OR $imageinfo[1] > 300 or $imageinfo[0] < 30 OR $imageinfo[1] < 30) {
                    throw new Exception('PICTURE_SIZE');
                    return false;
                }
                
                db()->update('cms_users')->with(array(
                    'picture_url' => $src
                ))->where('id', $user_id)->run();
                
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
                
                db()->update('cms_users')->with(array(
                    'picture_url' => ''
                ))->where('id', $user_id)->run();
                
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
        
        if (!is_int($profil_id) and !ctype_digit($profil_id) or $profil_id <= 0) {
            throw new Exception('INVALID_DATA'); 
            return false;
        }
        
        // Get database
        $comments = db()->select()->from('cms_user_status')
                        ->where('type', 'comment')->andWhere('state', 0)->andWhere('profil_id', $profil_id)
                        ->orderBy('date', 'DESC')->fetchAll();
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
        
        $userExist = db()->select('id')->from('cms_users')->where('id', $profil_id)->count();

        if ($userExist !== 1) {
            throw new Exception('UNKNOW_USER'); 
            return false;
        }
        
        $lastPost = db()->select('MAX(date) as LAST')->from('cms_user_status')->where('author_id', $author_id)->fetch();
        $lastPost = $lastPost['LAST'];

        if ($lastPost > time()-60) {
            throw new Exception('TOO_RECENT_STATUS'); 
            return false;
        }
        
        if ($date == 0) {
            $date = time();
        }
            
        return (bool) db()->insert('cms_user_status')->with(array(
            'type' => 'comment',
            'author_id' => $author_id,
            'profil_id' => $profil_id,
            'content' => $content,
            'state' => $state,
            'date' => $date
        ))->run(true);

    }
  
    public function deleteStatus($id, $profil_id, $checkUserPerm = false) {
        $rows = db()->select()->from('cms_user_status')->where('id', $id)->fetchAll();
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
        
        $rows = db()->delete('cms_user_status')->where('id', $id)->count();
        if ($rows == 1) {
            return true;
        } else {
            throw new Exception('UNKNOW_STATUS'); 
            return false;
        }
    }

}