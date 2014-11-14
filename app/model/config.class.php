<?php

class Config {

    private $configFile;
    private $configJson;
    
    function __construct($path = null) {
        if ($path === null) {
            $path = DATA . "config.cfg.php";
        }
        if (!file_exists($path)) {
            throw new Exception("FILE_NOT_FOUND");
        } else {
            $this->configFile = $path;
        }
        $this->getConfig();
    }

    public function get($key = null, $default = null) {
        $return = $this->getConfig();
        if ($key === null)
            return $return;
        $keys = explode('->', $key);
        foreach($keys as $key) {
            if (!isset($return->$key)) {
                $unknow = true;
                break;
            }
            $return = $return->$key;
        }
        if ($unknow and $default === null) {
            throw new Exception("UNKNOW_CONFIG_DATA");
        } else if ($unknow) {
            return $default;
        }
        return $return;
    }

    public function exists($key) {
        $config = $this->getConfig();
        $keys = explode('->', $key);
        foreach($keys as $key) {
            if (!isset($config->$key))
                $unknow = true;
            $config = $config->$key;
        }
        if ($unknow)
            return false;
        return true;
    }

    public function set($key, $value) {
        $configArray = json_decode(json_encode($this->configJson), true);
        $keys = explode('->', $key);
        $count = count($keys);
        $insert = $value;
        for ($i = $count-1; $i >= 0; $i--) {
            $insert = array(
                $keys[$i] => $insert
            );
        }
        $newConfig = array_replace_recursive( $configArray, $insert );
        $this->configJson = json_decode(json_encode($newConfig));
        return true;
    }

    public function delete($key) {
        if (preg_match('/^[a-zA-Z0-9](([a-zA-Z0-9]|(\-\>))+[a-zA-Z0-9])$/', $key)) {
            eval('unset($this->configJson->'.$key.');');
            return true;
        }
        return false;
    }

    public function save() {
        $json = json_encode($this->configJson);
        $json = $this->beautify( $json );
        $json = '<?php/*' . PHP_EOL . $json . PHP_EOL . '*/?>';
        file_put_contents($this->configFile, $json);
        return true;
    }

    public function refresh() {
        $this->getConfig(true);
        return true;
    }

    private function getConfig($refresh = false) {
        if ($this->configJson == null or !isset($this->configJson) or $refresh) {
            $file = file_get_contents($this->configFile);
            $fileJson = substr($file, 7, -4);
            $this->configJson = json_decode($fileJson);
            if (empty($this->configJson) and $this->configFile == DATA . "config.cfg.php") {
                echo 'Cannot load CMS configuration.';
                exit();
            }
        }
        return $this->configJson;
    }

    public function beautify( $rawJson ) {
        $result = '';
        $level = 0;
        $in_quotes = false;
        $in_escape = false;
        $ends_line_level = NULL;
        $json_length = strlen( $rawJson );

        for( $i = 0; $i < $json_length; $i++ ) {
            $char = $rawJson[$i];
            $new_line_level = NULL;
            $post = "";
            if( $ends_line_level !== NULL ) {
                $new_line_level = $ends_line_level;
                $ends_line_level = NULL;
            }
            if ( $in_escape ) {
                $in_escape = false;
            } else if( $char === '"' ) {
                $in_quotes = !$in_quotes;
            } else if( ! $in_quotes ) {
                switch( $char ) {
                    case '}': case ']':
                        $level--;
                        $ends_line_level = NULL;
                        $new_line_level = $level;
                        break;

                    case '{': case '[':
                        $level++;
                    case ',':
                        $ends_line_level = $level;
                        break;

                    case ':':
                        $post = " ";
                        break;

                    case " ": case "\t": case "\n": case "\r":
                        $char = "";
                        $ends_line_level = $new_line_level;
                        $new_line_level = NULL;
                        break;
                }
            } else if ( $char === '\\' ) {
                $in_escape = true;
            }
            if( $new_line_level !== NULL ) {
                $result .= "\n".str_repeat( "\t", $new_line_level );
            }
            $result .= $char.$post;
        }
        return $result;
    }
    
}
