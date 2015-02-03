<?php

/**
 * 
 * This Project was part of the RASTER Project. Thats why it has the RASTER Namespace.
 * You can remove this namespace if you like.
 * 
 * @todo Get the type of the value and dont set the "" by NULL,Int and so on.
 * @todo support types of data in where and set and so on (int,string,email,...)
 * @author Tim Rücker <tim.ruecker@ymail.com>
 * @copyright (c) 2015
 * 
 */

namespace RASTER;

class QueryBuilder {

    /**
     * The current database connection
     * @var \mysqli
     */
    private $db;

    /**
     * The until now build query saved as string.
     * @var string
     */
    private $query = '';

    /**
     * select,insert,update,delete,and so on.
     * Currently just used as check if one of this query methods is already called.
     * Later maybe used for syntax differences between methods
     * @var integer
     */
    private $query_type; //select,insert,update,delete,and so on

    /**
     * 
     * sets the mysqli object as property for the object.
     * Note: It requires a database connection for the mysqli_real_escape_string() Function
     * @param \mysqli $db
     */

    public function __construct(\mysqli $db) {
        $this->db = $db;
    }

    /**
     * 
     * Checks if the first query part was done.
     * @param Integer $newtype Sets the new type of query (if set)
     * @return boolean
     */
    private function isFirstQuery($newtype = null) {
        $isFirst = !isset($this->query_type);
        if ($newtype !== null) {
            $this->query_type = $newtype;
        }
        return $isFirst;
    }

    /**
     * 
     * Uses the mysqli_real_escape_string Function to escape the key and the value of an array.
     * After that you should be able to use these values for sql querys.
     * @param array $array
     * @return array
     */
    private function real_escape_array($array) {
        $return = array();
        foreach ($array as $key => $value) {
            $k = mysqli_real_escape_string($this->db, $key);
            $v = mysqli_real_escape_string($this->db, $value);
            $return[$k] = $v;
        }
        return $return;
    }

    /**
     * 
     * Appends a Space to a String if no space follows!
     * @param String $string
     * @return Stirng
     */
    private function appendSpace($string) {
        return substr($string, -1) === ' ' ? $string : $string . ' ';
    }

    /**
     * 
     * Builds the SELECT field,field,... part of the sql query.
     * @param String|array $fields The fields that shoud be accessed. Default is all
     * @return \RASTER\QueryBuilder Returns itself for the next query part
     */
    public function select($fields = '*') {
        if (!$this->isFirstQuery(DB_Constants::$QUERY_TYPE_SELECT)) {
            //exception
            return $this;
        }
        $this->query = 'SELECT ';
        if (is_array($fields)) {
            $this->query.= '`' . implode('`,`', $fields) . '`';
        } elseif (is_string($fields)) {
            $this->query.= '`' . $fields . '`';
        }
        return $this;
    }

    /**
     * 
     * Builds the INSERT INTO ... Part of the query
     * @param String $tableName the name of the table
     * @return \RASTER\QueryBuilder Returns itself for the next query part
     */
    public function insert($tableName) {
        if (!$this->isFirstQuery(DB_Constants::$QUERY_TYPE_INSERT)) {
            //exception
            return $this;
        }
        $this->query = 'INSERT INTO `' . $tableName . '`';
        return $this;
    }

    /**
     * 
     * Builds the UPDATE `table` Part of the query
     * @param String $tableName the name of the table
     * @return \RASTER\QueryBuilder Returns itself for the next query part
     */
    public function update($tableName) {
        if (!$this->isFirstQuery(DB_Constants::$QUERY_TYPE_UPDATE)) {
            //exception
            return $this;
        }
        $this->query = 'UPDATE `' . $tableName . '`';
        return $this;
    }

    /**
     * 
     * Builds the DELETE FROM `table` part of the query
     * @param string $tableName the name of the table
     * @return \RASTER\QueryBuilder Returns itself for the next query part
     */
    public function delete($tableName) {
        if (!$this->isFirstQuery(DB_Constants::$QUERY_TYPE_DELETE)) {
            //exception
            return $this;
        }
        $this->query = 'DELETE FROM `' . $tableName . '`';
        return $this;
    }

    /**
     * 
     * Builds the .. FROM `table` ... part of the query.
     * @param string $tableName the name of the database table
     * @return \RASTER\QueryBuilder Returns itself for the next query part
     */
    public function from($tableName) {
        $this->query = $this->appendSpace($this->query) . 'FROM `' . $tableName . '`';
        return $this;
    }

    /**
     * 
     * Builds the ... SET `key`="value", ... Part of the query.
     * @param array $data the data that should be set into the database table
     * @return \RASTER\QueryBuilder Returns itself for the next query part
     */
    public function set($data) {
        $_comma = array();
        foreach ($this->real_escape_array($data) as $key => $value) {
            array_push($_comma, '`' . $key . '`="' . $value . '"');
        }
        $this->query = $this->appendSpace($this->query) . 'SET ' . implode(',', $_comma);
        return $this;
    }

    /**
     * 
     * Builds the ... WHERE `key`="value" AND ... Part of the query.
     * All arguments has to be arrays, the arguments in each array are seperated by AND.
     * The different arguments are seperatet by OR.
     * The amount of arguments is dynamic!
     * @return \RASTER\QueryBuilder Returns itself for the next query part
     */
    public function where() {
        $OR = array();
        foreach (func_get_args() as $value) {
            $AND = array();
            foreach ($this->real_escape_array($value) as $key => $value) {
                array_push($AND, '`' . $key . '`="' . $value . '"');
            }
            array_push($OR, implode(' AND ', $AND));
        }
        $this->query = $this->appendSpace($this->query) . 'WHERE ' . implode(' OR ', $OR);
        return $this;
    }

    public function join() {
        
    }

    public function having() {
        
    }

    public function groupBy() {
        
    }

    public function orderBy() {
        
    }

    /**
     * 
     * Runs the query and returns the query object
     * @return mysqli_result
     */
    public function query() {
        return $this->query;
        return $this->db->query($this->query);
    }

}
