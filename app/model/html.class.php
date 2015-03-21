<?php

class Html {

    private static $css;
    private static $js;
    private static $variables = array();
    private static $output;
  
    public function __construct() {
    }
    
    public static function CSS($style = false, $mode = 'auto') {
        if (is_array($style)) {
            foreach ($style as $s) {
                self::CSS($s, $mode);
            }
            return;
        }
        if ($style === false) {
            if (!empty(self::$output)) {
                self::$css .= self::$output;
                self::$output = null;
            }
            return;
        }
        self::$output = null;
        if (!empty($style)) {
            if ($mode === 'style' or $mode === 'auto' and substr_count($style, "\n") > 0)
                self::$css .= '<style type="text/css">'.$style.'</style>';
            else if ($mode === 'stylesheet' or ($mode === 'auto' and substr_count($style, "\n") === 0 and substr($style, -4) === '.css'))
                self::$css .= '<link href="'.$style.'" rel="stylesheet">';
            else
                self::$css .= $style;
        }
    }
    
    public static function JS($script = false, $mode = 'auto') {
        if (is_array($script)) {
            foreach ($script as $s) {
                self::JS($s, $mode);
            }
            return;
        }
        if ($script === false) {
            if (!empty(self::$output)) {
                self::$js .= self::$output;
                self::$output = null;
            }
            return;
        }
        self::$output = null;
        if (!empty($script)) {
            if ($mode === 'script' or ($mode === 'auto' and substr_count($script, "\n") > 0))
                self::$js .= '<script type="text/javascript">'.$script.'</script>';
            else if ( $mode === 'src' or ($mode === 'auto' and substr_count($script, "\n") === 0 and substr($script, -3) === '.js'))
                self::$js .= '<script src="'.$script.'""></script>';
            else
                self::$js .= $script;
        }
    }

    public static function bind($key, $value) {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                self::bind($k, $v);
            }
            return;
        }
        if (isset(self::$variables[$key]))
            return false;
        self::$variables[$key] = $value;
    }

    public static function get($key) {
        if (isset(self::$$key))
            return self::$$key;
        return null;
    }

    public static function listen() {
        ob_start();
    }

    public static function output($vars = null, $format = true) {
        $content = ob_get_clean();
        if (!empty($content) and is_array($vars))
            $content = self::parse($content, $vars, $format);
        self::$output = (string) $content;
        return $content;
    }

    public static function parse($content, $vars, $format = true) {
        if (!empty($content) and is_array($vars)) {
            foreach ($vars as $key => $value) {
                if ($format)
                    $key = '%'.str_replace(' ', '_', strtoupper($key)).'%';
                $content = str_replace($key, $value, $content);
            }
        }
        return $content;
    }

    public static function file($file, $vars = null, $format = true) {
        if (is_array($file)) {
            $files = $file;
            foreach ($files as $file)
                if (is_file($file) and is_readable($file))
                    break;
        }
        if (!is_file($file) or !is_readable($file))
            return false;
        $content = @file_get_contents($file);
        if (is_array($vars))
            $content = self::parse($content, $vars, $format);
        return $content;
    }

}
