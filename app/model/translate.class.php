<?php

class Translate {

    private static $translations = array();
  
    public function __construct() {
    }
    
    public static function addPath($path, $defaultLanguage = 'en_US'){
        if (file_exists( $path . LANGUAGE . EX )) {
            require($path . LANGUAGE . EX);
        } else if (file_exists( $path . $defaultLanguage . EX )) {
            require($path . $defaultLanguage . EX);
        } else {
            return false;
        }
        if(is_array($lang)) {
            foreach($lang as $key => $value) {
                self::$translations[$key] = $value;
            }
        } else {
            return false;
        }
    }

    public static function get($tag, $default = false) {
        if ( !isset(self::$translations[$tag] ) ) {
            if ( $default !== false)
                return $default;
            return $tag;
        } else {
            return str_replace('\n', PHP_EOL, self::$translations[$tag]);
        }
    }

    public static function getAll() {
        return self::$translations;
    }

}
