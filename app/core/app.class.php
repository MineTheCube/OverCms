<?php

class App {

    private $db;
    
    public function __construct() {
    }

    public function init() {
        if (!function_exists('db')){function db($a=0){static$b;if($a&&!$b)$b=$a;return$b;}}
        require_once (CORE.'database'.EXC);
        $db = new Database;
        $config = new Config;
        $type = $config->get('database.type', 'sqlite');
        if (DEBUG)
            $options = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING);
        else
            $options = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT);
        if ($type == 'mysql') {
            if (extension_loaded('pdo_mysql')) {
                $host = $config->get('database.host');
                $dbname = $config->get('database.dbname');
                $user = $config->get('database.user');
                $password = $config->get('database.password');
                $port = $config->get('database.port', '3306');
                try {
                    $db->init('mysql', array($host, $dbname, $user, $password, $port), $options);
                    $tables = $db->query('SHOW TABLES LIKE "cms\_%"');
                    if ($tables === false or count($tables->fetchAll(PDO::FETCH_ASSOC)) == 0) {
                        $this->tempInstall($db);
                        exit('Installation du CMS en cours..');
                    }
                    $tables = $db->query('SHOW TABLES LIKE "cms\_%"');
                    if ($tables === false or count($tables->fetchAll(PDO::FETCH_ASSOC)) == 0) {
                        $this->throwError('ERROR_MYSQL_INCORRECT');
                    }
                    return db($db);
                } catch (Exception $e) {
                    $this->throwError('ERROR_MYSQL_DATABASE');
                }
            } else {
                $this->throwError('ERROR_MYSQL_MISSING');
            }
        } else if ($type == 'sqlite') {
            if (extension_loaded('pdo_sqlite')) {
                try {
                    $db->init('sqlite', DATA.'database.sql', $options);
                    $tables = $db->query('SELECT name FROM sqlite_master WHERE type="table" AND name LIKE "cms_%"');
                    if ($tables === false or count($tables->fetchAll(PDO::FETCH_ASSOC)) == 0) {
                        $this->tempInstall($db);
                        exit('Installation du CMS en cours..');
                    }
                    $tables = $db->query('SELECT name FROM sqlite_master WHERE type="table" AND name LIKE "cms_%"');
                    if ($tables === false or count($tables->fetchAll(PDO::FETCH_ASSOC)) == 0) {
                        $this->throwError('ERROR_SQLITE_INCORRECT');
                    }
                    return db($db);
                } catch (Exception $e) {

                    dump($e->getMessage());
                    $this->throwError('ERROR_SQLITE_DATABASE');
                }
            } else {
                $this->throwError('ERROR_SQLITE_MISSING');
            }
        }
        $this->throwError('ERROR_CONFIG_DATABASE');
    }

    public function tempInstall($db) {
        $file = $this->needFile(ADMIN.'adminer.sql');
        $sql = file_get_contents($file);
        $stmt = $db->getPDO()->exec($sql);
    }

    public function query($sql, $params = null) {
        throw new Exception('App->query is depreciated');
        exit('App->query is depreciated');
        return db()->query($sql, $params);
    }
    
    public function getToken($refresh = true) {
        if ($refresh) {
            if ($_SESSION['tokenTime'] < (time()-1) or isset($_POST['token'])) {
                $_SESSION['token'] = uniqid(rand('1', '100000'), true);
            }
            $_SESSION['tokenTime'] = time();
        }
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
            return false;
        }
        return true;
    }

    public function router() {
        $file = $this->needFile(CORE . 'router' . EX);
        require_once($file);
        return true;
    }

    public function start($request) {
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
            $this->view($page, '%PAGE_CONTENT%');
            return true;
        }
        $this->throwError('UNKNOW_ERROR');
        return false;
    }

    public function needFile($file) {
        if (file_exists($file) and is_file($file)) {
            return $file;
        } else {
            $this->throwError('ERROR_MISSING_FILE', array('file' => $file ) );
        }
    }

    protected function view($page, $content) {
        require_once MODEL . 'view' . EXF;
        echo view($content, $page);
        return true;
    }

    public function model($model) {
        require_once MODEL . strtolower($model) . EXC;
        $model = ucfirst($model);
        return new $model();
    }

    public function package($package) {
        if (file_exists(PACKAGE . strtolower($package) . '/core.php')) {
            require_once PACKAGE . strtolower($package) . '/core.php';
            return true;
        }
        return false;
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

    public function getTextEditor($name = 'texteditor', $content = '') {
        $texteditor = file_get_contents( TEMPLATE . CURRENT_TEMPLATE . '/misc/texteditor.htm' );
        $texteditor = str_replace('%TEXT_EDITOR_NAME%', $name, $texteditor);
        $texteditor = str_replace('%TEXT_EDITOR_CONTENT%', $content, $texteditor);
        return $texteditor;
    }

    public function getPlugins($onlyInstalled = true, $refresh = false) {
        static $installedPlugins;
        static $uninstalledPlugins;

        if (!isset($installedPlugins) or !isset($uninstalledPlugins) or $refresh) {
            $plugins = $this->loadPlugins();
            $installedPlugins = $plugins[0];
            $uninstalledPlugins = $plugins[1];
        }

        if ($onlyInstalled)
            return $installedPlugins;
        else
            return ($installedPlugins + $uninstalledPlugins);
    }

    public function getPlugin($name) {
        $plugins = $this->getPlugins();
        if (isset($plugins[$name]))
            return $plugins[$name];
        return false;
    }

    public function refreshPlugins() {
        $this->getPlugins(false, true);
    }

    public function loadPlugins() {
        $template = array();
        $pluginDependecies = array();
        $plugins = array();
        $installedPlugins = array();

        // Get Templates of plugins
        $templatePlugins = glob(TEMPLATE . CURRENT_TEMPLATE . '/plugins/*', GLOB_ONLYDIR | GLOB_MARK );
        foreach($templatePlugins as $templatePlugin) {
            $pluginName = substr(str_replace(TEMPLATE . CURRENT_TEMPLATE . '/plugins/', '', $templatePlugin), 0, -1);
            if (!ctype_alnum(str_replace('_', '', $pluginName))) continue;
            $pluginName = str_replace('_', ' ', $pluginName);
            if (file_exists( $templatePlugin . CONFIG)) {
                $templateConfig = new Config( $templatePlugin . CONFIG );
                $name = $templateConfig->get('plugin.name', false);
                if ($name !== $pluginName) continue;
                if (!isset($template[$name]) and $name) {
                    $template[$name] = array(
                        'version' => $templateConfig->get('plugin.version'),
                        'path' => $templatePlugin
                    );
                }
            }
        }

        // Get Plugins
        $config = new Config;
        $pluginsFolder = glob(PLUGIN . '*', GLOB_ONLYDIR | GLOB_MARK );
        if (is_array($pluginsFolder)) {
            foreach ($pluginsFolder as $plugin) {
                $pluginName = substr(str_replace(PLUGIN, '', $plugin), 0, -1);
                if (!ctype_alnum(str_replace('_', '', $pluginName))) continue;
                $pluginName = str_replace('_', ' ', $pluginName);
                if ((file_exists($plugin . 'core' . EX) or file_exists($plugin . 'api' . EX)) and file_exists($plugin . CONFIG)) {
                    try {
                        $pluginConfig = new Config($plugin . CONFIG);
                        $name = $pluginConfig->get('plugin.name', false);
                        if ($name !== $pluginName) continue;
                        if (!isset($plugins[$name]) and $name) {
                            $data = array();
                            $data['name'] = $name;
                            $data['installed'] = false;
                            $data['config'] = null;
                            $data['api_compatible'] = $pluginConfig->get('plugin.api_compatible', '0');
                            $data['depend'] = true;
                            $depend = $pluginConfig->get('depend', false);
                            if ($depend)
                                $pluginDependecies[$name] = $depend;
                            $type = $pluginConfig->get('plugin.type', 'custom');
                            if ($type === 'header')
                                $data['type'] = 'header';
                            else if ($type === 'page')
                                $data['type'] = 'page';
                            else if ($type === 'sidebar')
                                $data['type'] = 'sidebar';
                            else
                                $data['type'] = 'custom';
                            $data['lang'] = array(
                                'translation' => (bool) $pluginConfig->get('lang.translation', false),
                                'default_language' => $pluginConfig->get('lang.default_language', 'en_US')
                            );
                            $data['path'] = $plugin;
                            $data['compatible'] = false;
                            if ($config->exists('cms.compatible.'.$type) and
                                version_compare( $config->get('cms.version'), $pluginConfig->get('plugin.require.cms'), '>=' ) and
                                version_compare($pluginConfig->get('plugin.require.cms'), $config->get('cms.compatible.'.$type), '>=') )
                                    $data['compatible'] = true;
                            $data['version'] = $pluginConfig->get('plugin.version');
                            if ($pluginConfig->exists('plugin.require.template') and version_compare( $pluginConfig->get('plugin.require.template'), $template[$name]['version'], "<=" ))
                                    $data['template'] = $template[$name]['path'] . 'template/';
                            else
                                $data['template'] = $data['path'].'template/';
                            $plugins[$name] = $data;
                        }
                    } catch (Exception $e) {}
                }
            }
        }

        // Get list of installed plugins
        $installed = db()->select()->from('cms_extensions')->where('type', 'plugin')->fetchAll();
        foreach ($installed as $k => $p) {
            if (!isset($plugins[$p['name']]) or $plugins[$p['name']]['name'] !== $p['name'])
                continue;
            $plugins[$p['name']]['installed'] = true;
            $plugins[$p['name']]['config'] = $p['config'];
            foreach (explode(';', $p['events']) as $event) {
                if (!empty($event))
                    EventManager::listen($p['name'], $plugins[$p['name']], $event);
            }
            $installedPlugins[$p['name']] = $plugins[$p['name']];
            unset($plugins[$p['name']]);
            unset($installed[$k]);
        }

        // Remove plugin in database but not in files
        if (is_array($installed) and !empty($installed))
            foreach ($installed as $plugin)
                db()->delete('cms_extensions')->where('type', 'plugin')->andWhere('name', $plugin['name'])->save();

        // Manage dependencies
        foreach ($pluginDependecies as $name => $dependencies) {
            if (!isset($installedPlugins[$name]))
                continue;

            foreach ($dependencies as $depend_name => $depend_config) {
                $hasDepend = false;
                if (isset($installedPlugins[$depend_name]) and 
                    version_compare($installedPlugins[$depend_name]['version'], $depend_config['version'], '>=') and
                    version_compare($depend_config['version'], $installedPlugins[$depend_name]['api_compatible'], '>=')) {
                    if (!is_array($installedPlugins[$name]['depend']))
                        $installedPlugins[$name]['depend'] = array();
                    $installedPlugins[$name]['depend'][$depend_name] = $installedPlugins[$depend_name];
                } else {
                    if ($depend_config['required']) {
                        $installedPlugins[$name]['depend'] = false;
                        continue 2;
                    } else {
                        if (!is_array($installedPlugins[$name]['depend']))
                            $installedPlugins[$name]['depend'] = array();
                        $installedPlugins[$name]['depend'][$depend_name] = false;                        
                    }
                }
            }
        }

        ksort($installedPlugins);
        ksort($plugins);
        return array(
            $installedPlugins,
            $plugins // = uninstalledPlugins
        );
    }
    
    public function crypt($data, $key = null) {
        return trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key ? $key : UNIQUE_KEY, $data, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
    }
    
    public function decrypt($data, $key = null) {
        return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key ? $key : UNIQUE_KEY, base64_decode($data), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));
    }
    
    public function throwError($error, $args = null) {
        $translate = $this->model('Translate');
        $translate->addPath(LANG);
        echo '<b>' . $translate->get('FATAL_ERROR'). ':</b> ';
        $msgError = $translate->get($error);
        if ($args !== null) {
            $msgError = str_parse($msgError, $args);
        }
        echo $msgError;
        exit();
    }

}