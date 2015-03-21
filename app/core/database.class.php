<?php

Class Database {

    /*
     * --------------------------------
     *            Variables
     * --------------------------------
     *
     */

    private $db;
    private $dbType;
    private $errMode = 'Exception';
    private $debug = false;

    private $lastQuery;
    private $locked = false;
    private $ifBlockExecuted = false;

    private $columns = '*';
    private $table;
    private $where;
    private $having;
    private $orderBy;
    private $groupBy;
    private $limit;
    private $offset;
    private $values;
    private $args;
    private $stmt;


    /*
     * --------------------------------
     *          Initialisation
     * --------------------------------
     *
     */

    public function __construct($type = null, $access = null, $options = array()) {
        if (!is_null($type)) $this->init($type, $access, $options);
    }

    public function init($type, $access, $options = array()) {
        if (strcasecmp($type, 'MySQL') === 0) {
            $this->initMySQL($access, $options);
        } else if (strcasecmp($type, 'SQLite') === 0) {
            $this->initSQLite($access, $options);
        } else {
            $this->throwError('Database type not supported');
        }
    }

    private function initMySQL($access, $options = array()) {
        $options += array(
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES "UTF8"'
        );
        $dsn = 'mysql:host='.$access[0].';dbname='.$access[1].';charset=utf8;';
        if (!empty($access[4])) $dsn .= 'port='.$access[4].';';
        try {
            $pdo = new PDO($dsn, $access[2], $access[3], $options);
        } catch (Exception $e) {
            $this->throwError('Error while connecting to database: '.$e->getMessage());
        }
        $this->dbType = 'MySQL';
        $this->db = $pdo;
    }

    private function initSQLite($access, $options = array()) {
        $options += array(
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false
        );
        try {
            $pdo = new PDO('sqlite:'.$access, null, null, $options);
        } catch (Exception $e) {
            $this->throwError('Error while connecting to database: '.$e->getMessage());
        }
        $pdo->exec('PRAGMA synchronous=OFF');
        $this->dbType = 'SQLite';
        $this->db = $pdo;
    }

    public function setErrorMode($mode) {
        if (strcasecmp($mode, 'Exception') === 0) {
            $this->errMode = 'Exception';
        } else if (strcasecmp($mode, 'Warning') === 0) {
            $this->errMode = 'Warning';
        } else if (strcasecmp($mode, 'Silent') === 0) {
            $this->errMode = 'Silent';
        } else {
            $this->throwError('Unknow error mode');
        }
    }


    /*
     * --------------------------------
     *           PDO Methods
     * --------------------------------
     *
     */

    public function getPDO(){
        return $this->db;
    }

    public function setPDO(PDO $pdo){
        return $this->db = $pdo;
    }

    public function setAttribute($key, $value) {
        $pdo = $this->db();
        $pdo->setAttribute($key, $value);
        $this->db = $pdo;
    }

    public function close() {
        $this->newQuery();
        unset($this->db);
        unset($this->dbType);
    }

    public function lastInsertId($name = null){
        return (int) $this->db->lastInsertId($name);
    }

    public function bind($value) {
        if (is_array($value)) {
            foreach ($value as $v) {
                $this->args[] = $v;
            }
        } else {
            $this->args[] = $value;
        }
        return $this;
    }


    /*
     * --------------------------------
     *           Debugging
     * --------------------------------
     *
     */

    public function debug($mode = true) {
        $this->debug = (bool) $mode;
        return $this;
    }


    /*
     * --------------------------------
     *         Main function
     * --------------------------------
     *
     */

    public function query($sql, $params = null) {
        if ($this->isLocked()) return $this;
        if ($this->debug)
            return $this->debugQuery($sql, $params);
        if (empty($params)) {
            $stmt = $this->db()->query($sql);
        } else {
            if (!is_array($params)) 
                $params = array($params);
            $stmt = $this->db()->prepare($sql);
            if ($params === array_values($params)) {
                $stmt->execute($params);
            } else {
                foreach ($params as $key => $value) {
                    if (is_bool($value))
                        $type = PDO::PARAM_BOOL;
                    else if (is_null($value))
                        $type = PDO::PARAM_NULL;
                    else if (is_int($value))
                        $type = PDO::PARAM_INT;
                    else
                        $type = PDO::PARAM_STR;
                    $stmt->bindValue(((strpos($key, ':') === 0 or is_int($key)) ? $key : ':'.$key), $value, $type);
                }
                $stmt->execute();
            }
        }
        $this->lastQuery = $stmt;
        return $stmt;
    }

    public function save($return = false) {
        if ($this->isLocked()) return $this;
        switch ($this->action) {
            case 'INSERT': $req = $this->buildInsert();
                           if ($return) return $req;
                           break;
            case 'UPDATE': $req = $this->buildUpdate();
                           if ($return) return $req;
                           break;
            case 'DELETE': $req = $this->buildDelete();
                           if ($return) return $req;
                           break;
            default:
                return $this->throwError('Invalid request: Unknow query type');
                break;
        }
        return $this;
    }

    public function run($return = false) {
        return $this->save($return);
    }

    public function fetchAll($mode = null) {
        if ($this->isLocked()) return array();
        if ($this->action !== 'SELECT')
            return $this->throwError('Invalid request: FetchAll called, but not in a select query');
        return $this->buildSelect()->fetchAll($mode);        
    }

    public function fetch($mode = null) {
        if ($this->isLocked()) return false;
        if ($this->action !== 'SELECT')
            return $this->throwError('Invalid request: Fetch called, but not in a select query');
        if (!isset($this->stmt))
            $this->stmt = $this->buildSelect();
        return $this->stmt->fetch($mode);
    }

    public function count($table = null) {
        if ($this->isLocked()) return 0;
        if ($table) {
            return (int) $this->select('COUNT(*) AS N')->from($table)->fetch(PDO::FETCH_OBJ)->N;
            // return (int) $this->query('SELECT COUNT(*) AS N FROM '.$table)->fetch(PDO::FETCH_OBJ)->N;
        } else {
            if ($this->action === 'SELECT') {
                return count($this->fetchAll());
            } else if (in_array($this->action, array('INSERT', 'UPDATE', 'DELETE'))) {
                if (isset($this->lastQuery)) {
                    return $this->lastQuery->rowCount();
                }
                return $this->save(true)->rowCount();
            } else {
                $this->throwError('Invalid request: Count called in an unexpected way');
            }
        }
    }

    public function get($auto_increment_column = 'id') {
        if ($this->isLocked()) return false;
        if ($this->isLocked()) return array();

        switch ($this->action) {
            case 'SELECT': return $this->buildSelect()->fetchAll();
                           break;
            case 'INSERT': $id = $this->lastInsertId();
                           if ($id === 0)
                               $this->throwError('Cannot retrieve last inserted row: no auto-increment field');
                           $table = $this->table;
                           return $this->select()->from($table)
                               ->ifSQLite()->where('rowid', $id)
                               ->ifMySQL()->where($auto_increment_column, $id)
                               ->endIf()
                               ->fetch();
                           break;
            case 'UPDATE': $where = $this->where;
                           $table = $this->table;
                           $args = $this->args;
                           $values = $this->values;
                           foreach ($values as $value)
                               array_shift($args);
                           $stmt = $this->select()->from($table);
                           $this->where = $where;
                           $this->args = $args;
                           return $stmt->fetchAll();
                           break;
            case 'DELETE': return false;
                           break;
            default:
                return $this->throwError('Invalid request: Unknow query type');
                break;
        }
        return $this;
    }


    /*
     * --------------------------------
     *          Conditionnals
     * --------------------------------
     *
     */

    public function __call($method, $arguments) {
        $name = strtolower($method);
        if ($name === 'if') {
            $this->ifBlockExecuted = (bool) $arguments[0];
            $this->lock(!$arguments[0]);
        } else if ($name === 'elseif') {
            $executeBlock = ($this->locked and $arguments[0] and !$this->ifBlockExecuted);
            if ($executeBlock)
                $this->ifBlockExecuted = true;
            $this->lock(!$executeBlock);
        } else if ($name === 'else') {
            $executeBlock = (!$this->ifBlockExecuted);
            if ($executeBlock)
                $this->ifBlockExecuted = true;
            $this->lock(!$executeBlock);
        } else if ($name === 'endif') {
            $this->lock(false);
            $this->ifBlockExecuted = false;
        } else if ($method === 'whereNotNull') {
            return $this->whereNull($arguments[0], true);
        } else if ($method === 'orWhereNotNull') {
            return $this->orWhereNull($arguments[0], true);
        } else if ($method === 'andWhereNotNull') {
            return $this->andWhereNull($arguments[0], true);
        } else {
            $this->throwError(sprintf('Call to undefined method %s::%s()', get_class($this), $method));
        }
        return $this;
    }

    public function dbType() {
        return $this->dbType;
    }

    public function isSQLite() {
        return (strcasecmp($this->dbType, 'SQLite') === 0);
    }

    public function isMySQL() {
        return (strcasecmp($this->dbType, 'MySQL') === 0);
    }

    public function ifMySQL() {
        $this->lock(!$this->isMySQL());
        return $this;
    }

    public function ifSQLite() {
        $this->lock(!$this->isSQLite());
        return $this;
    }

    public function end() {
        $this->lock(false);
        return $this;
    }

    public function lock($state) {
        $this->locked = (bool) $state;
        return $this;
    }

    public function isLocked() {
        return $this->locked;
    }


    /*
     * --------------------------------
     *           SQL methods
     * --------------------------------
     *
     */

    public function select($rows = '*') {
        $this->newQuery('SELECT');
        if (is_array($rows))
            $this->columns = implode(', ', $rows); 
        else
            $this->columns = $rows; 
        return $this;
    }

    public function from($table) {
        $this->table = $table;
        return $this;
    }

    public function insert($table = null) {
        $this->newQuery('INSERT');
        if (is_array($table))
            $this->with($table);
        else if (!is_null($table))
            $this->table = $table;
        return $this;
    }

    public function update($table = null) {
        $this->newQuery('UPDATE');
        if (is_array($table))
            $this->with($table);
        else if (!is_null($table))
            $this->table = $table;
        return $this;
    }

    public function delete($table = null) {
        $this->newQuery('DELETE');
        if (!is_null($table))
            $this->table = $table;
        return $this;
    }

    public function with($data) {
        if ($this->isLocked()) return $this;
        unset($this->args);
        unset($this->stmt);
        $this->columns = array();
        $this->values = array();
        $this->args = array();
        foreach ($data as $key => $value) {
            $this->columns[] = $key;
            $this->values[] = '?';
            $this->args[] = $value;
        }
        if (array_values($data) === $data) {
            unset($this->columns);
        }
        return $this;
    }

    public function values($data) {
        return $this->with($data);
    }

    public function into($table) {
        $this->table = $table;
        return $this->save();
    }

    public function in($table) {
        $this->table = $table;
        return $this->save();
    }


    /*
     * --------------------------------
     *           SQL filters
     * --------------------------------
     *
     */

    public function where($column = null, $operator = null, $value = null) {
        if ($this->isLocked()) return $this;
        unset($this->where);
        if (is_null($column))
            return $this;
        if (is_null($operator)) {
            $this->where = $column;
            return $this;
        }
        if (is_array($operator)) {
            $this->where = $column;
            return $this->bind($operator);
        }
        return $this->andWhere($column, $operator, $value);
    }

    public function andWhere($column, $operator, $value = null) {
        if ($this->isLocked()) return $this;
        if (is_null($value)) {
            $value = $operator;
            $operator = '=';
        }
        return $this->buildWhere('AND', $column, $operator, $value);
    }

    public function orWhere($column, $operator, $value = null) {
        if ($this->isLocked()) return $this;
        if (is_null($value)) {
            $value = $operator;
            $operator = '=';
        }
        return $this->buildWhere('OR', $column, $operator, $value);
    }

    public function whereNull($column, $not = false) {
        if ($this->isLocked()) return $this;
        $is = ($not ? 'IS NOT' : 'IS');
        return $this->buildWhere('AND', $column, $is, NULL);
    }

    public function orWhereNull($column, $not = false) {
        if ($this->isLocked()) return $this;
        $is = ($not ? 'IS NOT' : 'IS');
        return $this->buildWhere('OR', $column, $is, NULL);
    }

    public function andWhereNull($column, $not = false) {
        if ($this->isLocked()) return $this;
        $is = ($not ? 'IS NOT' : 'IS');
        return $this->buildWhere('AND', $column, $is, NULL);
    }

    public function orderBy($column = null, $order = 'ASC') {
        if ($this->isLocked()) return $this;
        unset($this->stmt);
        if (is_null($column)) {
            unset($this->orderBy);
        } else {
            if (!empty($this->orderBy))
                $this->orderBy .= ', ';
            $this->orderBy .= $column.' '.$order;
        }
        return $this;
    }

    public function groupBy($column = null) {
        if ($this->isLocked()) return $this;
        unset($this->stmt);
        if (is_null($column))
            unset($this->groupBy);
        else if (is_array($column))
            $this->groupBy = implode(', ', $column);
        else
            $this->groupBy = $column;
        return $this;
    }

    public function having($raw = null, $args = array()) {
        if ($this->isLocked()) return $this;
        unset($this->stmt);
        if (is_null($raw)) {
            unset($this->having);
        } else {
            $this->having = $raw;
            if (!is_array($args)) {
                $this->args[] = $args;
            } else {
                foreach ($args as $v) {
                    $this->args[] = $v;
                }
            }
        }
        return $this;
    }
    
    public function limit($a = null, $b = null) {
        if ($this->isLocked()) return $this;
        unset($this->stmt);
        if (is_null($a) and is_null($b))
            unset($this->limit, $this->offset);
        if (is_null($b)) {
            $this->limit = (int) $a;
        } else {
            $this->offset = (int) $a;
            $this->limit = (int) $b;            
        }
        return $this;
    }

    public function offset($a = null) {
        if ($this->isLocked()) return $this;
        unset($this->stmt);
        if (is_null($a))
            unset($this->offset);
        if (!is_null($a))
            $this->offset = (int) $a;
        return $this;
    }


    /*
     * --------------------------------
     *        Table management
     * --------------------------------
     *
     */

    public function create($table, $config, $override = false) {
        if ($this->isLocked()) return $this;
        if ($override)
            $this->drop($table);
        $sql = 'CREATE TABLE IF NOT EXISTS '.$table." (\n ";
        $sql .= implode(",\n ", $config);
        $sql .= "\n);";
        $this->query($sql);
        $this->table = $table;
        unset($this->stmt);
        return $this;
    }

    public function drop($table) {
        if ($this->isLocked()) return $this;
        $this->query('DROP TABLE IF EXISTS '.$table);
        return $this;
    }

    public function alter($table, $action) {
        if ($this->isLocked()) return $this;
        $this->query('ALTER TABLE '.$table.' '.$action);
        return $this;
    }

    public function getTable($table, $assoc = false) {
        if ($this->isMySQL()) {
            try {
                $columns = $this->query('DESC '.$table)->fetchAll();
            } catch(PDOException $e) {
                return null;
            }
            foreach ($columns as $c) {
                $add = array(
                    'name' => $c['Field'],
                    'type' => $c['Type'],
                    'null' => $c['Null'] === 'YES',
                    'default' => $c['Default'],
                    'primary' => $c['Key'] === 'PRI',
                    'autoincrement' => $c['Extra'] === 'auto_increment'
                );
                if ($assoc)
                    $return[$add['name']] = $add;
                else
                    $return[] = $add;
            }
            return $return;
        } else if ($this->isSQLite()) {
            $columns = $this->query('PRAGMA table_info('.$table.')')->fetchAll();
            if (empty($columns))
                return null;
            foreach ($columns as $c) {
                $add = array(
                    'name' => $c['name'],
                    'type' => $c['type'],
                    'null' => $c['notnull'] === '0',
                    'default' => $c['dflt_value'],
                    'primary' => $c['pk'] === '1',
                    'autoincrement' => $c['pk'] === '1'
                );
                if ($assoc)
                    $return[$add['name']] = $add;
                else
                    $return[] = $add;
            }
            return $return;
        }
        throw new Exception('Database type is not supported.');
    }

    public function hasTable($table) {
        if ($this->isMySQL()) {
            return count($this->query('SHOW TABLES LIKE "'.$table.'"')->fetchAll()) > 0;
        } else if ($this->isSQLite()) {
            return $this->select()->from('sqlite_master')->where('type', 'table')->andWhere('name', $table)->count() > 0;
        }
        throw new Exception('Database type is not supported.');
    }

    public function getColumns($table, $assoc = true) {
        return $this->getTable($table, $assoc);
    }

    public function hasColumn($table, $column) {
        $columns = $this->getTable($table, true);
        if (is_null($columns))
            return false;
        return (isset($columns[$column]) and !empty($columns[$column]));
    }

    public function getColumn($table, $column) {
        $columns = $this->getTable($table, true);
        if (is_null($columns) or !isset($columns[$column]) or empty($columns[$column]))
            return null;
        return $columns[$column];
    }


    /*
     * --------------------------------
     *         Private methods
     * --------------------------------
     *
     */

    private function db() {
        if (!isset($this->db))
            return $this->throwError('Database not initialized');        
        return $this->db;
    }

    private function newQuery($action = null) {
        unset(
            $this->columns,
            $this->where,
            $this->orderBy,
            $this->limit,
            $this->offset,
            $this->groupBy,
            $this->having,
            $this->values,
            $this->args,
            $this->stmt,
            $this->lastQuery
        );
        if (is_null($action))
            unset($this->action);
        else
            $this->action = $action;
    }

    private function buildWhere($logical, $column, $operator, $value) {
        if ($this->isLocked()) return $this;
        unset($this->stmt);
        if (!empty($this->where))
            $this->where .= ' '.$logical.' ';
        if (is_array($value)) {
            $this->where .= $column.' '.$operator.' ';
            if (stripos($operator, 'between') !== false) {
                $i = 0;
                foreach ($value as $v) {
                    if ($i++)
                        $this->where .= ' AND ';
                    $this->args[] = $v;
                    $this->where .= '?';
                }
            } else {
                $this->where .= '(';
                $i = 0;
                foreach ($value as $v) {
                    if ($i++)
                        $this->where .= ', ';
                    $this->args[] = $v;
                    $this->where .= '?';
                }
                $this->where .= ')';
            }
        } else if (is_null($value)) {
            $this->where .= $column.' '.$operator.' NULL';
        } else {
            $this->where .= $column.' '.$operator.' ?';
            $this->args[] = $value;            
        }
        return $this;
    }

    private function buildSelect() {
        if ($this->isLocked()) return;
        if (empty($this->table))
            return $this->throwError('Uncomplete request: Missing table name');
        if (empty($this->columns))
            $this->columns = '*';
        $sql = 'SELECT '.$this->columns.' FROM '.$this->table;
        if (empty($this->where))
            $sql .= ' WHERE 1';
        else
            $sql .= ' WHERE '.$this->where;
        if (!empty($this->groupBy))
            $sql .= ' GROUP BY '.$this->groupBy;
        if (!empty($this->having))
            $sql .= ' HAVING '.$this->having;
        if (!empty($this->orderBy))
            $sql .= ' ORDER BY '.$this->orderBy;
        if (!empty($this->limit))
            $sql .= ' LIMIT '.$this->limit;
        if (!empty($this->offset))
            $sql .= ' OFFSET '.$this->offset;
        return $this->query($sql, $this->args);
    }

    private function buildInsert() {
        if ($this->isLocked()) return;
        if (empty($this->table))
            return $this->throwError('Uncomplete request: Missing table name');
        if (empty($this->args))
            return $this->throwError('Uncomplete request: Missing values');
        if (empty($this->values) or count($this->values) < count($this->args)) {
            foreach ($this->args as $value) {
                $this->values[] = '?';
            }
        }
        $sql = 'INSERT INTO '.$this->table;
        if (!empty($this->columns)) {
            $sql .= ' (';
            $sql .= implode(', ', $this->columns);
            $sql .= ')';
        }
        $sql .= ' VALUES ('.implode(', ', $this->values).')';
        return $this->query($sql, $this->args);
    }

    private function buildUpdate() {
        if ($this->isLocked()) return;
        if (empty($this->table) or empty($this->args) or empty($this->columns))
            return $this->throwError('Uncomplete request');
        if (count($this->columns) > count($this->args))
            return $this->throwError('Invalid request: There are more columns to update than values');
        $sql = 'UPDATE '.$this->table.' SET ';
        foreach ($this->columns as $column) {
            $updateValues[] = $column.' = ?';
        }
        $sql .= implode(', ', $updateValues);
        if (empty($this->where))
            $sql .= ' WHERE 1';
        else
            $sql .= ' WHERE '.$this->where;
        return $this->query($sql, $this->args);
    }

    private function buildDelete() {
        if ($this->isLocked()) return;
        if (empty($this->table))
            return $this->throwError('Uncomplete request: Missing table name');
        $sql = 'DELETE FROM '.$this->table;
        if (empty($this->where))
            $sql .= ' WHERE 1';
        else
            $sql .= ' WHERE '.$this->where;
        return $this->query($sql, $this->args);
    }

    private function throwError($error, $mode = null) {
        if (is_null($mode))
            $mode = $this->errMode;
        if (strcasecmp($mode, 'Exception') === 0) {
            throw new Exception($error);
        } else if (strcasecmp($mode, 'Warning') === 0) {
            trigger_error($error, E_USER_ERROR);
        } else {
            return NULL;
        }
    }

    private function debugQuery($sql, $params) {
        echo PHP_EOL.'<b>Debugging:</b><pre>';
        echo 'SQL: "'.$sql.'"'.PHP_EOL;
        if (is_null($params)) {
            echo 'No args given.';
        } else if (!is_array($params)) {
            echo 'Args: ';
            echo PHP_EOL.'- [';
            echo str_replace('double', 'float', gettype($params));
            echo '] => '.$params;
        } else {
            echo 'Args: ';
            foreach ($params as $value) {
                echo PHP_EOL.'- [';
                echo str_replace('double', 'float', gettype($value));
                echo '] => '.$value;
            }                
        }
        echo PHP_EOL.PHP_EOL.'</pre>';
        $this->debug = false;
        return $this;
    }

}



