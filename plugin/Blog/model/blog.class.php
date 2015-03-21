<?php

defined('IN_ENV') or die;

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
    }

    public function setup($id) {
        
        // Get database
        $rows = db()->select()->from('plugin_blog_posts')->where('id', $id)->limit(0, 10)->fetchAll();
        
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
        
        if (!is_numeric($page) or $page <= 0 or $page > 9999) {
            throw new Exception('INVALID_DATA'); 
            return false;
        }
        
        $page = ($page-1)*5;
        
        // Get database
        if ($showHidden) {
            $rows = db()->select()->from('plugin_blog_posts')->orderBy('date', 'DESC')->limit($page, 5)->fetchAll();
        } else {
            $rows = db()->select()->from('plugin_blog_posts')->where('state', 0)->orderBy('date', 'DESC')->limit($page, 5)->fetchAll();
        }
        
        if (count($rows) == 0) {
            throw new Exception('NO_ARTICLE'); 
            return false;
        } else {
            return $rows;
        }
        
    }

    public function getArticle($id, $showHidden = false) {

        if (!is_numeric($id) or $id <= 0 or $id > 99999) {
            throw new Exception('INVALID_DATA'); 
            return false;
        }
        
        // Get database
        if ($showHidden) {
            $rows = db()->select()->from('plugin_blog_posts')->where('id', $id)->fetchAll();
        } else {
            $rows = db()->select()->from('plugin_blog_posts')->where('id', $id)->andWhere('state', 0)->fetchAll();
        }

        if (count($rows) == 0) {
            throw new Exception('NO_ARTICLE'); 
            return false;
        } else {
            return $rows[0];
        }
        
    }

    public function deleteUser($id) {
        if (!ctype_digit($id) and !is_int($id) or $id <= 0) return false;

        db()->delete('plugin_blog_posts')->where('author_id', $id)->run();
        db()->delete('plugin_blog_comments')->where('author_id', $id)->run();
        return true;
    }

    public function getComments($id) {
        
        if (!is_numeric($id) or $id <= 0) {
            throw new Exception('INVALID_DATA'); 
            return false;
        }
        
        // Get database
        return db()->select()->from('plugin_blog_comments')->where('type', 'comment')->andWhere('state', 0)->andWhere('post_id', $id)->orderBy('date', 'DESC')->fetchAll();
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
        
        $articleExist = db()->select('id')->from('plugin_blog_posts')->where('id', $post_id)->andWhere('state', 0)->count();

        if ($articleExist !== 1) {
            throw new Exception('UNKNOW_ARTICLE'); 
            return false;
        }
        
        $lastPost = db()->select('MAX(date) as LAST')->from('plugin_blog_comments')->where('author_id', $author_id)->fetch();
        $lastPost = $lastPost['LAST'];

        if ($lastPost > time()-60) {
            throw new Exception('TOO_RECENT_COMMENT'); 
            return false;
        }
        
        // $max = db()->select('MAX(id) as MAX')->from('plugin_blog_comments')->fetch();
        // $id = $max['MAX'] + 1;
        
        if ($date == 0) {
            $date = time();
        }
            
        $params = array(
            // 'id' => $id,
            'type' => 'comment',
            'author_id' => $author_id,
            'post_id' => $post_id,
            'content' => $content,
            'state' => $state,
            'date' => $date
        
        );

        return (bool) db()->insert('plugin_blog_comments')->with($params)->run(true);
        
        /*
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
        */
    }
  
    public function deleteComment($id, $post_id, $checkUserPerm = false) {
        $rows = db()->select()->from('plugin_blog_comments')->where('id', $id)->fetchAll();
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
        
        $rows = db()->delete('plugin_blog_comments')->where('id', $id)->count();
        if ($rows == 1) {
            return true;
        } else {
            throw new Exception('UNKNOW_COMMENT'); 
            return false;
        }
    }

    public function addArticle($bbcode, $title, $author_id, $picture = '', $state = 0, $date = 0) {

        if (strlen($title) < 3 OR strlen($title) > 64) {
            throw new Exception('INVALID_TITLE'); 
            return false;
        }
        
        if (!is_numeric($author_id)) {
            throw new Exception('INVALID_AUTHOR'); 
            return false;
        }
        
        if ($picture !== '' and strpos($picture, 'https://') !== 0 and strpos($picture, '//') !== 0 and strpos($picture, 'http://') !== 0)  {
            throw new Exception('INVALID_PICTURE'); 
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

        $bbcode = e($bbcode);
        
        $slug = slug($title);
        
        // $max = db()->select('MAX(id) as MAX')->from('plugin_blog_posts')->fetch();
        // $id = $max['MAX'] + 1;
        
        if ($date == 0) {
            $date = time();
        }
        
        $app = new App;
            
        $params = array(
            // 'id' => $id,
            'title' => $title,
            'slug' => $slug,
            'bbcode' => $bbcode,
            'html' => $app->bbcode2html($bbcode),
            'picture' => $picture,
            'author_id' => $author_id,
            'date' => $date,
            'state' => $state
        
        );

        return (bool) db()->insert('plugin_blog_posts')->with($params)->run(true);
        
        /*$parameters = array();
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
        return true;*/
    }
  
    public function editArticle($id, $bbcode, $picture = '', $title = 0, $author_id = 0, $date = 0, $state = 0) {

        if ($title !== 0 and (strlen($title) < 3 OR strlen($title) > 64)) {
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
        
        if ($picture !== '' and (strpos($picture, 'https://') !== 0 and strpos($picture, '//') !== 0 and strpos($picture, 'http://') !== 0))  {
            throw new Exception('INVALID_PICTURE'); 
            return false;
        }
        
        if ($date !== 0 and (!is_numeric($date))) {
            throw new Exception('INVALID_DATE'); 
            return false;
        }
        
        $app = new App;
        
        if ($title !== 0) {
            $slug = slug($title);
        }
        
        $rows = db()->select('id')->from('plugin_blog_posts')->where('id', $id)->count();
        if ($rows !== 1) {
            throw new Exception('UNKNOW_ARTICLE'); 
            return false;
        }

        $bbcode = e($bbcode);
        
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

        foreach($array as $key => $value)
            $this->$key = $value;

        db()->update('plugin_blog_posts')->with($array)->where('id', $id)->run();
        return true;
        
        /*$parameters = array();
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
        */
    }
  
    public function deleteArticle($id) {
        $rows = db()->delete('plugin_blog_posts')->where('id', $id)->count();
        if ($rows >= 1) {
            db()->delete('plugin_blog_comments')->where('post_id', $id)->run();
            return true;
        } else {
            throw new Exception('UNKNOW_ARTICLE'); 
            return false;
        }
    }
}
