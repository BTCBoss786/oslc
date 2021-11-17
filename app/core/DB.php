<?php

class DB {
    private static $_instance = null,
                   $_pdo2 = null;
    private $_pdo,
            $_query,
            $_results,
            $_error = false,
            $_count = 0;

    protected function __construct() {
        try {
            $this->_pdo = new PDO("mysql:host=" . Config::get("mysql/host") . ";dbname=" . Config::get("mysql/db"), Config::get("mysql/user"), Config::get("mysql/pass"));
            $this->_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            die($e->getMessage());
        }
    }

    public static function getInstance() {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function query($sql, $params = []) {
        $this->_error = false;
        if ($this->_query = $this->_pdo->prepare($sql)) {
            if (count($params)) {
                $x = 1;
                foreach ($params as $param) {
                    $this->_query->bindValue($x, $param);
                    $x++;
                }
            }
            if ($this->_query->execute()) {
                $this->_results = $this->_query->fetchAll(PDO::FETCH_OBJ);
                $this->_count = $this->_query->rowCount();
            } else {
                $this->_error = true;
            }
        }
        return $this;
    }

    protected function action($action, $table, $where = []) {
        if (count($where) === 3) {
            $oprators = ["=", ">", "<", ">=", "<="];
            $field = $where[0];
            $oprator = $where[1];
            $value = $where[2];
            if (in_array($oprator, $oprators)) {
                $sql = "{$action} FROM `{$table}` WHERE `{$field}` {$oprator} ?";
                if (!$this->query($sql, [$value])->error()) {
                    return $this;
                }
            }
        }
        return false;
    }

    public function get($table, $where) {
        return $this->action("SELECT *", $table, $where);
    }

    public function delete($table, $where) {
        return $this->action("DELETE", $table, $where);
    }

    public function insert($table, $fields = []) {
        if (count($fields)) {
            $keys = array_keys($fields);
            $values = "";
            $x = 1;
            foreach ($fields as $field) {
                $values .= "?";
                if ($x < count($fields)) {
                    $values .= ", ";
                }
                $x++;
            }
            $sql = "INSERT INTO `{$table}` (`" . implode("`, `", $keys) . "`) VALUES ({$values})";
            if (!$this->query($sql, $fields)->error()) {
                return true;
            }
        }
        return false;
    }

    public function update($table, $id, $fields) {
        $set = "";
        $x = 1;
        foreach ($fields as $field => $value) {
            $set .= "`{$field}` = ?";
            if ($x < count($fields)) {
                $set .= ", ";
            }
            $x++;
        }
        $i = array_key_first($id);
        $sql = "UPDATE `{$table}` SET {$set} WHERE `{$i}` = {$id[$i]}";
        if (!$this->query($sql, $fields)->error()) {
            return true;
        }
        return false;
    }

    public function results() {
        return $this->_results;
    }

    public function first() {
        return $this->results()[0];
    }

    public function error() {
        return $this->_error;
    }

    public function count() {
        return $this->_count;
    }

    public static function pdo() {
        if (!isset(self::$_pdo2)) {
            self::$_pdo2 = new self();
            self::$_pdo2 = self::$_pdo2->_pdo;
        }
        return self::$_pdo2;
    }
}