class Table {

    private static $dbType;
    private static $supportedDb = array(
        'mysql' => true,
        'sqlite' => true
    );

    public static function dbType($set = null) {
        if ($set !== null)
            return self::$dbType = $set;
        if (isset(self::$dbType)) 
            return self::$dbType;
        if (defined('DATABASE'))
            $dbType = strtolower(DATABASE);
        else if (function_exists('db'))
            $dbType = strtolower(db()->dbType());
        else
            throw new Exception('Function db() not found.');
        if (!isset(self::$supportedDb[$dbType]))
            throw new Exception('Database type is not supported.');
        return self::$dbType = $dbType;
    }

    private static function choose($name, array $options, $array) {
        $type = $array[self::dbType()];
        if (isset($options['size']))
            $type = str_replace(' $', 
                '('.$options['size']
                .(isset($options['precision']) ? ', '.$options['precision'] : '')
                .')', $type);
        else
            $type = str_replace(' $', '', $type);
        if (isset($options['case'])) {
            if ($options['case'])
                $casePerDbType = array(
                    'mysql'  => ' BINARY',
                    'sqlite' => ''
                );
            else
                $casePerDbType = array(
                    'mysql'  => '',
                    'sqlite' => ' COLLATE NOCASE'
                );
            $type .= $casePerDbType[self::dbType()];
        }
        if (isset($options['null']))
            $type .= $options['null'] ? ' NULL' : ' NOT NULL';
        if (isset($options['rawDefault']))
            $type .= ' DEFAULT '.$options['rawDefault'];
        else if (isset($options['default'])) {
            $type .= ' DEFAULT ';
            if (is_string($options['default']))
                $type .= '"'.$options['default'].'"';
            else if (is_numeric($options['default']))
                $type .= $options['default'];
            else if (is_null($options['default']))
                $type .= 'NULL';
            else if (is_bool($options['default']))
                $type .= $options['default'] ? '1' : '0';
            else
                $type .= $options['default'];
        }
        return $name.' '.$type;
    }


