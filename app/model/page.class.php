<?php

class Page {

    private $id;
    private $title;
    private $desc;
    private $slug;
    private $is_parent;
    private $parent_id;
    private $p_view;
    private $p_edit;
    private $content;
    private $type;
    private $type_data;
    private $state;
    private $header;
    private $header_data;
    
    public function __construct() {
    }
    
    public function setup( $data = REQUEST_SLUG, $type = null, $pageType = 'all' ) {
        
        $app = new App;
        
        if (empty($type) or !ctype_alnum(str_replace('_', '', $type))) {
            $type = 'slug';
        }
        
        // Get database
        $rows1 = db()->select()->from('cms_public_pages')->where($type, $data)->fetchAll();
        $rows2 = db()->select()->from('cms_pages')->where($type, $data)->fetchAll();
        
        if (count($rows1) == 1 and $pageType != 'admin') {
            $page = $rows1[0];
            foreach($page as $key => $value) {
                $this->$key = $value;
            }
            return true;
        } else if (count($rows2) == 1 and $pageType != 'public') {
            $page = $rows2[0];
            foreach($page as $key => $value) {
                $this->$key = $value;
            }
            return true;
        } else {
            return false;
        }
        
    }

    
    public function get($index) {
        if (isset($this->$index)) {
            return $this->$index;
        } else {
            return false;
        }
    }
    
    public function getPage( $array, $pageType = 'all' ) {
        
        $app = new App;
        
        $data = array();
        foreach ($array as $key => $value) {
            if (!empty($req)) {
                $req .= ' AND ';
            }
            $req .= $key.' = ?';
            $data[] = $value;
        }
        
        // Get database
        if ($pageType != 'admin') {
            $rows1 = db()->query('SELECT * FROM cms_public_pages where ' . $req . ' ORDER BY position', $data)->fetchAll();
            if (count($rows1) == 1) {
                $page = $rows1[0];
                return $page;
            }
        }

        if ($pageType != 'public') {
            $rows2 = db()->query('SELECT * FROM cms_pages where ' . $req, $data)->fetchAll();
            if (count($rows2) == 1) {
                $page = $rows2[0];
                return $page;
            }
        }

        return false;
        
    }
    
