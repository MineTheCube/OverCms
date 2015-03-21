<?php


/*
 * --------------------------------
 *            Respond
 * --------------------------------
 *
 */
function setFlash($type = false, $message = null, $location = null) {
    if ($_SESSION['flash_exists'] === true) return;
    $flash = make_response($type, $message, $location);
    $_SESSION['flash_type'] = $flash['type'];
    $_SESSION['flash_message'] = $flash['message'];
    $_SESSION['flash_location'] = $flash['location'];
    $_SESSION['flash_exists'] = true;
}

function getFlash() {
    if ($_SESSION['flash_exists'] !== true)
        return false;
    $flash = array(
        'type' => $_SESSION['flash_type'],
        'message' => $_SESSION['flash_message'],
        'location' => $_SESSION['flash_location']
    );
    unset($_SESSION['flash_type'], $_SESSION['flash_message'], $_SESSION['flash_location'], $_SESSION['flash_exists']);
    return $flash;
}

function hasFlash() {
    return ($_SESSION['flash_exists'] === true);
}

function renderFlash($type = null, $message = null, $location = null, $quitButton = true) {
    if ($type !== null)
        $flash = make_response($type, $message, $location);
    else if (isset($type['flash_type'], $type['flash_message'], $type['flash_location']))
        $flash = $type;
    else
        $flash = getFlash();
    if (!$flash or empty($flash))
        return '';
    $html = '<div class="alert alert-flash alert-'.($flash['type'] ? 'success' : 'danger').' alert-dismissible">';
    if ($quitButton)
        $html .= '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
    $html .= nl2br(e($flash['message'])).'</div>';
    if (!empty($location)) {
        if ($location === '$')
            $html .= '<script type="text/javascript">setTimeout(function(){location.reload(true)}, 1500);</script>';
        else
            $html .= '<script type="text/javascript">setTimeout(function(){document.location.href="'.ABS_ROOT.$location.'/"}, 1500);</script>';
    }
    return $html;
}

function ajax_respond($type = false, $message = null, $location = null) {
    $response = make_response($type, $message, $location);
    if ($response['type'])
        $response['type'] = 'success';
    else
        $response['type'] = 'danger';
    if (empty($response['location']))
        unset($response['location']);
    echo json_encode($response);
    exit();
}

function respond($type = false, $message = null, $location = null) {
    if (AJAX) 
        ajax_respond($type, $message, $location);
    else
        setFlash($type, $message, $location);
}

function make_response($type = false, $message = null, $location = null) {
    if ($type instanceof Exception) {
        $message = Translate::get($type->getMessage(), Translate::get('UNKNOW_ERROR'));
        $type = false;
        $location = empty($location) ? null : $location;
    } else if ($message instanceof Exception) {
        $message = $type.'_'.$message->getMessage();
        $type = false;
        $message = Translate::get($message, Translate::get('UNKNOW_ERROR'));
        $location = empty($location) ? null : $location;
    } else {
        $type = (bool) $type;
        $message = empty($message) ? ($type ? Translate::get('MODIFICATION_SUCCESSFUL') : Translate::get('UNKNOW_ERROR')) : Translate::get($message, $message);
        $location = empty($location) ? null : $location;
    }
    if (!empty($location) and $location !== '$')
        $location = ABS_ROOT.$location.'/';
    return array(
        'type' => $type,
        'message' => $message,
        'location' => $location
    );
}


/*
 * --------------------------------
 *             CMS
 * --------------------------------
 *
 */
function db(Database $instance = null) {
    static $database;
    if (!is_null($instance) and !isset($database))
        $database = $instance;
    return $database;
}

function reldate( $time ) {
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
            $unit = str_replace('{s}', ($delta == 1 ? '' : 'S'), $unit);
            $strTime = $delta . ' {@' . $unit . '}';
            break;
        }
    }
    return $prefix.trim($strTime).$suffix;
}

function debug() {
    echo '<pre>'.PHP_EOL;
    $vars = func_get_args();
    if (count($vars)) {
        echo '<b>Variable dump</b>: '.PHP_EOL;
        ob_start();
        foreach ($vars as $var) {
            echo '- ';
            var_dump($var);
        }
        echo e(ob_get_clean());
    }
    echo PHP_EOL.'<b>Included files</b>: '.PHP_EOL;
    $files = get_included_files();
    $root = dirname($_SERVER["SCRIPT_FILENAME"]);
    foreach ($files as $file) {
        echo '- '.str_replace($root, '', $file).PHP_EOL;
    }
    echo PHP_EOL.'<b>Backtrace</b>: '.PHP_EOL;
    debug_print_backtrace();
    exit();
}

