<?php

class Blog {

    private $id;
    private $title;
    private $slug;
    private $bbcode;
    private $html;
    private $picture;
    private $author_id;
    private $date;
    private $state;

    public function __construct() {
        $app = new App;
        $tableExist1 = $app->query('SELECT id FROM plugin_blog_posts WHERE 0');
        $tableExist2 = $app->query('SELECT id FROM plugin_blog_comments WHERE 0');
        if ($tableExist1 == false or $tableExist2 == false) {
            $app->createTables( PATH_PLUGIN . 'model/tables.sql' );
        }
    }

    public function setup($id) {
        
        $app = new App;
        
        // Get database
        $rows = $app->query('SELECT * FROM plugin_blog_posts WHERE id = ? LIMIT 0, 10', array($id))->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($rows) == 1) {
            $article = $rows[0];
            foreach($article as $key => $value) {
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

    public function getList($page, $showHidden) {
        
        $app = new App;
        
        if (!is_numeric($page) or $page <= 0 or $page > 9999) {
            throw new Exception('INVALID_DATA'); 
            return false;
        }
        
        $page = ($page-1)*5;
        
        // Get database
        if ($showHidden) {
            $rows = $app->query('SELECT * FROM plugin_blog_posts WHERE 1 ORDER BY date DESC LIMIT '.$page.', 5')->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $rows = $app->query('SELECT * FROM plugin_blog_posts WHERE state = 0 ORDER BY date DESC LIMIT '.$page.', 5')->fetchAll(PDO::FETCH_ASSOC);
        }
        
        if (count($rows) == 0) {
            throw new Exception('NO_ARTICLE'); 
            return false;
        } else {
            return $rows;
        }
        
    }

    public function getArticle($id, $showHidden) {
        
        $app = new App;
        
        if (!is_numeric($id) or $id <= 0 or $id > 99999) {
            throw new Exception('INVALID_DATA'); 
            return false;
        }
        
        // Get database
        if ($showHidden) {
            $rows = $app->query('SELECT * FROM plugin_blog_posts WHERE id = ?', $id)->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $rows = $app->query('SELECT * FROM plugin_blog_posts WHERE id = ? AND state = 0', $id)->fetchAll(PDO::FETCH_ASSOC);
        }
        
        if (count($rows) == 0) {
            throw new Exception('NO_ARTICLE'); 
            return false;
        } else {
            return $rows[0];
        }
        
    }

    public function getComments($id) {
        
        $app = new App;
        
        if (!is_numeric($id) or $id <= 0) {
            throw new Exception('INVALID_DATA'); 
            return false;
        }
        
        // Get database
        $comments = $app->query('SELECT * FROM plugin_blog_comments WHERE type="comment" AND state = 0 AND post_id = ? ORDER BY date DESC', $id);
        return $comments;
    }

    public function addComment($content, $author_id, $post_id, $date = 0, $state = 0) {
    
        $content = trim($content);
        
        if (strlen($content) < 10 OR strlen($content) > 500) {
            throw new Exception('CONTENT_LENGHT'); 
            return false;
        }
        
        if (substr_count( $content, "\n" ) > 5) {
            throw new Exception('TOO_MUCH_NEWLINE'); 
            return false;
        }

        if (!is_numeric($author_id) or $author_id <= 0) {
            throw new Exception('INVALID_DATA'); 
            return false;
        }

        if ((!is_numeric($date) or $date <= 0) and $date !== 0) {
            throw new Exception('INVALID_DATA'); 
            return false;
        }
        
        $app = new App;
        
        $rows = $app->query('SELECT id FROM plugin_blog_posts WHERE id = ? AND state = 0', $post_id)->fetchAll(PDO::FETCH_ASSOC);
        $articleExist = count($rows);

        if ($articleExist !== 1) {
            throw new Exception('UNKNOW_ARTICLE'); 
            return false;
        }
        
        $lastPost_req = $app->query('SELECT MAX(date) as LAST FROM plugin_blog_comments WHERE author_id = ?', $author_id);
        $lastPost = $lastPost_req->fetch();
        $lastPost = $lastPost['LAST'];

        if ($lastPost > time()-60) {
            throw new Exception('TOO_RECENT_COMMENT'); 
            return false;
        }
        
        $max_req = $app->query('SELECT MAX(id) as MAX FROM plugin_blog_comments WHERE 1');
        $max = $max_req->fetch();
        $id = $max['MAX'] + 1;
        
        if ($date == 0) {
            $date = time();
        }
            
        $array = array(
            'id' => $id,
            'type' => 'comment',
            'author_id' => $author_id,
            'post_id' => $post_id,
            'content' => $content,
            'state' => $state,
            'date' => $date
        
        );
        
        $parameters = array();
        foreach($array as $key => $value) {
            $parameters[] = $value;
            if ( isset($query1) ) {
                $query1 .= ', ' . $key;
                $query2 .= ', ?';
            } else {
                $query1 .= $key;
                $query2 .= '?';
            }
        }
 
        $query_result = $app->query('INSERT INTO plugin_blog_comments('.$query1.') VALUES ('.$query2.')', $parameters);
        return true;

    }
  
    public function deleteComment($id, $post_id, $checkUserPerm = false) {
        $app = new App;
        $rows = $app->query('SELECT * FROM plugin_blog_comments WHERE id = ?', $id)->fetchAll(PDO::FETCH_ASSOC);
        if (count($rows) !== 1) {
            throw new Exception('UNKNOW_COMMENT'); 
            return false;
        }
        
        $query = $rows[0];
        if ($query['post_id'] != $post_id) {
            throw new Exception('UNKNOW_COMMENT'); 
            return false;
        }
        
        if ($checkUserPerm) {
            $user = new User;
            $page = new Page;
            $user->setup();
            $page->setup();
            if ($user->get('permission') < $page->get('p_edit') and $user->get('id') != $query['author_id']) {
                throw new Exception('NO_PERMISSION'); 
                return false;
            }
        }
        
        $rows = $app->query('DELETE FROM plugin_blog_comments WHERE id = ?', $id);
        if ($rows->rowCount() == 1) {
            return true;
        } else {
            throw new Exception('UNKNOW_COMMENT'); 
            return false;
        }
    }

    public function addArticle($bbcode, $title, $author_id, $picture = '', $state = 0, $date = 0) {

        if (strlen($title) < 3 OR strlen($title) > 32) {
            throw new Exception('INVALID_TITLE'); 
            return false;
        }
        
        if (!is_numeric($author_id)) {
            throw new Exception('INVALID_AUTHOR'); 
            return false;
        }
        
        $user = new User;
        if ($user->setup($author_id, 'id') === false) {
            throw new Exception('INVALID_AUTHOR'); 
            return false;
        }
        
        if (empty($bbcode)) {
            throw new Exception('NO_CONTENT'); 
            return false;
        }
        
        $str = strtolower( $title );
        $str = htmlentities($str, ENT_NOQUOTES, 'utf-8');
        $str = preg_replace('#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
        $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str);
        $str = preg_replace('#&[^;]+;#', '', $str);
        $str = strtr( $str, ' ', '-');
        $slug =  preg_replace('/[^A-Za-z0-9-]+/', '', $str);
        $slug =  trim($slug, '-');
        $app = new App;
        /*$alreadySaved = $app->query('SELECT id FROM plugin_blog_posts WHERE slug = ?', $slug);
        if ($alreadySaved == false) {
            throw new Exception('ERROR_DATABASE'); 
            return false;
        }
        if ($alreadySaved->rowCount() == 1) {
            throw new Exception('SLUG_ALREADY_SAVED'); 
            return false;
        }*/
        
        $max_req = $app->query('SELECT MAX(id) as MAX FROM plugin_blog_posts WHERE 1');
        $max = $max_req->fetch();
        $id = $max['MAX'] + 1;
        
        if ($date == 0) {
            $date = time();
        }
            
        $array = array(
            'id' => $id,
            'title' => $title,
            'slug' => $slug,
            'bbcode' => $bbcode,
            'html' => $app->bbcode2html($bbcode),
            'picture' => $picture,
            'author_id' => $author_id,
            'date' => $date,
            'state' => $state
        
        );
        
        $parameters = array();
        foreach($array as $key => $value) {
            $this->$key = $value;
            $parameters[] = $value;
            if ( isset($query1) ) {
                $query1 .= ', ' . $key;
                $query2 .= ', ?';
            } else {
                $query1 .= $key;
                $query2 .= '?';
            }
        }
        
        $query_result = $app->query('INSERT INTO plugin_blog_posts('.$query1.') VALUES ('.$query2.')', $parameters);
        return true;
    }
  
    public function editArticle($id, $bbcode, $picture = '', $title = 0, $author_id = 0, $date = 0, $state = 0) {

        if ($title !== 0 and (strlen($title) < 3 OR strlen($title) > 32)) {
            throw new Exception('INVALID_TITLE'); 
            return false;
        }
        
        if ($author_id !== 0 and (!is_numeric($author_id))) {
            throw new Exception('INVALID_AUTHOR'); 
            return false;
        }
        
        $user = new User;
        if ( $author_id !== 0 and $user->setup($author_id, 'id') === false) {
            throw new Exception('INVALID_AUTHOR'); 
            return false;
        }
        
        if ($date !== 0 and (!is_numeric($date))) {
            throw new Exception('INVALID_DATE'); 
            return false;
        }
        
        $app = new App;
        
        if ($title !== 0) {
            $str = strtolower( $title );
            $str = htmlentities($str, ENT_NOQUOTES, 'utf-8');
            $str = preg_replace('#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
            $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str);
            $str = preg_replace('#&[^;]+;#', '', $str);
            $str = strtr( $str, ' ', '-');
            $slug =  preg_replace('/[^A-Za-z0-9-]+/', '', $str);
            $slug =  trim($slug, '-');
            
            /*$alreadySaved = $app->query('SELECT id FROM plugin_blog_posts WHERE slug = ? AND id != ?', array($slug, $id));
            if ($alreadySaved == false) {
                throw new Exception('ERROR_DATABASE'); 
                return false;
            }
            if ($alreadySaved->rowCount() == 1) {
                throw new Exception('SLUG_ALREADY_SAVED'); 
                return false;
            }*/
        }
        
        $rows = $app->query('SELECT id FROM plugin_blog_posts WHERE id = ?', $id)->fetchAll(PDO::FETCH_ASSOC);
        $postsExists = count($rows);
        if ($postsExists !== 1) {
            throw new Exception('UNKNOW_ARTICLE'); 
            return false;
        }
        
        $array = array(
            'id' => $id,
            'bbcode' => $bbcode,
            'html' => $app->bbcode2html($bbcode)
        );
        
        if ($title !== 0) {
            $array['title'] = $title;
            $array['slug'] = $slug;
        }
        
        if ($picture !== 0) {
            $array['picture'] = $picture;
        }
        
        if ($author_id !== 0) {
            $array['author_id'] = $author_id;
        }
        
        if ($date !== 0) {
            $array['date'] = $date;
        }
        
        if ($state !== 0) {
            $array['state'] = $state;
        }
        
        $parameters = array();
        foreach($array as $key => $value) {
            $this->$key = $value;
            $parameters[] = $value;
            if ( isset($query) ) {
                $query .= ', ' . $key . '=' . '?';
            } else {
                $query = $key . '=' . '?';
            }
        }
        $parameters[] = $id;
        
        $query_result = $app->query('UPDATE plugin_blog_posts SET '.$query.' WHERE id = ?', $parameters);
        return true;

    }
  
    public function deleteArticle($id) {
        $app = new App;
        $rows = $app->query('DELETE FROM plugin_blog_posts WHERE id = ?', $id);
        if ($rows->rowCount() >= 1) {
            $app->query('DELETE FROM plugin_blog_comments WHERE post_id = ?', $id);
            return true;
        } else {
            throw new Exception('UNKNOW_ARTICLE'); 
            return false;
        }
    }
}

?>