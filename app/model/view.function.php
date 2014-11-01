<?php

function view($html, $page) {

    $config = new Config;
    $app = new App;
    $tools = new Tools;
    
    $user = new User;
    $user->setup();
    
    $html = str_replace( '<!-- %', '%', $html );
    $html = str_replace( '% -->', '%', $html );
    
    /* NAVBAR MAKER */
    /* ============ */

    $match = $tools->textBetween($html, '%SINGLE%');
    $navSingle = $match[1];
    $html = str_replace( $match[0], '', $html );
    
    $match = $tools->textBetween($html, '%CHILD_START%');
    $navGroup['start'] = $match[1];
    $html = str_replace( $match[0], '', $html );
    
    $match = $tools->textBetween($html, '%CHILD_SINGLE%');
    $navGroup['single'] = $match[1];
    $html = str_replace( $match[0], '', $html );
    
    $match = $tools->textBetween($html, '%CHILD_END%');
    $navGroup['end'] = $match[1];
    $html = str_replace( $match[0], '', $html );
    
    $navbar = '';
    
    foreach ($page->getParents() as $navPage) {
        if ($navPage['is_parent'] == 1) {
        
            $navbar .= str_replace( '{TITLE}' , $navPage['title'], $navGroup['start']) . PHP_EOL;
                        
            foreach ($page->getChilds( $navPage['id'] ) as $navChild) {
                $nav = $navGroup['single'];
                $nav = str_replace( '{TITLE}' , $navChild['title'], $nav);
                $nav = str_replace( '{HREF}' , ABS_ROOT . $navPage['slug'] . '/' . $navChild['slug'] . '/', $nav);
                $navbar .= $nav . PHP_EOL;
            }
            $navbar .= $navGroup['end'] . PHP_EOL;
            
        } else {
        
            $nav = $navSingle;         
            if ( $page->get('slug') == $navPage['slug'] ) {
                $nav = str_replace( '{ACTIVE_CURRENT}' , 'active', $nav);
            } else {
                $nav = str_replace( '{ACTIVE_CURRENT}' , '', $nav);
            }
            if ($navPage['home'] == 1 ) {
                $nav = str_replace( '{HREF}' , HTTP_ROOT, $nav);
                $nav = str_replace( '{TITLE}' , $config->get('user->nav->icons->home') . $navPage['title'], $nav);   
            } else {
                $nav = str_replace( '{HREF}' , ABS_ROOT . $navPage['slug'] . '/', $nav);
                $nav = str_replace( '{TITLE}' , $navPage['title'], $nav);   
            }
            
            
            
            
            
            $navbar .= $nav . PHP_EOL;
            
        }
    }
    
    $match = $tools->textBetween($html, '%NAVBAR%');
    $html = str_replace( $match[0], $navbar, $html );
    
    /* USER NAVBAR */
    /* ============*/
    
    $page_login = $page->getPage(array('type' => 'native', 'type_data' => 'login' ));
    $page_register = $page->getPage(array('type' => 'native', 'type_data' => 'register' ));
    $page_account = $page->getPage(array('type' => 'native', 'type_data' => 'account' ));
    $page_logout = $page->getPage(array('type' => 'native', 'type_data' => 'logout' ));
    $page_recovery = $page->getPage(array('type' => 'native', 'type_data' => 'recovery' ));
    $page_member = $page->getPage(array('type' => 'native', 'type_data' => 'member' ));
    
    if ( $user->auth() ) {
        if ( $page->get('slug') == $page_member['slug'] ) {
            $html = str_replace( '{USER_1_ACTIVE_CURRENT}', 'active', $html );
        } else {
            $html = str_replace( '{USER_1_ACTIVE_CURRENT}', '',       $html );
        }
        $html = str_replace( '{USER_2_ACTIVE_CURRENT}', '',       $html );
        $html = str_replace( '{USER_1_HREF}', ABS_ROOT . $page_member['slug'] . '/', $html );
        $html = str_replace( '{USER_2_HREF}', ABS_ROOT . $page_logout['slug']  . '/', $html );
        $html = str_replace( '{USER_1_TITLE}', $config->get('user->nav->icons->account') . $user->get('username'), $html );
        $html = str_replace( '{USER_2_TITLE}', $config->get('user->nav->icons->logout') . $page_logout['title'] ,  $html );
    } else {
        if ( $page->get('slug') == $page_login['slug'] ) {
            $html = str_replace( '{USER_1_ACTIVE_CURRENT}', 'active', $html );
        } else {
            $html = str_replace( '{USER_1_ACTIVE_CURRENT}', '',       $html );
        }
        if ( $page->get('slug') == $page_register['slug'] ) {
            $html = str_replace( '{USER_2_ACTIVE_CURRENT}', 'active', $html );
        } else {
            $html = str_replace( '{USER_2_ACTIVE_CURRENT}', '',       $html );
        }
        $html = str_replace( '{USER_1_HREF}', ABS_ROOT . $page_login['slug']    . '/', $html );
        $html = str_replace( '{USER_2_HREF}', ABS_ROOT . $page_register['slug'] . '/', $html );
        $html = str_replace( '{USER_1_TITLE}', $config->get('user->nav->icons->login') . $page_login['title'],    $html );
        $html = str_replace( '{USER_2_TITLE}', $config->get('user->nav->icons->register') . $page_register['title'], $html );
    }
        
    /* HEADER MAKER */
    /* ============ */
    
    $header['active'] = $page->get('header');
    if ( empty( $header['active'] )) {
        $html = str_replace( '%HEADER%', '', $html );
    } else {
        $header['plugins'] = array();
        $files = glob(PLUGIN . 'header/*', GLOB_ONLYDIR | GLOB_MARK );
        foreach($files as $file) {
            if (file_exists($file . 'config.cfg.php')) {
                $configPlugin = new Config($file . 'config.cfg.php');
                if ( $configPlugin->get('plugin->type') == 'header' ) {
                    $header['plugins'][ $configPlugin->get('plugin->name') ] = $file;
                }
            }
        }
        $data = $page->get('header_data');
        define('HEADER', HTTP_ROOT . $header['plugins'][$header['active']]);
        $file = $app->needFile( $header['plugins'][$header['active']] . 'core.php' );
        require_once $file;
        $html = str_replace( '%HEADER%', $content, $html );
        unset( $content );
    }
        
    /* SIDEBAR MAKER */
    /* ============= */
    
    $match = $tools->textBetween($html, '%SIDEBAR%');
    $sidebar['template'] = $match[1];
    
    $sidebar['active'] = $page->getSidebars();
    if (!empty( $sidebar['active'] )) {
        $sidebar['plugins'] = array();
        $files = glob(PLUGIN . 'sidebar/*', GLOB_ONLYDIR | GLOB_MARK );
        foreach($files as $file) {
            if (file_exists($file . 'config.cfg.php')) {
                $configPlugin = new Config($file . 'config.cfg.php');
                if ( $configPlugin->get('plugin->type') == 'sidebar' ) {
                    $sidebar['plugins'][ $configPlugin->get('plugin->name') ] = $file;
                }
            }
        }
        foreach ($sidebar['active'] as $value) {
            if ( !empty($sidebar['plugins'][$value['plugin']]) ) {
                if ( file_exists( $sidebar['plugins'][$value['plugin']] . 'core.php') ) {
                    $data = $value['data'];
                    $currentFile = $sidebar['plugins'][$value['plugin']];
                    require_once $currentFile . 'core.php';
                    $sidebar['construct'] = $sidebar['template'];
                    $sidebar['construct'] = str_replace( '%TITLE%',   $value['name'], $sidebar['construct']);
                    $sidebar['construct'] = str_replace( '%CONTENT%', $content,       $sidebar['construct']);
                    unset( $content );
                    $sidebar['html'][] = $sidebar['construct'];
                }
            }    
        }
    }
    
    foreach ($sidebar['html'] as $value) {
        $sidebar['render'] .= $value;
    }
    $html = str_replace( $match[0], $sidebar['render'], $html );
    
    /* REPLACE VARIABLES */
    /* ================= */
    
    $variables = array(
        'NAME' => $config->get('user->info->name', 'unknow'),
        'VERSION' => $config->get('cms->version', 'unknow'),
        'LANGUAGE' => $config->get('user->settings->language', 'en_US'),
        'HTML_LANG' => substr( $config->get('user->settings->language', 'en_US') , 0, 2),
        'DESCRIPTION' => $config->get('user->info->description', ''),
        'THEME' => $config->get('user->settings->theme', 'bootswatch'),
        'DEBUG' => $config->get('user->settings->debug', false)
    );
    
    if ( defined ( 'TITLE' ) ) {
        $title = TITLE;
    } else {
        $title = $page->get('title');
    }

    $variables = array_merge($variables, array(
        'PAGE' => $title,
        'CURRENT_USER' => $user->get('username'),
        'CURRENT_USER_PICTURE' => $user->get('picture'),
        'YEAR' => date("Y"),
        'ROOT' => ABS_ROOT,
        'ASSETS' => HTTP_ROOT . THEMES . THEME . '/assets/'
    ));
    
    $variables = array_merge($variables, array(
        'URL_ROOT' => ABS_ROOT,
        'URL_LOGIN' => ABS_ROOT . $page_login['slug']    . '/',
        'URL_REGISTER' => ABS_ROOT . $page_register['slug']    . '/',
        'URL_LOGOUT' => ABS_ROOT . $page_logout['slug']    . '/',
        'URL_ACCOUNT' => ABS_ROOT . $page_account['slug']    . '/',
        'URL_RECOVERY' => ABS_ROOT . $page_recovery['slug']    . '/'
    ));
    
    
    // CMS variables
    foreach( $variables as $key => $value ) {
        $arrayVariables['{'.$key.'}'] = $value;
    }
    $html = strtr($html, $arrayVariables);
    
    // PHP Variables
    preg_match_all('/\{\$([a-zA-Z\ \_\[\]\'\"\-\>]*)\}/', $html, $matches, PREG_PATTERN_ORDER);
    foreach ($matches[1] as $value) {
        if ( strpos( $value, 'post ') == 0 ) {
            $postvalue = str_replace('post ', '', $value);
            $html = str_replace('{$'.$value.'}', $_POST[$postvalue], $html);
        } else {
            global $$value;
            $html = str_replace('{$'.$value.'}', $$value, $html);
        }
    }
    
    // Traduction Tags
    $translations = Translate::getAll();
    foreach( $translations as $key => $value ) {
        $arrayTranslations['{@'.$key.'}'] = $value;
    }
    $html = strtr($html, $arrayTranslations);
    
    $textareasBefore = $tools->textBetween($html, '<textarea', 'textarea>');
    $html = preg_replace('/<!--.*?-->|\t|(?:\r?\n[ \t]*)+/s', ' ', $html);
    $html = preg_replace('/ {2,}/', ' ', $html);
    $textareasAfter = $tools->textBetween($html, '<textarea', 'textarea>');
    if ($textareasBefore and $textareasAfter) {
        $html = str_replace( $textareasAfter[0], $textareasBefore[0], $html);
    }
    
    global $timestart;
    $html = str_replace( '{EXEC_TIME}', number_format(microtime(true)-$timestart, 4), $html );
    
    // Return result
    return $html;
}