function dump() {
    echo '<pre>'.PHP_EOL.'<b>Variable dump</b>: '.PHP_EOL;
    $vars = func_get_args();
    if (count($vars)) {
        ob_start();
        foreach ($vars as $var) {
            echo '- ';
            var_dump($var);
        }
        echo e(ob_get_clean());
    } else {
        echo '<b>Dump()</b>: No variable given.';
    }
    exit();
}

function sdump() {
    ob_start();

    echo '<pre>'.PHP_EOL.'<b>Variable dump</b>: '.PHP_EOL;
    $vars = func_get_args();
    if (count($vars)) {
        ob_start();
        foreach ($vars as $var) {
            echo '- ';
            var_dump($var);
        }
        echo e(ob_get_clean());
    } else {
        echo '<b>Dump()</b>: No variable given.';
    }

    return ob_get_clean();
}

function go($url = null) {
    if (empty($url))
        header('Location: ' . HTTP_ROOT);
    else
        header('Location: ' . ABS_ROOT . $url . '/');
    exit();
}


/*
 * --------------------------------
 *             General
 * --------------------------------
 *
 */
function with($obj) {
    return $obj;
}

function head($a) {
    return is_array($a) ? reset($a) : null;
}

function last($a) {
    return is_array($a) ? end($a) : null;
}

function array_value($array, $key, $default = null) {
    if (is_array($array))
        return $array[$key];
    if (is_object($array))
        return $array->key;
    return $default;
}

function e($html) {
    return htmlentities($html, ENT_QUOTES, "UTF-8");
}

function slug($string) {
    $string = strtolower(str_replace(array(
            'Š', 'š', 'Đ', 'đ', 'Ž', 'ž', 'Č', 'č', 'Ć', 'ć',
            'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É',
            'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô',
            'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'Þ', 'ß',
            'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é',
            'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ð', 'ñ', 'ò', 'ó',
            'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ý', 'ý', 'þ',
            'ÿ', 'Ŕ', 'ŕ', '/', ' ', '_', "\n", "\r", "\t"
        ), array(
            's', 's', 'dj', 'dj', 'z', 'z', 'c', 'c', 'c', 'c',
            'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e',
            'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o',
            'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'b', 'ss',
            'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e',
            'e', 'e', 'i', 'i', 'i', 'i', 'o', 'n', 'o', 'o',
            'o', 'o', 'o', 'o', 'u', 'u', 'u', 'y', 'y', 'b',
            'y', 'r', 'r', '-', '-', '-', '-', '-', '-'
        ), $string));
    $string = preg_replace('/[^a-z0-9-]+/', '', $string);
    $string = preg_replace('/-{2,}/', '-', $string);
    return trim($string, '-');
}

function str_random($length = 10, $withDigit_or_CustomChars = true, $withUpperCase = true, $withSpecialChars = false) {
    $length = ((int) $length < 0 ? 10 : (int) $length);
    if (is_string($withDigit_or_CustomChars)) {
        $chars = $withDigit_or_CustomChars;
    } else {
        $chars = 'abcdefghijklmnopqrstuvwxyz';
        if ($withDigit_or_CustomChars)
            $chars .= '0123456789';
        if ($withUpperCase)
            $chars .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        if ($withSpecialChars)
            $chars .= '-_.{[()]}=+~^*&#%$';
    }
    $random = '';
    while($length > strlen($chars)) {
        $random .= str_shuffle($chars);
        $length = $length-strlen($chars);
    }
    return $random.substr(str_shuffle($chars), 0, $length);
}

function digit_random($length = 10) {
    $length = ((int) $length < 0 ? 10 : (int) $length);
    $random = '';
    while($length > 7) {
        $random .= mt_rand(1000000,9999999);
        $length = $length-7;
    }
    return $random.substr(mt_rand(1000000,9999999), 0, $length);
}

