<?php

class Page {

    private $id;
    private $title;
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
    
    public function setup( $data = REQUEST_SLUG, $type = null ) {
        
        $app = new App;
        
        if ( empty( $type ) or !preg_match('/^[a-z]+$/', $type) ) {
            $type = 'slug';
        }
        
        // Get database
        $rows1 = $app->query('SELECT * FROM cms_pages where '.$type.' = ?', array($data))->fetchAll(PDO::FETCH_ASSOC);
        $rows2 = $app->query('SELECT * FROM cms_public_pages where '.$type.' = ?', array($data))->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($rows1) == 1) {
            $page = $rows1[0];
            foreach($page as $key => $value) {
                $this->$key = $value;
            }
            return true;
        } else if (count($rows2) == 1) {
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
    
    public function getPage( $array ) {
        
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
        $rows1 = $app->query('SELECT * FROM cms_public_pages where ' . $req, $data)->fetchAll(PDO::FETCH_ASSOC);
        $rows2 = $app->query('SELECT * FROM cms_pages where ' . $req, $data)->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($rows1) == 1) {
            $page = $rows1[0];
            return $page;
        } else if (count($rows2) == 1) {
            $page = $rows2[0];
            return $page;
        } else {
            return false;
        }
        
    }
    
    public function getParents() {
        $app = new App;
        $page_req = $app->query('SELECT id, title, slug, home, is_parent, parent_id FROM cms_public_pages where parent_id = 0 AND state = 0 ORDER BY position');
        $parents = $page_req->fetchAll();
        return $parents;
    }
    
    public function getChilds($parent_id) {
        $app = new App;
        if (is_numeric($parent_id) or $parent_id == 0) {
            $page_req = $app->query('SELECT * FROM cms_public_pages where parent_id = ? ORDER BY position', $parent_id);
            $childs = $page_req->fetchAll();
            return $childs;
        } else {
            throw new Exception ('INVALID_DATA');
            return false;
        }
    }
    
    public function getSidebars() {
        $app = new App;
        $page_req = $app->query('SELECT * FROM cms_sidebars where 1 ORDER BY position');
        $parents = $page_req->fetchAll(PDO::FETCH_ASSOC);
        return $parents;
    }

    public function slugExist($req, $isChild = null, $isParent = false) {
        if (preg_match('/^([a-z_\-\s0-9\.\/]+$)/', $req)) {
            $app = new App;
            if ($isChild === null) {
                $public_req = $app->query('SELECT slug FROM cms_public_pages WHERE slug = ?', $req);
                $pages_req = $app->query('SELECT slug FROM cms_pages WHERE slug = ?', $req);
            } else if ($isChild === true) {
                $public_req = $app->query('SELECT slug FROM cms_public_pages WHERE slug = ? AND parent_id != 0 AND is_parent = 0', $req);
            } else if ($isChild === false and $isParent === false) {
                $public_req = $app->query('SELECT slug FROM cms_public_pages WHERE slug = ? AND parent_id = 0 AND is_parent = 0', $req);
                $pages_req = $app->query('SELECT slug FROM cms_pages WHERE slug = ?', $req);
            } else if ($isChild === false and $isParent === true) {
                $public_req = $app->query('SELECT slug FROM cms_public_pages WHERE slug = ? AND parent_id = 0 AND is_parent = 1', $req);
            } else {
                throw new Exception('INVALID_DATA'); 
                return true;
            }
            $rows = $public_req->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($pages_req))
                $rows = $rows + $pages_req->fetchAll(PDO::FETCH_ASSOC);
            if (count($rows) >= 1) {
                return true;
            }
        }
        return false;
    }

    public function getHomePage($value) {
        $app = new App;
        $rows = $app->query('SELECT * FROM cms_public_pages WHERE home = 1')->fetchAll(PDO::FETCH_ASSOC);
        $hp = $rows[0];
        if (count($rows) == 0) {
            return false;
        }
        return $hp[$value];
    }
    
    public function isChild() {
        if ($this->is_parent == 0) {
            return false;
        } else {
            return true;
        }
    }
    
    public function create($title, $is_parent, $parent_id, $content = '', $header = '', $header_data = '', $p_view = 0, $p_edit = 3, $type = 'custom', $state = 0) {
        
        $app = new App;
        
        if (strlen($title) < 5 OR strlen($title) > 32) {
            throw new Exception('INVALID_TITLE'); 
            return false;
        }
        
        if (!is_numeric($parent_id) or !is_numeric($is_parent)) {
            throw new Exception('INVALID_PARENT'); 
            return false;
        }
        
        if (!is_numeric($p_view) or !is_numeric($p_edit) or !is_numeric($state)) {
            throw new Exception('INVALID_DATA'); 
            return false;
        }
        
        if (!($type == 'custom' or $type == 'native' or $type == 'plugin')) {
            throw new Exception('INVALID_TYPE'); 
            return false;
        }
        
        $max_req = $app->query('SELECT MAX(id) as MAX FROM cms_public_pages WHERE 1');
        $max = $max_req->fetch();
        $id = $max['MAX'] + 1;
        
        $str = strtolower( $title );
        $str = htmlentities($str, ENT_NOQUOTES, 'utf-8');
        $str = preg_replace('#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
        $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str);
        $str = preg_replace('#&[^;]+;#', '', $str);
        $str = strtr( $str, ' ', '-');
        $slug =  preg_replace('/[^A-Za-z0-9-]+/', '', $str);
        $slug =  trim($slug, '-');
        
        if ($app->slugExist($slug)) {
            throw new Exception('SLUG_ALREADY_SAVED'); 
            return false;
        }
        
        $array = array(
            'id' => $id,
            'title' => $title,
            'slug' => $slug,
            'is_parent' => $is_parent,
            'parent_id' => $parent_id,
            'p_view' => $p_view,
            'p_edit' => $p_edit,
            'content' => $content,
            'type' => $type,
            'type_data' => $type_data,
            'state' => $state,
            'header' => $header,
            'header_data' => $header_data
        
        );
        
        $parameters = array();
        foreach($array as $key => $value) {
            $this->$key = $value;
            $parameters[] = $value;
        }

        $query_result = $app->query('INSERT INTO cms_public_pages(id, title, slug, home, is_parent, parent_id, p_view, p_edit, content, type, type_data, state, header) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', $parameters);
        
        return true;        
    }

}