    /*
     * --------------------------------
     *              Raw
     * --------------------------------
     *
     */

    public static function raw($name, $raw) {
        if (!is_array($raw))
            return $name.' '.$raw;
        if (isset($raw[self::dbType()]))
            return $name.' '.$raw[self::dbType()];
        throw new Exception('Database type ['.self::dbType().'] is not supported.');
    }


    /*
     * --------------------------------
     *           Primary Keys
     * --------------------------------
     *
     */

    public static function id($name = 'id', $options = array()) {
        return self::increment($name, $options);
    }

    public static function increment($name, array $options = array()) {
        return self::choose($name, $options, array(
            'mysql'   =>  'INT $ AUTO_INCREMENT PRIMARY KEY',
            'sqlite'  =>  'INTEGER PRIMARY KEY AUTOINCREMENT'
        ));
    }

    public static function autoIncrement($name, array $options = array()) {
        return self::increment($name, $options);
    }


    /*
     * --------------------------------
     *              Numbers
     * --------------------------------
     *
     */

    public static function int($name, array $options = array()) {
        return self::choose($name, $options, array(
            'mysql'   =>  'INT $',
            'sqlite'  =>  'INTEGER'
        ));
    }

    public static function integer($name, array $options = array()) {
        return self::int($name, $options);
    }

