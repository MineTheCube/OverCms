<?php

function view($content, $page) {


    /**
     *  Create needed objects
     */
    $config = new Config;
    $app = new App;
    $tools = new Tools;
    $user = new User;
    $user->setup();


    /**
     *  Get page
     */
    $e = 'Template file not found: '.THEMES.CURRENT_THEME.'/includes/';
    $html = new StdClass;
    $html->overall = @file_get_contents(THEMES . CURRENT_THEME . '/includes/' . 'overall' . EXT) or die($e.'overall.htm');
    $html->navbar = @file_get_contents(THEMES . CURRENT_THEME . '/includes/' . 'navbar' . EXT) or die($e.'navbar.htm');
    $html->container = @file_get_contents(THEMES . CURRENT_THEME . '/includes/' . 'container' . EXT) or die($e.'container.htm');
    $html->footer = @file_get_contents(THEMES . CURRENT_THEME . '/includes/' . 'footer' . EXT) or die($e.'footer.htm');
    $html->header = '';
    $html->content = '';


    /**
     *  Begin with some stuff
     */
    foreach ($html as $key => $data) {
        $data = str_replace( '<!-- %', '%', $data );
        $html->$key = str_replace( '% -->', '%', $data );
    }
    $error = @file_get_contents(THEMES . CURRENT_THEME . '/misc/error.htm');
    if ($error === false)
        $error = '%MESSAGE%';
        
    
    /**
     *  Making the navbar
     */
    $match = $tools->textBetween($html->navbar, '%SINGLE%');
    $navSingle = $match[1];
    $html->navbar = str_replace( $match[0], '', $html->navbar );
    
    $match = $tools->textBetween($html->navbar, '%CHILD_START%');
    $navGroup['start'] = $match[1];
    $html->navbar = str_replace( $match[0], '', $html->navbar );
    
    $match = $tools->textBetween($html->navbar, '%CHILD_SINGLE%');
    $navGroup['single'] = $match[1];
    $html->navbar = str_replace( $match[0], '', $html->navbar );
    
    $match = $tools->textBetween($html->navbar, '%CHILD_END%');
    $navGroup['end'] = $match[1];
    $html->navbar = str_replace( $match[0], '', $html->navbar );
    
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
            if ( $currentSlug == $navPage['slug'] ) {
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
    
    $match = $tools->textBetween($html->navbar, '%NAVBAR%');
    $html->navbar = str_replace( $match[0], $navbar, $html->navbar );
    
    
    /**
     *  Insert user links in navbar
     */
    $page_login = $page->getPage(array('type' => 'native', 'type_data' => 'login' ));
    $page_register = $page->getPage(array('type' => 'native', 'type_data' => 'register' ));
    $page_account = $page->getPage(array('type' => 'native', 'type_data' => 'account' ));
    $page_logout = $page->getPage(array('type' => 'native', 'type_data' => 'logout' ));
    $page_recovery = $page->getPage(array('type' => 'native', 'type_data' => 'recovery' ));
    $page_member = $page->getPage(array('type' => 'native', 'type_data' => 'member' ));
    $currentSlug = $page->get('slug');
    
    if ( $user->auth() ) {
        if ( $currentSlug == $page_member['slug'] )
            $html->navbar = str_replace( '{USER_1_ACTIVE_CURRENT}', 'active', $html->navbar );
        else
            $html->navbar = str_replace( '{USER_1_ACTIVE_CURRENT}', '', $html->navbar );

        $html->navbar = str_replace( '{USER_2_ACTIVE_CURRENT}', '',       $html->navbar );
        $html->navbar = str_replace( '{USER_1_HREF}', ABS_ROOT . $page_member['slug'] . '/', $html->navbar );
        $html->navbar = str_replace( '{USER_2_HREF}', ABS_ROOT . $page_logout['slug']  . '/', $html->navbar );
        $html->navbar = str_replace( '{USER_1_TITLE}', $config->get('user->nav->icons->account') . $user->get('username'), $html->navbar );
        $html->navbar = str_replace( '{USER_2_TITLE}', $config->get('user->nav->icons->logout') . $page_logout['title'] ,  $html->navbar );
    } else {
        if ( $currentSlug == $page_login['slug'] )
            $html->navbar = str_replace( '{USER_1_ACTIVE_CURRENT}', 'active', $html->navbar );
        else
            $html->navbar = str_replace( '{USER_1_ACTIVE_CURRENT}', '',       $html->navbar );

        if ( $currentSlug == $page_register['slug'] )
            $html->navbar = str_replace( '{USER_2_ACTIVE_CURRENT}', 'active', $html->navbar );
        else
            $html->navbar = str_replace( '{USER_2_ACTIVE_CURRENT}', '',       $html->navbar );

        $html->navbar = str_replace( '{USER_1_HREF}', ABS_ROOT . $page_login['slug']    . '/', $html->navbar );
        $html->navbar = str_replace( '{USER_2_HREF}', ABS_ROOT . $page_register['slug'] . '/', $html->navbar );
        $html->navbar = str_replace( '{USER_1_TITLE}', $config->get('user->nav->icons->login') . $page_login['title'], $html->navbar );
        $html->navbar = str_replace( '{USER_2_TITLE}', $config->get('user->nav->icons->register') . $page_register['title'], $html->navbar );
    }

    
    /**
     *  Get template of plugins
     */
    $template = array();
    $themePlugins = glob(THEMES . CURRENT_THEME . '/plugins/*', GLOB_ONLYDIR | GLOB_MARK );
    foreach($themePlugins as $themePlugin) {
        if (file_exists( $themePlugin . CONFIG)) {
            $themeConfig = new Config( $themePlugin . CONFIG );
            $name = $themeConfig->get('plugin->name', false);
            if (!isset($template[$name]) and $name) {
                $template[$name] = array(
                    'version' => $themeConfig->get('plugin->version'),
                    'path' => $themePlugin
                );
            }
        }
    }

    
    /**
     *  Get all Plugins
     */
    $plugins = array();
    $pluginsFolder = glob(PLUGIN . '*', GLOB_ONLYDIR | GLOB_MARK );
    if (is_array($pluginsFolder)) {
        foreach ($pluginsFolder as $plugin) {
            if (file_exists($plugin . 'core' . EX) and file_exists($plugin . CONFIG)) {
                try {
                    $pluginConfig = new Config($plugin . CONFIG);
                    $name = $pluginConfig->get('plugin->name', false);
                    if (!isset($plugins[$name]) and $name) {
                        $data = array();
                        $type = $pluginConfig->get('plugin->type', false);
                        $data['type'] = $type;
                        $data['api'] = $pluginConfig->get('plugin->api', false);
                        $data['path'] = $plugin;
                        $data['compatible'] = false;
                        if ( $config->exists('cms->compatible->'.$type))
                            if ( version_compare( $config->get('cms->version'), $pluginConfig->get('plugin->require->cms'), '>=' ) and
                                 version_compare($pluginConfig->get('plugin->require->cms'), $config->get('cms->compatible->'.$type), '>=') )
                                    $data['compatible'] = true;
                        $data['template'] = false;
                        if ($pluginConfig->exists('plugin->require->theme'))
                            if (version_compare( $pluginConfig->get('plugin->require->theme'), $template[$name]['version'], "<=" ))
                                $data['template'] = $template[$name]['path'] . 'views/';
                        $plugins[$name] = $data;
                    }
                } catch (Exception $e) {
                }
            }
        }
    }
    
    
    /**
     *  Encapsulation (PHP 5.2 compatible)
     */
    function pluginEncapsulation($page, $theme, $path) {
        require_once $path.'core.php';
        return $output;
    }
    function encapsulation($page, $data, $path) {
        require_once $path.'core.php';
        return $output;
    }
    
    
    /**
     *  Include plugin
     */
    if ($page->get('type') == 'plugin') {
        $name = $page->get('type_data');
        if (!empty($plugins[$name]) and $plugins[$name]['compatible'] === true and $plugins[$name]['type'] === 'page') {
            $theme = $plugins[$name]['template'];
            $data = pluginEncapsulation($page, $theme, $plugins[$name]['path']);
            $html->content = $data['content'];
            if ($data['translation']) {
                Translate::addPath( $plugins[$name]['path'] . $data['translation'], (isset($data['default_language']) ? $data['default_language'] : 'en_US') );
            }
        } else if (DEBUG) {
            if ($plugins[$name]['compatible'] === false)
                $html->content = str_replace( '%MESSAGE%', '{@ERROR_PLUGIN_VERSION}'.$name , $error );            
            else
                $html->content = str_replace( '%MESSAGE%', '{@ERROR_PLUGIN_NOT_FOUND}'.$name , $error ); 
        } else {
            $html->content = str_replace( '%MESSAGE%', '{@ERROR_CANNOT_LOAD_PAGE}'.$name , $error );
        }
    } else {
        $html->content = $content;
    }
    unset($content);
    
    
    /**
     *  Making the header
     */
    $name = $page->get('header');
    if (!empty($name)) {
	    if (!empty($plugins[$name]) and $plugins[$name]['compatible'] === true and $plugins[$name]['type'] === 'header') {
	        $headerData = $page->get('header_data');
	        $data = encapsulation($page, $headerData, $plugins[$name]['path']);
	        $html->header = $data['content'];
	        if ($data['translation']) {
	            Translate::addPath( $plugins[$name]['path'] . $data['translation'], (isset($data['default_language']) ? $data['default_language'] : 'en_US') );
	        }
	    } else if (DEBUG) {
	        if ($plugins[$name]['compatible'] === false)
	            $html->header = str_replace( '%MESSAGE%', '{@ERROR_PLUGIN_VERSION}'.$name , $error );            
	        else
	            $html->header = str_replace( '%MESSAGE%', '{@ERROR_PLUGIN_NOT_FOUND}'.$name , $error );
	    }    	
    }
    unset($content);

    
    /**
     *  Making the sidebar
     */
    $sidebarMatch = $tools->textBetween($html->container, '%SIDEBAR%');
    $sidebarTemplate = $sidebarMatch[1];
    $sidebarHtml = '';
    $sidebars = $page->getSidebars();
    if (!empty( $sidebars )) {
        foreach ($sidebars as $sidebar) {
            $name = $sidebar['plugin'];
            if ( !empty($plugins[$name]) and $plugins[$name]['compatible'] === true and $plugins[$name]['type'] === 'sidebar') {
                    $sidebarData = $sidebar['data'];
                    $data = encapsulation($page, $sidebarData, $plugins[$name]['path']);
                    $sidebar['construct'] = $sidebarTemplate;
                    $sidebar['construct'] = str_replace( '%TITLE%',   $sidebar['name'], $sidebar['construct']);
                    $sidebar['construct'] = str_replace( '%CONTENT%', $data['content'], $sidebar['construct']);
                    unset( $content, $sidebarData );
                    $sidebarHtml .= $sidebar['construct'];
                    if ($data['translation']) {
                        Translate::addPath( $plugins[$name]['path'] . $data['translation'], (isset($data['default_language']) ? $data['default_language'] : 'en_US') );
                    }
            } else if (DEBUG) {
                if ($plugins[$name]['compatible'] === false)
                    $sidebarHtml .= str_replace( '%MESSAGE%', '{@ERROR_PLUGIN_VERSION}'.$name , $error );            
                else
                    $sidebarHtml .= str_replace( '%MESSAGE%', '{@ERROR_PLUGIN_NOT_FOUND}'.$name , $error );
            }
        }
    }
    $html->container = str_replace($sidebarMatch[0], $sidebarHtml, $html->container);
    
    
    /**
     *  Generate container
     */
    $sidebarEnabled = $config->get('user->settings->sidebar', 'true') == true;
    if ( FULLWIDE === true ) {
        $inner = textBetween($html->container, '%FULLWIDE%');
        $html->container = str_replace($inner[0], $html->content, $html->container);
    } else if ( WIDE === true ) {
        $inner = $tools->textBetween($html->container, '%WIDE%');
        $html->container = str_replace($inner[0], $html->content, $html->container);
        $html->container = str_replace('%FULLWIDE%', '', $html->container);
    } else if ( NOBODY === true ) {
        $inner = $tools->textBetween($html->container, '%BODY%');
        $html->container = str_replace($inner[0], $html->content, $html->container);
        $html->container = str_replace('%FULLWIDE%', '', $html->container);
        $html->container = str_replace('%WIDE%', '', $html->container);
    } else {
        $html->container = str_replace('%INNER%', $html->content, $html->container);
        $html->container = str_replace('%FULLWIDE%', '', $html->container);
        $html->container = str_replace('%WIDE%', '', $html->container);
        $html->container = str_replace('%BODY%', '', $html->container);
    }
    
    
    /**
     *  Render with sidebar settings
     */
    if ($sidebarEnabled) {
        $html->container = preg_replace('/(?s)%SIDEBAR_OFF%(.*?)%SIDEBAR_OFF%/', '', $html->container);
    } else {
        $html->container = preg_replace('/(?s)%SIDEBAR_ON%(.*?)%SIDEBAR_ON%/', '', $html->container);
    }
    $html->container = str_replace('%SIDEBAR_ON%', '', $html->container);
    $html->container = str_replace('%SIDEBAR_OFF%', '', $html->container);
    
    
    /**
     *  Render complete html page
     */
    $htmlpage = '';
    $htmlpage .= $html->overall;
    $htmlpage .= $html->navbar;
    $htmlpage .= $html->header;
    $htmlpage .= $html->container;
    $htmlpage .= $html->footer;
    
    
    /**
     *  Include render of theme if any
     */
    $render = THEMES . CURRENT_THEME . '/render/core.php';
    if (file_exists($render))
        include $render;
    
    
    /**
     *  Prepare variables
     */
    if (defined('TITLE'))
        $title = TITLE;
    else
        $title = $page->get('title');
    
    $mail = $config->get('user->info->email', 'unknow');
    $mailto = 'mailto:'.$mail;
    for ($i=0; $i<strlen($mail); $i++)
        $mailEncoded .= "&#" . ord($mail[$i]) . ";";
    for ($i=0; $i<strlen($mailto); $i++)
        $mailtoEncoded .= "&#" . ord($mailto[$i]) . ";";
    
    
    /**
     *  Add variables
     */
    $variables = array(
        'NAME' => $config->get('user->info->name', 'unknow'),
        'MAIL' => $mail,
        'MAIL_ENCODED' => $mailEncoded,
        'MAILTO_ENCODED' => $mailtoEncoded,
        'VERSION' => $config->get('cms->version', 'unknow'),
        'LANGUAGE' => $config->get('user->settings->language', 'en_US'),
        'HTML_LANG' => substr( $config->get('user->settings->language', 'en_US') , 0, 2),
        'DESCRIPTION' => $config->get('user->info->description', ''),
        'CURRENT_THEME' => $config->get('user->settings->theme', 'bootswatch'),
        'DEBUG' => $config->get('user->settings->debug', false)
    );
    

    $variables = array_merge($variables, array(
        'PAGE' => $title,
        'CURRENT_USER' => $user->get('username'),
        'CURRENT_USER_PICTURE' => $user->get('picture'),
        'YEAR' => date("Y"),
        'ROOT' => ABS_ROOT,
        'ASSETS' => HTTP_ROOT . THEMES . CURRENT_THEME . '/assets/'
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
    $htmlpage = strtr($htmlpage, $arrayVariables);
    
    // PHP Variables
    preg_match_all('/\{\$([a-zA-Z\ \_\[\]\'\"\-\>]*)\}/', $htmlpage, $matches, PREG_PATTERN_ORDER);
    foreach ($matches[1] as $value) {
        if ( strpos( $value, 'post ') == 0 ) {
            $postvalue = str_replace('post ', '', $value);
            $htmlpage = str_replace('{$'.$value.'}', $_POST[$postvalue], $htmlpage);
        } else {
            global $$value;
            $htmlpage = str_replace('{$'.$value.'}', $$value, $htmlpage);
        }
    }
    
    // Traduction Tags
    $translations = Translate::getAll();
    foreach( $translations as $key => $value ) {
        $arrayTranslations['{@'.$key.'}'] = $value;
    }
    $htmlpage = strtr($htmlpage, $arrayTranslations);
    
    $textareasBefore = $tools->textBetween($htmlpage, '<textarea', 'textarea>');
    $htmlpage = preg_replace('/<!--.*?-->|\t|(?:\r?\n[ \t]*)+/s', ' ', $htmlpage);
    $htmlpage = preg_replace('/ {2,}/', ' ', $htmlpage);
    $textareasAfter = $tools->textBetween($htmlpage, '<textarea', 'textarea>');
    if ($textareasBefore and $textareasAfter)
        $htmlpage = str_replace( $textareasAfter[0], $textareasBefore[0], $htmlpage);
    
    global $timestart;
    $execTime = microtime(true)-$timestart;
    // $htmlpage = str_replace( '{EXEC_TIME}', count(get_included_files()).' files', $htmlpage );
    if ($execTime > 1)
        $htmlpage = str_replace( '{EXEC_TIME}', number_format(microtime(true)-$timestart, 3) . ' s', $htmlpage );
    else
        $htmlpage = str_replace( '{EXEC_TIME}', round($execTime*1000) . ' ms', $htmlpage );
    
    
    /**
     *  Return final html page
     */
    return $htmlpage;
}