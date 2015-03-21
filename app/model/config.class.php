<?php

class Config {

    private $configFile;
    private $configData;
    
    function __construct($path = null) {
        if ($path === null) {
            $path = DATA . CONFIG;
        }
        if (!file_exists($path)) {
            throw new Exception("FILE_NOT_FOUND");
        } else {
            $this->configFile = $path;
        }
        $this->getConfig();
    }

    public function get($path = null, $default = null) {
        if(strpos($path, '->')) throw new Exception("Config doesnt use arrows anymore.");
        $return = $this->getConfig();
        if ($path === null)
            return $return;
        foreach(explode('.', $path) as $key) {
            if (isset($return[$key])) {
                $return = $return[$key];
            } else {
                $unknow = true;
                break;
            }
        }
        if ($unknow and $default === null) {
            throw new Exception("UNKNOW_CONFIG_DATA");
        } else if ($unknow) {
            return $default;
        }
        return $return;
    }

    public function exists($path) {
        if(strpos($path, '->')) throw new Exception("Config doesnt use arrows anymore.");
        $config = $this->getConfig();
        foreach(explode('.', $path) as $key) {
            if (isset($config[$key]))
                $config = $config[$key];
            else
                return false;
        }
        return true;
    }

    public function set($path, $value) {
        if(strpos($path, '->')) throw new Exception("Config doesnt use arrows anymore.");
        $array = $this->getConfig();
        $tmp = &$array;
        foreach(explode('.', $path) as $key) {
            if (!isset($tmp[$key]))
                $tmp[$key] = array();
            $tmp = &$tmp[$key];
        }
        $tmp = $value;
        $this->configData = $array;
        return true;
    }

    public function delete($path = null) {
        if(strpos($path, '->')) throw new Exception("Config doesnt use arrows anymore.");
        if (is_null($path)) {
            $this->configData = array();
            return true;
        }
        $array = $this->getConfig();
        $tmp = &$array;
        $keys = explode('.', $path);
        while (count($keys) > 1) {
            $key = array_shift($keys);
            if (isset($tmp[$key]))
                $tmp = &$tmp[$key];
            else
                return false;
        }
        $key = array_shift($keys);
        unset($tmp[$key]);
        $this->configData = $array;
        return true;
    }

    public function save($beautify = true) {
        $json = json_encode($this->configData);
        if ($beautify)
            $json = $this->beautify($json);
        $json = '<?php/*' . PHP_EOL . $json . PHP_EOL . '*/?>';
        file_put_contents($this->configFile, $json);
        return true;
    }

    public function refresh() {
        $this->getConfig(true);
        return true;
    }

    private function getConfig($refresh = false) {
        if (!isset($this->configData) or $refresh) {
            $file = file_get_contents($this->configFile);
            $fileJson = substr($file, 7, -4);
            $this->configData = json_decode($fileJson, true);
            if (empty($this->configData) and $this->configFile == DATA . CONFIG) {
                echo 'Cannot load CMS configuration.';
                exit();
            }
        }
        return $this->configData;
    }

    public function beautify($rawJson) {
        $result = '';
        $level = 0;
        $in_quotes = false;
        $in_escape = false;
        $ends_line_level = NULL;
        $json_length = strlen($rawJson);

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