    public static function float($name, array $options = array()) {
        return self::choose($name, $options, array(
            'mysql'   =>  'FLOAT $',
            'sqlite'  =>  'REAL'
        ));
    }

    public static function double($name, array $options = array()) {
        return self::choose($name, $options, array(
            'mysql'   =>  'DOUBLE $',
            'sqlite'  =>  'REAL'
        ));
    }

    public static function real($name, array $options = array()) {
        return self::choose($name, $options, array(
            'mysql'   =>  'DECIMAL $',
            'sqlite'  =>  'REAL'
        ));
    }

    public static function decimal($name, array $options = array()) {
        return self::choose($name, $options, array(
            'mysql'   =>  'DECIMAL $',
            'sqlite'  =>  'REAL'
        ));
    }



    /*
     * --------------------------------
     *              Strings
     * --------------------------------
     *
     */

    public static function char($name, array $options = array()) {
        $options += array('case' => false);
        return self::choose($name, $options, array(
            'mysql'   =>  'CHAR $',
            'sqlite'  =>  'TEXT'
        ));
    }

    public static function varchar($name, array $options = array()) {
        $options += array('case' => false);
        $options += array('size' => 255);
        return self::choose($name, $options, array(
            'mysql'   =>  'VARCHAR $',
            'sqlite'  =>  'TEXT'
        ));
    }

    public static function string($name, array $options = array()) {
        return self::varchar($name, $options);
    }

    public static function text($name, array $options = array()) {
        $options += array('case' => false);
        return self::choose($name, $options, array(
            'mysql'   =>  'TEXT $',
            'sqlite'  =>  'TEXT'
        ));
    }


    /*
     * --------------------------------
     *             Binaries
     * --------------------------------
     *
     */

    public static function binary($name, array $options = array()) {
        return self::choose($name, $options, array(
            'mysql'   =>  'BLOB $',
            'sqlite'  =>  'BLOB'
        ));
    }

    public static function blob($name, array $options = array()) {
        return self::binary($name, $options);
    }


    /*
     * --------------------------------
     *             Boolean
     * --------------------------------
     *
     */

    public function boolean($name, array $options = array()) {
        return self::choose($name, $options, array(
            'mysql'   =>  'BOOL $',
            'sqlite'  =>  'INT',
        ));
    }

    public function bool($name, array $options = array()) {
        return self::boolean($name, $options);
    }

}