function str_limit($string, $limit = 10, $addIfLimitReached = '') {
    $limit = ((int) $limit < 0 ? 10 : (int) $limit);
    if (strlen($string) <= $limit)
        $addIfLimitReached = '';
    $string = utf8_substr($string, 0, $limit);
    // $string = mb_substr($string, 0, $limit, 'UTF-8');
    if (utf8_decode(substr($string, -1)) === '?')
        $string = substr($string, 0, -1);
    return $string.$addIfLimitReached;
}

function str_between($string, $tagOpen, $tagClose = null) {
    if ($tagClose === null)
        $tagClose = $tagOpen;
    
    $startIn = strpos($string, $tagOpen);
    $endIn = strpos($string, $tagClose, $startIn+strlen($tagOpen));

    if ($startIn === false or $endIn === false)
        return null;

    $startIn += strlen($tagOpen);
    return substr($string, $startIn, $endIn - $startIn);
}

function str_where($string, $tagOpen, $tagClose = null) {
    if ($tagClose === null)
        $tagClose = $tagOpen;
    
    $startOut = strpos($string, $tagOpen);
    $endOut = strpos($string, $tagClose, $startOut+strlen($tagOpen));

    if ($startOut === false or $endOut === false)
        return null;

    $endOut += strlen($tagClose);
    return substr($string, $startOut, $endOut - $startOut);
}

function str_remove_between($string, $tagOpen, $tagClose = null) {
    if (is_array($tagOpen)) 
        return str_remove_between_array($string, $tagOpen, $tagClose);

    if ($tagClose === null)
        $tagClose = $tagOpen;
    
    $startIn = strpos($string, $tagOpen);
    $endIn = strpos($string, $tagClose, $startIn+strlen($tagOpen));
    
    if ($startIn === false or $endIn === false)
        return $string;

    $startIn += strlen($tagOpen);
    return substr($string, 0, $startIn) . substr($string, $endIn);
}

function str_remove_where($string, $tagOpen, $tagClose = null) {
    if (is_array($tagOpen)) 
        return str_remove_where_array($string, $tagOpen, $tagClose);
            
    if ($tagClose === null)
        $tagClose = $tagOpen;
    
    $startOut = strpos($string, $tagOpen);    
    $endOut = strpos($string, $tagClose, $startOut+strlen($tagOpen));

    if ($startOut === false or $endOut === false)
        return $string;

    $endOut += strlen($tagClose);
    return substr($string, 0, $startOut) . substr($string, $endOut);
}

function str_remove_between_array($string, $tagsOpen, $tagsClose = null) {
    if (!is_array($tagsOpen))
        return $string;
    if (!is_array($tagsClose) or empty($tagsClose) or count($tagsOpen) !== count($tagsClose))
        $tagsClose = $tagsOpen;

    foreach (array_combine($tagsOpen, $tagsClose) as $tagOpen => $tagClose) {
        $string = str_remove_between($string, $tagOpen, $tagClose);
    }

    return $string;
}

function str_remove_where_array($string, $tagsOpen, $tagsClose = null) {
    if (!is_array($tagsOpen))
        return $string;
    if (!is_array($tagsClose) or empty($tagsClose) or count($tagsOpen) !== count($tagsClose))
        $tagsClose = $tagsOpen;

    foreach (array_combine($tagsOpen, $tagsClose) as $tagOpen => $tagClose) {
        $string = str_remove_where($string, $tagOpen, $tagClose);
    }

    return $string;
}

function str_remove($string, $toRemove) {
    return str_replace($toRemove, '', $string);
}

function str_remove_array($string, $array) {
    return str_replace($array, '', $string);
}

function str_replace_array($string, array $array, $replace = null) {
    if ($replace === null)
        return str_replace(array_keys($array), array_values($array), $string);
    else
        return str_replace($array, $replace, $string);
}

function str_parse($string, $args, $tag = '%') {
    if (!is_array($args) or empty($args))
        return $string;
    $args = array_combine(array_map(create_function('$k', 'return "'.$tag.'".$k."'.$tag.'";'), array_keys($args)), $args);
    return strtr($string, $args);
}

function strip_comments($html) {
    return str_replace(array(
        '<!--  -->',
        '<!-- %',
        '% -->'
    ), array(
        '',
        '%',
        '%'
    ), $html);
}

function utf8_substr($string, $start, $length = null) {
    if (function_exists('mb_substr'))
        return mb_substr($string, $start, ($length === null ? mb_strlen($string) : $length), 'UTF-8');
    if ($length === null)
        return substr($string, $start);
    else
        return substr($string, $start, $length);
}
