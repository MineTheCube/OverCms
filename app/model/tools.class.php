<?php

Class Tools {
    
    public function reldate( $time ) {
        if ($time > time())
            return '{@IN_THE_FUTURE}';
            
        $timeDiff = time() - $time;
        
        $prefix = '{@RELATIVE_DATE_PREFIX}';
        $suffix = '{@RELATIVE_DATE_SUFFIX}';

        if($timeDiff <= 60)
            return $prefix.'{@LESS_THAN_A_MINUTE}'.$suffix;

        $times = array(
                31104000 => 'YEAR{s}',
                2592000  => 'MONTH{s}',
                604800   => 'WEEK{s}',
                86400    => 'DAY{s}',
                3600     => 'HOUR{s}',
                60       => 'MINUTE{s}');

        foreach($times AS $seconds => $unit) {
            $delta = floor($timeDiff / $seconds);
            if($delta >= 1) {
                $unit = str_replace('{s}', ($delta == 1 ? NULL : 'S'), $unit);
                $strTime = $delta . ' {@' . $unit . '}';
                break;
            }
        }
        return $prefix.trim($strTime).$suffix;
    }

    public function textBetween($data, $tagOpen, $tagClose = null) {
    
        if ($tagClose === null) {
            $tagClose = $tagOpen;
        }
        
        $startIn = strpos($data, $tagOpen) + strlen($tagOpen);
        $startOut = strpos($data, $tagOpen);
        
        $endIn = strpos($data, $tagClose, $startIn);
        $endOut = strpos($data, $tagClose, $startIn) + strlen($tagClose);
        
        if ($endIn and $endOut) {
            $result[0] = substr($data, $startOut, $endOut - $startOut);
            $result[1] = substr($data, $startIn, $endIn - $startIn);
            return $result;
        }
        return false;
        
    }

    public function loadTextEditor($name = 'texteditor', $content = null) {
        $texteditor = file_get_contents( THEMES . CURRENT_THEME . '/misc/texteditor.htm' );
        $texteditor = str_replace( '%TEXT_EDITOR_NAME%', $name, $texteditor);
        $texteditor = str_replace( '%TEXT_EDITOR_CONTENT%', $content, $texteditor);
        return $texteditor;
    }
    
    public function executeSqlFile( $pathToSql ) {  
        if (!file_exists( $pathToSql )) {
            return false;
        }
        $app = new App;
        $lines = file( $pathToSql );
        foreach ($lines as $line) {
            if (substr($line, 0, 2) == '--' || $line == '')
                continue;
            $templine .= $line;
            if (substr(trim($line), -1, 1) == ';') {
                $result = $app->query($templine);
                $templine = '';
                if ($result === false) {
                    return false;
                }
            }
        }
        return true;
    }

    public function parse($text, $args) {
        if (!is_array($args)) {
            return $text;
        }
        $args = array_combine(array_map(create_function('$k', 'return "%".$k."%";'), array_keys($args)), $args);
        return strtr($text, $args);
    }
    
}