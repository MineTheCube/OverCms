<?php

class App {

    private $db;
    
    public function __construct() {
    }

    public function init() {
        $tables = $this->query('SELECT name FROM sqlite_master WHERE type="table"');
        if ($tables === false)
            $this->throwError('ERROR_SQLITE_INCORRECT');
        if (count($tables->fetchAll(PDO::FETCH_ASSOC)) == 0) {
            $tools = $this->model('Tools');
            $install = $tools->executeSqlFile( DATA.'install.sql' );
            if ($install === false)
                $this->throwError('ERROR_SQLITE_INCORRECT');
        }
    }
    
    public function query($sql, $params = null) {
        if ($params == null) {
            $result = $this->getDatabase()->query($sql);
        }
        else {
            $result = $this->getDatabase()->prepare($sql);
            if (!is_array($params)) {
                $params = array( $params );
            }
            $result->execute($params);
        }
        return $result;
    }
    
    private function getDatabase() {
        if ($this->db === null) {
            if (extension_loaded('pdo_sqlite')) {
                try {
                    $dsn = 'sqlite:' . DATA . 'database.sql';
                    $this->db = new PDO($dsn);
                } catch (Exception $e) {
                    $this->throwError('ERROR_DATABASE');
                }
            } else {
                $this->throwError('ERROR_SQLITE_MISSING');
            }
        }
        return $this->db;
    }
    
    public function getToken() {
        if ($_SESSION['tokenTime'] < (time()-1) or isset($_POST['token'])) {
            $_SESSION['token'] = uniqid(rand('1', '100000'), true);
        }
        $_SESSION['tokenTime'] = time();
        $token = $_SESSION['token'];
        return $this->crypt( $token );
    }
    
    public function checkToken($samepage = false) {
        $token = $_SESSION['token'];
        $sendToken = $this->decrypt( $_POST['token'] );
        $tokenTime = $_SESSION['tokenTime'];
        if ($sendToken !== $token) {
            return false;
        }
        if ((time()-600) >= $tokenTime) {
            echo 'Token Expired';
            die();
            return false;
        }
        return true;
    }

    public function go($url = null) {
        if ( empty($url) ) {
            header('Location: /' . ROOT);
        } else {
            // For debug only
            // echo ('Location: ' . ABS_ROOT . $url . '/');die;
            // throw new Exception('LOCATION_URL');
            header('Location: ' . ABS_ROOT . $url . '/');
        }
        exit();
    }

    public function router() {
        $file = $this->needFile(CORE . 'router' . EX);
        require_once($file);
        return true;
    }

    public function prepare($request) {
        $file = $this->needFile(CONTROLLER . 'page' . EX);
        require_once($file);
        return true;
    }

    public function construct($page, $request) {
        if ($page->get('type') == 'custom') {
            $this->view($page, $page->get('content'));
            return true;
        } else if ($page->get('type') == 'native') {
            $file = $this->needFile(CONTROLLER . $page->get('type_data') . EX);
            require_once($file);
            $this->view($page, $content);
            return true;
        } else if ($page->get('type') == 'plugin') {
            $files = glob(PLUGIN . 'page/*', GLOB_ONLYDIR | GLOB_MARK );
            foreach($files as $file) {
                if (file_exists($file . 'config.cfg.php')) {
                    $pluginConfig = new Config($file . 'config.cfg.php');
                    if ( $page->get('type_data') == $pluginConfig->get('plugin->name') and $pluginConfig->get('plugin->type') == 'page' ) {
                        define('PATH_PLUGIN', $file);
                        define('ABS_PATH_PLUGIN', '/' . $file);
                    }
                }
            }
            if (defined('PATH_PLUGIN') and file_exists(PATH_PLUGIN . 'core' . EX)) {
                $file = $this->needFile(PATH_PLUGIN . 'core' . EX);
                require_once($file);
                unset($files, $file);
                if (empty($content))
                    $content = 'Invalid data.';
                $this->view($page, $content);
                return true;
            } else {
                $this->throwError('ERROR_MISSING_PLUGIN', array('plugin' => $page->get('type_data') ) );

            }
        }
        $this->throwError('UNKNOW_ERROR');
        return false;
    }

    public function needFile($file) {
        if (file_exists($file)) {
            return $file;
        } else {
            $this->throwError('ERROR_MISSING_FILE', array('file' => $file ) );
        }
    }

    protected function view($page, $content) {
        require_once VIEW . 'page' . EX;
        return true;
    }

    public function render($html, $page) {
        require_once MODEL . 'translate' . EXC;
        require_once MODEL . 'view' . EXF;
        return view($html, $page);
    }

    public function model($model) {
        require_once MODEL . strtolower($model) . EXC;
        $model = ucfirst($model);
        return new $model();
    }

    public function loadModels() {
        foreach (glob(MODEL.'*'.EXC) as $filename)
            require_once $filename;
        return true;
    }

    public function bbcode2html($bbcode) {
        require_once MODEL . 'bbcode' . EXF;
        return bbcode2html( $bbcode );
    }
    
    public function crypt($data, $key = null) {
        if ($key === null or empty($key)) {
            $key = UNIQUE_KEY;
        }
        return trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $data, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
    }
    
    public function decrypt($data, $key = null) {
        if ($key === null or empty($key)) {
            $key = UNIQUE_KEY;
        }
        return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, base64_decode($data), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));
    }
    
    public function throwError($error, $args = null) {
        $translate = $this->model('Translate');
        $tools = $this->model('Tools');
        $translate->addPath(LANG);
        echo '<b>' . $translate->get('FATAL_ERROR'). ':</b> ';
        $msgError = $translate->get($error);
        if ($args !== null) {
            $msgError = $tools->parse($msgError, $args);
        }
        echo $msgError;
        exit();
    }

}