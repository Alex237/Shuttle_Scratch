<?php

/**
 * The base model
 * 
 * @author Alann Dragin <a.dragin@insta.fr>
 * @author Fabien Morchoisne <f.morchoisne@insta.fr>
 * @author Alex Maxime Cadevall <a.cadevall@insta.fr>
 */
abstract class BaseModel
{

    /**
     * The PDO instance
     * 
     * @var \PDO
     */
    protected $db;

    /**
     *
     * @var type 
     */
    public $query;

    /**
     *
     * @var type 
     */
    protected $config;

    /**
     *
     * @var type 
     */
    protected $table;

    /**
     * The primary key field name or name in a table
     * 
     * @var integer
     */
    protected $primaryKey;

    /**
     * Constructor
     * 
     * @param string $table The DataBase table name <b>REQUIRED</b>
     * @param string $primaryKey The primary key field name <b>OPTIONAL</b>
     */
    function __construct($table, $primaryKey) {

        $this->query = array(
            'select' => '',
            'from' => '',
            'where' => '',
            'join' => '',
            'orderBy' => '',
            'limit' => '',
        );

        $this->config = array(
            'host' => 'localhost',
            'database' => 'shuttle',
            'user' => 'root',
            'password' => '',
            'charset' => 'UTF8'
        );

        $this->table = $table;
        $this->primaryKey = $primaryKey;
    }

    /**
     * Initialise PDO Object
     * 
     * @return void
     * @throws Exception PDOException
     */
    public function init() {

        try {
            $this->db = new PDO('mysql:host=' . $this->config['host'] . ';dbname=' . $this->config['database'], $this->config['user'], $this->config['password']);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->db->exec('SET NAMES "' . $this->config['charset'] . '"');
        } catch (PDOException $ex) {
            print 'Unable to connect to Database ! Check configuration in "init()" method !\nERROR : !' . $ex->getMessage();
        }
    }

    /**
     * 
     * @param type $id
     */
    public function loadById($id, $columns = array()) {
        $sql = $this->select($columns)
                ->where(array($this->primaryKey . ' = :id'))
                ->buildQuery();

        $loadById = $this->db->prepare($sql);
        $loadById->execute(array(':id' => $id));
        return $loadById->fetch();
    }

    /**
     * 
     */
    public function loadAll($columns = array()) {
        $sql = $this->select($columns)
                ->buildQuery();

        $loadAll = $this->db->prepare($sql);
        $loadAll->execute();
        return $loadAll->fetchAll();
    }

    /**
     * 
     */
    public function loadWhere($clause = array(), $columns = array()) {
        $sql = $this->select($columns)
                ->where($clause)
                ->buildQuery();

        $loadWhere = $this->db->prepare($sql);
        $loadWhere->execute();
        return $loadWhere->fetchAll();
    }

    /**
     * 
     * @param type $id
     */
    public function deleteById($id) {

        $sql = 'DELETE FROM ' . $this->table . ' WHERE ' . $this->primaryKey . ' = :id';
        $deleteById = $this->db->prepare($sql);

        return $deleteById->execute(array(':id' => $id));
    }

    /**
     * 
     * @param type $data
     * @param type $table
     */
    public function save($data, $table = null, $primaryKey = null) {

        $primaryKey = $primaryKey != null ? $primaryKey : $this->primaryKey;

        if (in_array($primaryKey, array_keys($data))) {
            $sql = 'UPDATE ';
            $sql .= $table != null ? $table : $this->table;
            $sql .= ' SET ';

            $columns = array_keys($data);
            foreach ($columns as $column) {
                if ($column != $primaryKey) {
                    $sql .= $column . ' = :' . $column;
                    if (end($columns) != $column) {
                        $sql .= ', ';
                    }
                }
            }
            $sql .= ' WHERE ' . $primaryKey . ' = :' . $primaryKey;
        } else {
            $sql = 'INSERT INTO ';
            $sql .= $table != null ? $table : $this->table;
            $sql .= ' (' . implode(',', array_keys($data)) . ') VALUES (:' . implode(',:', array_keys($data)) . ')';
        }

        $insert = $this->db->prepare($sql);

        return $insert->execute($data);
    }

    /**
     * Unset PDO Objects
     * 
     * @return void
     */
    public function close() {

        $this->db = NULL;
    }

    /**
     * 
     * @return string $sql The SQL query
     */
    protected function buildQuery() {

        if ($this->query['select'] != '') {
            $sql = 'SELECT ' . $this->query['select'];
        } else {
            $sql = 'SELECT *';
        }

        if ($this->query['from'] != '') {
            $sql .= ' FROM ' . $this->query['from'];
        } else {
            $sql .= ' FROM ' . $this->table;
        }

        $sql .= $this->query['join'];


        if ($this->query['where'] != '') {
            $sql .= ' WHERE ' . $this->query['where'];
        }

        $sql .= $this->query['orderBy'] . $this->query['limit'];
        $this->flush();

        return (string) $sql;
    }

    /**
     * Build a 'select' query
     * 
     * @param array $columns
     */
    protected function select($columns = array()) {

        if (!empty($columns)) {

            if ($this->query['select'] != '') {
                $this->query['select'] .= ',';
            }

            $this->query['select'] .= implode(',', $columns);
        }

        return $this;
    }

    /**
     * Build a 'from' query
     * 
     * @param array $tables
     */
    protected function from($tables = array()) {

        if (!empty($tables)) {

            if ($this->query['from'] != '') {
                $this->query['from'] .= ',';
            }

            $this->query['from'] .= implode(',', $tables);
        }

        return $this;
    }

    /**
     * Build a 'join' query
     * 
     * @param string $table The joined table
     * @param string $ref The reference clause
     */
    protected function join($table = null, $ref = null) {

        if (!is_null($table) and !is_null($ref)) {

            $this->query['join'] .= ' LEFT JOIN ' . $table . ' ON ' . $ref;
        }

        return $this;
    }

    /**
     * Build a 'where' query
     * 
     * @param array $clauses
     */
    protected function where($clauses = array(), $logic = 'AND') {

        if (!empty($clauses)) {

            if ($this->query['where'] != '') {
                $this->query['where'] .= ' ' . $logic . ' ';
            }

            $this->query['where'] .= '(' . implode(' AND ', $clauses) . ')';
        }

        return $this;
    }

    /**
     * Build an 'order by' query
     * 
     * @param array $columns The ordered columns
     * @param string The ordering way
     */
    protected function orderBy($columns = array(), $way = 'ASC') {

        if (!empty($columns)) {
            $this->query['orderBy'] = ' ORDER BY ' . implode(',', $columns) . ' ' . $way;
        }

        return $this;
    }

    /**
     * Build a 'limit' query
     * 
     * @param int $offet The query offset
     * @param int $limit The query limit
     */
    protected function limit($offset = 0, $limit = 1) {

        if ($limit > 0 and $offset >= 0) {
            $this->query['limit'] = ' LIMIT ' . $offset . ', ' . $limit;
        }

        if ($limit > 0 and $offset >= 0) {
            $this->query['limit'] = ' LIMIT ' . $offset . ', ' . $limit;
        }
        return $this;
    }

    /**
     * 
     */
    public function flush() {
        $this->query = array(
            'select' => '',
            'from' => '',
            'where' => '',
            'join' => '',
            'orderBy' => '',
            'limit' => '',
        );

        return $this;
    }

}