    public function getPages( $array = 1, $type = 'all' ) {
        
        $data = array();
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                if (!empty($req)) {
                    $req .= ' AND ';
                }
                $req .= $key.' = ?';
                $data[] = $value;
            }
        } else if ($array == 1) {
            $req = "1";
        } else {
            throw new Exception('INVALID_DATA');
        }

        // Get database
        if ($type != 'admin') {
            $rows1 = db()->query('SELECT * FROM cms_public_pages where ' . $req . ' ORDER BY position', $data)->fetchAll();
            if (count($rows1) >= 1) {
                return $rows1;
            }
        }

        if ($type != 'public') {
            $rows2 = db()->query('SELECT * FROM cms_pages where ' . $req, $data)->fetchAll();
            if (count($rows2) >= 1) {
                return $rows2;
            }
        }

        return false;
        
    }

    public function setOrder($order) {
        $i = 1;
        foreach ($order as $id) {
            if (ctype_digit($id)) {
                $req = db()->update('cms_public_pages')->with(array(
                    'position' => $i
                ))->where('id', (int) $id)->run();
                if (!$req)
                    return false;
            }
            $i++;
        }
        return true;
    }

    public function setParents($parents) {
        $req = db()->update('cms_public_pages')->with(array(
            'parent_id' => 0
        ))->run(true);
        if (!$req) return false;
        foreach ($parents as $id => $childs) {
            if (ctype_digit($id) or is_int($id)) {
                $id = intval($id);
                foreach ($childs as $childId) {
                    if (ctype_digit($childId) or is_int($childId)) {
                        $childId = intval($childId);
                        $req = db()->update('cms_public_pages')->with(array(
                            'parent_id' => $id
                        ))->where('id', $childId)->run(true);
                        if (!$req) return false;
                    }
                }
            }
        }
        return true;
    }
    
    public function getParents() {
        return db()->select()->from('cms_public_pages')->where('parent_id', 0)->andWhere('state', 0)->orderBy('position')->fetchAll();
    }
    
    public function getChilds($parent_id) {
        if (is_numeric($parent_id) or $parent_id == 0) {
            $childs = db()->select()->from('cms_public_pages')->where('parent_id', $parent_id)->orderBy('position')->fetchAll();
            return $childs;
        } else {
            throw new Exception ('INVALID_DATA');
            return false;
        }
    }

    public function slugExist($req, $isChild = null, $isParent = false) {
        if (preg_match('/^([a-z_\-\s0-9\.\/]+$)/', $req)) {
            if ($isChild === null) {
                $public_req = db()->select('slug')->from('cms_public_pages')->where('slug', $req)->andWhere('type', '!=', 'link')->count();
                $pages_req = db()->select('slug')->from('cms_pages')->where('slug', $req)->count();
            } else if ($isChild === true) {
                $public_req = db()->select('slug')->from('cms_public_pages')->where('slug', $req)->andWhere('parent_id', '!=', 0)->andWhere('is_parent', 0)->andWhere('type', '!=', 'link')->count();
            } else if ($isChild === false and $isParent === false) {
                $public_req = db()->select('slug')->from('cms_public_pages')->where('slug', $req)->andWhere('parent_id', 0)->andWhere('is_parent', 0)->andWhere('type', '!=', 'link')->count();
                $pages_req = db()->select('slug')->from('cms_pages')->where('slug', $req)->count();
            } else if ($isChild === false and $isParent === true) {
                $public_req = db()->select('slug')->from('cms_public_pages')->where('slug', $req)->andWhere('parent_id', 0)->andWhere('is_parent', 1)->andWhere('type', '!=', 'link')->count();
            } else {
                throw new Exception('INVALID_DATA'); 
                return true;
            }
            if (($pages_req + $public_req) >= 1) {
                return true;
            }
        }
        return false;
    }

    public function getHomePage($value = null) {
        $rows = db()->select()->from('cms_public_pages')->where('home', 1)->fetchAll();
        $hp = $rows[0];
        if (count($rows) == 0) {
            return false;
        }
        if (is_null($value))
            return $hp;
        return $hp[$value];
    }

    public function setHome($pageId = null) {
        if (is_null($pageId))
            $pageId = $this->id;
        $rows = db()->select()->from('cms_public_pages')->where('id', $pageId)->fetchAll();
        if (count($rows) == 1) {
            if ($rows[0]['is_parent'] != 1) {
                db()->update('cms_public_pages')->with(array(
                    'home' => 0
                ))->where('home', 1)->save();
                db()->update('cms_public_pages')->with(array(
                    'home' => 1
                ))->where('id', $pageId)->save();
                return true;
            }
        }
        return false;
    }
    
    public function isChild() {
        return (!$this->is_parent == 0);
    }
    
    public function getSidebar() {
        return db()->select()->from('cms_sidebar')->orderBy('position')->fetchAll();
    }
    
    public function addSidebar($pluginName, $returnId = false) {
        $app = new App;
        $plugins = $app->getPlugins();
        if (isset($plugins[$pluginName]) and $plugins[$pluginName]['type'] === "sidebar") {
            $stmt = db()->select('MAX(position) as MAX')->from('cms_sidebar')->fetch();
            $pos = $stmt['MAX']+1;
            $id = db()->insert('cms_sidebar')->with(array(
                'name' => '',
                'plugin' => $pluginName,
                'position' => $pos,
                'data' => ''
            ))->save()->lastInsertId();
            if ($returnId)
                return $id;
            return (bool) $id;
        }
        return false;
    }
    
    public function setSidebarOrder($order) {
        $i = 1;
        foreach ($order as $id) {
            if (ctype_digit($id)) {
                $id = intval($id);
                $req = db()->update('cms_sidebar')->with(array(
                    'position' => $i
                ))->where('id', $id)->run(true);
                if (!$req)
                    return false;
            }
            $i++;
        }
        return true;
    }
    
    public function updateSidebar($id, $title, $data) {
        $id = (int) $id;
        if ($id == 0) return false;
        $rows = db()->update('cms_sidebar')->with(array(
            'name' => $title,
            'data' => $data
        ))->where('id', $id)->count();
        if ($rows == 1)
            return true;
        else
            return false;
    }
    
    public function deleteSidebar($id) {
        $id = (int) $id;
        if ($id == 0) return false;
        $rows = db()->delete('cms_sidebar')->where('id', $id)->count();
        if ($rows == 1)
            return true;
        else
            return false;
    }
    
    public function delete($pageId = null) {
        if (is_null($pageId) and !empty($this->id))
            $pageId = $this->id;
        $rows = db()->delete('cms_public_pages')->where('id', $pageId)->count();
        db()->update('cms_public_pages')->with(array(
            'parent_id' => 0
        ))->where('parent_id', $pageId)->run();
        if ($rows == 1) {
            return true;
        } else {
            return false;
        }
    }
    
    public function create($title, $desc = '', $is_parent = 0, $parent_id = 0, $home = 0, $content = '', $header = null, $header_data = '', $p_view = 0, $p_edit = 3, $type = 'custom', $type_data = '', $state = 0, $position = null) {

        $app = new App;
        
        if (strlen($title) < 4 OR strlen($title) > 64) {
            throw new Exception('INVALID_TITLE'); 
            return false;
        }
        
        if (strlen($desc) > 64) {
            throw new Exception('INVALID_DESC'); 
            return false;
        }
        
        if (!is_numeric($parent_id) or !is_numeric($is_parent)) {
            throw new Exception('INVALID_DATA'); 
            return false;
        }
        
        if (!is_numeric($p_view) or !is_numeric($p_edit) or !is_numeric($state)) {
            throw new Exception('INVALID_DATA'); 
            return false;
        }
        
        if (!($type == 'custom' or $type == 'native' or $type == 'plugin')) {
            throw new Exception('INVALID_DATA'); 
            return false;
        }
        
        if ($position === null) {
            $pos = db()->select('MAX(position) as POS')->from('cms_public_pages')->fetch();
            $position = $pos['POS'] + 1;
        }

        if ($header === null) {
            $header = with(new Config)->get('user.settings.default_header', '');
            $plugins = $app->getPlugins();
            if (!isset($plugins[$header]) or $plugins[$header]['type'] !== 'header') {
                $header = '';
            }
        }
        
        // $max = db()->select('MAX(id) as MAX')->from('cms_public_pages')->fetch();
        // $id = $max['MAX'] + 1;
        
        $slug = slug($title);
        $suffix = '';
        $i = 1;
        
        while ($this->slugExist($slug.$suffix)) {
            $i++;
            $suffix = '-'.$i;
        }

        $slug = $slug . $suffix;

        if (strlen($slug) < 3 or strlen($slug) > 64) {
            throw new Exception('INVALID_DATA'); 
            return false;
        }
        
        $params = array(
            // 'id' => $id,
            'title' => $title,
            'desc' => $desc,
            'slug' => $slug,
            'home' => $home,
            'is_parent' => $is_parent,
            'parent_id' => $parent_id,
            'p_view' => $p_view,
            'p_edit' => $p_edit,
            'content' => $content,
            'type' => $type,
            'type_data' => $type_data,
            'state' => $state,
            'header' => $header,
            'header_data' => $header_data,
            'position' => $position
        );

        foreach ($params as $key => $value) {
            $this->$key = $value;
        }

        $id = db()->insert('cms_public_pages')->with($params)->save()->lastInsertId();
        $this->id = $id;

        if ($id)
            return true;
        return false;
    }


    public function update($id, $data) {

        $id = (int) $id;

        foreach ($data as $key => $value) {
            $$key = $value;
        }

        $currentPage = new Page;
        $result = $currentPage->setup($id, 'id', 'public'); 
        if (!$result) {
            throw new Exception('INVALID_DATA');                 
        }

        if (isset($title) and (strlen($title) < 4 OR strlen($title) > 64)) {
            throw new Exception('INVALID_TITLE'); 
            return false;
        }
        
        if (isset($desc) and strlen($desc) > 64) {
            throw new Exception('INVALID_DESC'); 
            return false;
        }
        
        if (isset($parent_id) and !is_numeric($parent_id) or isset($data['is_parent']) and !is_numeric($is_parent)) {
            throw new Exception('INVALID_DATA'); 
            return false;
        }

        if (isset($data['is_parent']) && $is_parent == 1) {
            if ($currentPage->get('parent_id') != 0) {
                throw new Exception('HAS_A_PARENT'); 
                return false;
            }
            if ($currentPage->get('home') == 1) {
                throw new Exception('IS_HOMEPAGE'); 
                return false;
            }
        } else if (isset($data['is_parent']) && $is_parent == 0) {
            $result = $currentPage->getPages(array('parent_id' => $id));
            if ($result !== false) {
                throw new Exception('HAS_CHILD'); 
                return false;
            }
        }
        
        if (isset($p_view) and !is_numeric($p_view) or isset($data['p_edit']) and !is_numeric($p_edit) or isset($data['state']) and !is_numeric($state)) {
            throw new Exception('INVALID_DATA'); 
            return false;
        }
        
        if (isset($type) and !($type === 'custom' or $type === 'native' or $type === 'plugin' or $type === 'link')) {
            throw new Exception('INVALID_DATA'); 
            return false;
        }
        
        if (isset($position) and $position === null) {
            $pos = db()->select('MAX(position) as POS')->from('cms_public_pages')->fetch();
            $position = $pos['POS'] + 1;
        }

        if (isset($header) and $header === null) {
            $header = with(new Config)->get('user.settings.default_header', '');
        }
        
        $rows = db()->select()->from('cms_public_pages')->where('id', $id)->count();
        if ($rows !== 1) {
            throw new Exception('INVALID_DATA');
            return false;
        }
        
        if (isset($title)) {
            $slug =  slug($title);
            $suffix = '';
            $i = 1;

            $newPage = new Page;

            do {
                $exists = $newPage->setup($slug.$suffix);
                if ($exists) {
                    if ((int) $newPage->get('id') === $id) {
                        $exists = false;
                    } else {
                        $i++;
                        $suffix = '-'.$i;                    
                    }                    
                }
            } while($exists);

            $slug = $slug . $suffix;

/*
            $newPage = new Page;
            $exists = $newPage->setup($slug);

            if ($exists and $newPage->get('id') != $id) {
                throw new Exception('SLUG_ALREADY_SAVED'); 
                return false;
            }

            if (strlen($slug) < 3 or strlen($slug) > 64) {
                throw new Exception('INVALID_DATA'); 
                return false;
            }
*/
            $data['slug'] = $slug;
        }
        
        return (bool) db()->update('cms_public_pages')->with($data)->where('id', $id)->run(true);
    }

}