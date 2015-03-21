<?php

function view($content, $page) {


    /**
     *  Create needed objects
     */
    $config = new Config;
    $app = new App;
    $user = new User;
    $user->setup();


    /**
     *  Get page
     */
    $e = 'Template file not found: '.TEMPLATE.CURRENT_TEMPLATE.'/includes/';
    $html = new StdClass;
    $html->overall = @file_get_contents(TEMPLATE . CURRENT_TEMPLATE . '/includes/' . 'overall' . EXT) or die($e.'overall.htm');
    $html->navbar = @file_get_contents(TEMPLATE . CURRENT_TEMPLATE . '/includes/' . 'navbar' . EXT) or die($e.'navbar.htm');
    $html->container = @file_get_contents(TEMPLATE . CURRENT_TEMPLATE . '/includes/' . 'container' . EXT) or die($e.'container.htm');
    $html->footer = @file_get_contents(TEMPLATE . CURRENT_TEMPLATE . '/includes/' . 'footer' . EXT) or die($e.'footer.htm');
    $html->header = '';
    $html->content = '';


    /**
     *  Begin with some stuff
     */
    foreach ($html as $key => $data) {
        $html->$key = str_replace(array('<!-- %', '% -->'), '%', $data );
    }
    $error = '<div class="alert alert-warning alert-debug">%MESSAGE%</div>';

    
    /**
     *  Making the navbar
     */
    $navSingle = str_between($html->navbar, '%SINGLE%');
    $html->navbar = str_remove($html->navbar, '%SINGLE%'.$navSingle.'%SINGLE%');
    
    $navGroup['start'] = str_between($html->navbar, '%CHILD_START%');
    $html->navbar = str_remove($html->navbar, '%CHILD_START%'.$navGroup['start'].'%CHILD_START%');
    
    $navGroup['single'] = str_between($html->navbar, '%CHILD_SINGLE%');
    $html->navbar = str_remove($html->navbar, '%CHILD_SINGLE%'.$navGroup['single'].'%CHILD_SINGLE%');
    
    $navGroup['end'] = str_between($html->navbar, '%CHILD_END%');
    $html->navbar = str_remove($html->navbar, '%CHILD_END%'.$navGroup['end'].'%CHILD_END%');
    
    $navbar = '';
    
    $userPerm = $user->get('permission');
    foreach ($page->getParents() as $navPage) {
        if ($userPerm >= $navPage['p_view']) {
            if ($navPage['is_parent'] == 1) {
            
                $tmp_navbar = str_replace( '{TITLE}' , e($navPage['title']), $navGroup['start']) . PHP_EOL;
                
                $hasChild = false;
                foreach ($page->getChilds( $navPage['id'] ) as $navChild) {
                    if ($userPerm < $navChild['p_view'])
                        continue;
                    $nav = $navGroup['single'];
                    $nav = str_replace( '{TITLE}' , e($navChild['title']), $nav);
                    if ($navChild['type'] === 'link') {
                        $url = $navChild['type_data'];
                        if ($navChild['content'] === 'target_blank')
                            $url = e($url).'" target="_blank';
                        else
                            $url = e($url);
                        $nav = str_replace( '{HREF}' , $url, $nav);
                    } else {
                        $nav = str_replace( '{HREF}' , ABS_ROOT . $navPage['slug'] . '/' . $navChild['slug'] . '/', $nav);
                    }
                    $tmp_navbar .= $nav . PHP_EOL;
                    $hasChild = true;
                }

                $tmp_navbar .= $navGroup['end'] . PHP_EOL;

                if ($hasChild)
                    $navbar .= $tmp_navbar;

                
            } else {
            
                $nav = $navSingle;         
                if ( $currentSlug == $navPage['slug'] ) {
                    $nav = str_replace( '{ACTIVE_CURRENT}' , 'active', $nav);
                } else {
                    $nav = str_replace( '{ACTIVE_CURRENT}' , '', $nav);
                }
                if ($navPage['home'] == 1 ) {
                    $nav = str_replace( '{HREF}' , HTTP_ROOT, $nav);

                    $iconHome = $config->get('user.nav.icons.home', '');
                    $iconHome = (empty($iconHome) ? '' : '<span class="glyphicon '.$iconHome.'"></span> &nbsp;');
                    $nav = str_replace( '{TITLE}' , $iconHome . e($navPage['title']), $nav);   
                } else {

                    if ($navPage['type'] === 'link') {
                        $url = $navPage['type_data'];
                        if ($navPage['content'] === 'target_blank')
                            $url = e($url).'" target="_blank';
                        else
                            $url = e($url);
                        $nav = str_replace( '{HREF}' , $url, $nav);
                    } else {
                        $nav = str_replace( '{HREF}' , ABS_ROOT . $navPage['slug'] . '/', $nav);
                    }

                    $nav = str_replace( '{TITLE}' , e($navPage['title']), $nav);   
                }
                $navbar .= $nav . PHP_EOL;
            }
        }
    }
    
    $match = str_where($html->navbar, '%NAVBAR%');
    $html->navbar = str_replace($match, $navbar, $html->navbar);
    
    
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

        $iconAccount = $config->get('user.nav.icons.account', '');
        $iconAccount = (empty($iconAccount) ? '' : '<span class="glyphicon '.$iconAccount.'"></span> &nbsp;');
        $html->navbar = str_replace( '{USER_1_TITLE}', $iconAccount . $user->get('username'), $html->navbar );

        $iconLogout = $config->get('user.nav.icons.logout', '');
        $iconLogout = (empty($iconLogout) ? '' : '<span class="glyphicon '.$iconLogout.'"></span> &nbsp;');
        $html->navbar = str_replace( '{USER_2_TITLE}', $iconLogout . $page_logout['title'] ,  $html->navbar );
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

        $iconLogin = $config->get('user.nav.icons.login', '');
        $iconLogin = (empty($iconLogin) ? '' : '<span class="glyphicon '.$iconLogin.'"></span> &nbsp;');
        $html->navbar = str_replace( '{USER_1_TITLE}', $iconLogin . $page_login['title'], $html->navbar );

        $iconRegister = $config->get('user.nav.icons.register', '');
        $iconRegister = (empty($iconRegister) ? '' : '<span class="glyphicon '.$iconRegister.'"></span> &nbsp;');
        $html->navbar = str_replace( '{USER_2_TITLE}', $iconRegister . $page_register['title'], $html->navbar );
    }

    
    /**
     *  Get all Plugins
     */
    $plugins = $app->getPlugins();
    
    
    /**
     *  Encapsulation (PHP 5.2 compatible)
     */
    function api_encapsulation($page, $user, $plugin) {
        $config = $plugin['config'];
        $path = $plugin['path'];        
        $r = (require $path.'api'.EX);
        return $r === 1 ? true : $r;
    }

    function encapsulation($page, $user, $plugin, $data, $id = null) {
        // Define variables
        $config = $plugin['config'];
        $path = $plugin['path'];
        $template = $plugin['template'];
        $depend = array();
        if ($id === null) unset($id);

        // Includes dependencies
        if (is_array($plugin['depend'])) {
            foreach ($plugin['depend'] as $depend_name => $depend_plugin) {
                if (is_array($depend_plugin)) {
                    $depend[$depend_name] = api_encapsulation($page, $user, $depend_plugin);
                } else {
                    $depend[$depend_name] = false;
                }
            }
        }
        unset($depend_plugin, $depend_name);

        // Include plugin
        $return = (require ($path.'core'.EX));

        // Get result
        if ($return instanceof Parser)
            $html = $return->render();
        else if ($return !== 1)
            $html = $return;
        else
            $html = null;

        // Parse plugin's variables
        if (!empty($html))
            return str_replace(array(
                '{PLUGIN_ASSETS}',
                '{PLUGIN_TEMPLATE}',
                '{PLUGIN_PATH}',
            ), array(
                HTTP_ROOT.$template.'assets/',
                HTTP_ROOT.$template,
                HTTP_ROOT.$path
            ), $html);
        else if ($html !== false and DEBUG)
            return renderFlash(false, 'ERROR_EMPTY_PLUGIN');
        else
            return false;
    }
    
    
    /**
     *  Include plugin
     */
    if ($page->get('type') == 'plugin') {
        $name = $page->get('type_data');
        if (empty($name)) {
            $html->content = str_replace( '%MESSAGE%', '{@ERROR_NO_PLUGIN}' , $error );
        } else if (!empty($plugins[$name]) and $plugins[$name]['compatible'] === true and $plugins[$name]['type'] === 'page' and $plugins[$name]['depend'] !== false) {
            $path = $plugins[$name]['path'];
            if ($plugins[$name]['lang']['translation']) {
                Translate::addPath(
                    $plugins[$name]['path'].'lang/',
                    $plugins[$name]['lang']['default_language']
                );
            }
            $html->content = encapsulation($page, $user, $plugins[$name], $page->get('content'));
        } else if (DEBUG) {
            if ($plugins[$name]['compatible'] === false)
                $html->content = str_replace( '%MESSAGE%', '{@ERROR_PLUGIN_VERSION}'.$name , $error );            
            else if ($plugins[$name]['depend'] === false)
                $html->content = str_replace( '%MESSAGE%', '{@ERROR_PLUGIN_DEPENDENCIES}'.$name , $error );
            else
                $html->content = str_replace( '%MESSAGE%', '{@ERROR_PLUGIN_NOT_FOUND}'.$name , $error ); 
        } else {
            $html->content = str_replace( '%MESSAGE%', '{@ERROR_CANNOT_LOAD_PAGE}' , $error );
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
	    if (!empty($plugins[$name]) and $plugins[$name]['compatible'] === true and $plugins[$name]['type'] === 'header' and $plugins[$name]['depend'] !== false) {
	        $headerData = $page->get('header_data');
            $path = $plugins[$name]['path'];
            if ($plugins[$name]['lang']['translation']) {
                Translate::addPath(
                    $plugins[$name]['path'].'lang/',
                    $plugins[$name]['lang']['default_language']
                );
            }
	        $html->header = encapsulation($page, $user, $plugins[$name], $headerData);
	    } else if (DEBUG) {
	        if ($plugins[$name]['compatible'] === false)
	            $html->header = str_replace( '%MESSAGE%', '{@ERROR_PLUGIN_VERSION}'.$name , $error );
            else if ($plugins[$name]['depend'] === false)
                $html->header = str_replace( '%MESSAGE%', '{@ERROR_PLUGIN_DEPENDENCIES}'.$name , $error );
	        else
	            $html->header = str_replace( '%MESSAGE%', '{@ERROR_PLUGIN_NOT_FOUND}'.$name , $error );
	    }    	
    }
    unset($content);

    
    /**
     *  Making the sidebar
     */
    $sidebarTemplate = str_between($html->container, '%SIDEBAR%');
    $sidebarHtml = '';
    $sidebars = $page->getSidebar();
    $id = 0;
    if (!empty( $sidebars )) {
        foreach ($sidebars as $sidebar) {
            $name = $sidebar['plugin'];
            if ( !empty($plugins[$name]) and $plugins[$name]['compatible'] === true and $plugins[$name]['type'] === 'sidebar' and $plugins[$name]['depend'] !== false) {
                $sidebarData = $sidebar['data'];
                $path = $plugins[$name]['path'];
                if ($plugins[$name]['lang']['translation']) {
                    Translate::addPath(
                        $plugins[$name]['path'].'lang/',
                        $plugins[$name]['lang']['default_language']
                    );
                }
                $data = encapsulation($page, $user, $plugins[$name], $sidebarData, ++$id);
                $sidebar['construct'] = $sidebarTemplate;
                $sidebar['construct'] = str_replace( '%TITLE%', $sidebar['name'], $sidebar['construct']);
                $sidebar['construct'] = str_replace( '%CONTENT%', $data, $sidebar['construct']);
                unset( $content, $sidebarData );
                if ($data !== false)
                    $sidebarHtml .= $sidebar['construct'];
            } else if (DEBUG) {
                if ($plugins[$name]['compatible'] === false)
                    $sidebarHtml .= str_replace( '%MESSAGE%', '{@ERROR_PLUGIN_VERSION}'.$name , $error );            
                else if ($plugins[$name]['depend'] === false)
                    $sidebarHtml .= str_replace( '%MESSAGE%', '{@ERROR_PLUGIN_DEPENDENCIES}'.$name , $error );            
                else
                    $sidebarHtml .= str_replace( '%MESSAGE%', '{@ERROR_PLUGIN_NOT_FOUND}'.$name , $error );
            }
        }
    }
    $html->container = str_replace('%SIDEBAR%'.$sidebarTemplate.'%SIDEBAR%', $sidebarHtml, $html->container);
    
    
    /**
     *  Generate container
     */
    $sidebarEnabled = $config->get('user.settings.sidebar', true) or defined('WIDE') and WIDE === true;
    if (defined('FULL_PAGE') and FULL_PAGE === true) {
        $inner = str_where($html->container, '%FULL_PAGE%');
        $html->container = str_replace($inner, $html->content, $html->container);
        $html->header = '';
        $html->footer = '';
    } else {
        $html->container = str_replace('%FULL_PAGE%', '', $html->container);
        $html->container = str_replace('%INNER%', $html->content, $html->container);
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
    $htmlpage .= $html->container;
    $htmlpage .= $html->footer;
    $htmlpage = str_replace('%HEADER%', $html->header, $htmlpage);
    
    
    /**
     *  Add CSS and JavaScript
     */
    $css = Html::get('css');
    $js = Html::get('js');
    $htmlpage = str_replace('%CSS%', $css, $htmlpage);
    $htmlpage = str_replace('%JAVASCRIPT%', $js, $htmlpage);

    
    /**
     *  Include render of template if any
     */
    $render = TEMPLATE . CURRENT_TEMPLATE . '/render/core.php';
    if (file_exists($render)) 
        include $render;
    
    
    /**
     *  Prepare variables
     */
    if (defined('TITLE'))
        $title = TITLE;
    else
        $title = $page->get('title');
    
    $mail = $config->get('user.info.email', 'unknow');
    $mailto = 'mailto:'.$mail;
    for ($i=0; $i<strlen($mail); $i++)
        $mailEncoded .= "&#" . ord($mail[$i]) . ";";
    for ($i=0; $i<strlen($mailto); $i++)
        $mailtoEncoded .= "&#" . ord($mailto[$i]) . ";";
    
    
    /**
     *  Add variables
     */
    $variables = array(
        'ALERT' => '<div id="alert">'.renderFlash().'</div>'
    );

    $variables += array(
        'NAME' => $config->get('user.info.name', 'unknow'),
        'MAIL' => $mail,
        'MAIL_ENCODED' => $mailEncoded,
        'MAILTO_ENCODED' => $mailtoEncoded,
        'VERSION' => $config->get('cms.version', 'unknow'),
        'LANGUAGE' => $config->get('user.settings.language', 'en_US'),
        'HTML_LANG' => substr( $config->get('user.settings.language', 'en_US') , 0, 2),
        'DESCRIPTION' => $config->get('user.info.description', ''),
        'CURRENT_TEMPLATE' => $config->get('user.settings.template', 'bootswatch'),
        'DEBUG' => $config->get('user.settings.debug', false)
    );
    

    $variables += array(
        'PAGE' => e($title),
        'CURRENT_USER' => $user->get('username'),
        'CURRENT_USER_PICTURE' => $user->get('picture'),
        'YEAR' => date("Y"),
        'ROOT' => ABS_ROOT,
        'WEBROOT' => '/'.ROOT,
        'ASSETS' => HTTP_ROOT . TEMPLATE . CURRENT_TEMPLATE . '/assets/'
    );
    
    $variables += array(
        'URL_ROOT' => ABS_ROOT,
        'URL_LOGIN' => ABS_ROOT . $page_login['slug']    . '/',
        'URL_REGISTER' => ABS_ROOT . $page_register['slug']    . '/',
        'URL_LOGOUT' => ABS_ROOT . $page_logout['slug']    . '/',
        'URL_ACCOUNT' => ABS_ROOT . $page_account['slug']    . '/',
        'URL_RECOVERY' => ABS_ROOT . $page_recovery['slug']    . '/'
    );


    // Added variables
    $addedVars = Html::get('variables');
    if (!empty($addedVars))
        $variables += $addedVars;
        
    // CMS variables
    foreach($variables as $key => $value) {
        $variables_keys[] = '{'.$key.'}';
        $variables_values[] = $value;
    }
    $htmlpage = str_replace($variables_keys, $variables_values, $htmlpage);
    
    // PHP Variables
    preg_match_all('/\{\$([a-zA-Z0-9-_ ]*)\}/', $htmlpage, $matches, PREG_PATTERN_ORDER);
    foreach ($matches[1] as $value) {
        $htmlpage = str_replace('{$'.$value.'}', htmlentities($_POST[$value], ENT_QUOTES, "UTF-8"), $htmlpage);
    }
    
    // Traduction Tags
    $translations = Translate::getAll();
    foreach($translations as $key => $value) {
        $translations_keys[] = '{@'.$key.'}';
        $translations_values[] = htmlentities($value, ENT_QUOTES, "UTF-8");
    }
    $htmlpage = str_replace($translations_keys, $translations_values, $htmlpage);

    $htmlpage = str_replace('</form>', '<input type="hidden" name="token" class="hidden" value="'.e($app->getToken()).'"></form>', $htmlpage);

    $htmlpage = str_replace('<!--  -->', '', $htmlpage);

    $event = new Event(array(
        'html' => $htmlpage,
        'user' => $user,
        'page' => $page,
        'update' => false
    ));

    EventManager::fire('onPageDisplay', $event);

    if ($event->update)
        $htmlpage = $event->htmlpage;                

    global $timestart;
    $execTime = microtime(true)-$timestart;
    if ($execTime > 1)
        $htmlpage = str_replace( '{EXEC_TIME}', number_format(microtime(true)-$timestart, 3) . ' s', $htmlpage );
    else
        $htmlpage = str_replace( '{EXEC_TIME}', round($execTime*1000) . ' ms', $htmlpage );
    
    
    /**
     *  Return final html page
     */
    return $htmlpage;
}