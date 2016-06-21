<?php

/**
 * mysqlpdo数据库驱动
 *
 * @author Mr.L <349865361@qq.com>
 */

namespace dux\kernel\model;

class MysqlPdoDriver implements DbInterface {

    protected $config = [];
    protected $writeLink = NULL;
    protected $readLink = NULL;
    protected $sqlMeta = array('sql' => '', 'params' => [], 'link' => NULL);
    protected $transaction = false;

    public function __construct($config = []) {
        $this->config = $config;
    }

    public function select($table, array $condition = [], $field = '*', $order = NULL, $limit = NULL) {
        $field = !empty($field) ? $field : '*';
        $order = !empty($order) ? ' ORDER BY ' . $order : '';
        $limit = !empty($limit) ? ' LIMIT ' . $limit : '';
        $table = $this->_table($table);
        $condition = $this->_where($condition);
        return $this->query("SELECT {$field} FROM {$table} {$condition['_where']} {$order} {$limit}", $condition['_bindParams']);
    }

    public function query($sql, array $params = []) {
        $sth = $this->_bindParams($sql, $params, $this->_getReadLink());
        if ($sth->execute()) {
            $data = $sth->fetchAll(\PDO::FETCH_ASSOC);
            return $data;
        }
        $err = $sth->errorInfo();
        throw new \Exception('Database SQL: "' . $this->getSql() . '". ErrorInfo: ' . $err[2], 500);
    }

    public function execute($sql, array $params = []) {
        $sth = $this->_bindParams($sql, $params, $this->_getWriteLink());
        if ($sth->execute()) {
            $affectedRows = $sth->rowCount();
            return $affectedRows;
        }

        $err = $sth->errorInfo();
        throw new \Exception('Database SQL: "' . $this->getSql() . '". ErrorInfo: ' . $err[2], 500);
    }

    public function insert($table, array $data = []) {
        $table = $this->_table($table);
        $values = [];
        $keys = [];
        $marks = [];
        foreach ($data as $k => $v) {
            $keys[] = "`{$k}`";
            $values[":{$k}"] = $v;
            $marks[] = ":{$k}";
        }
        $status = $this->execute("INSERT INTO {$table} (" . implode(', ', $keys) . ") VALUES (" . implode(', ', $marks) . ")", $values);
        $id = $this->_getWriteLink()->lastInsertId();
        if ($id) {
            return $id;
        } else {
            return $status;
        }
    }

    public function update($table, array $condition = [], array $data = []) {
        if (empty($condition)) return false;
        $values = [];
        $keys = [];
        foreach ($data as $k => $v) {
            $keys[] = "`{$k}`=:_data_{$k}";
            $values[":_data_{$k}"] = $v;
        }
        $table = $this->_table($table);
        $condition = $this->_where($condition);
        return $this->execute("UPDATE {$table} SET " . implode(', ', $keys) . $condition['_where'], $condition['_bindParams'] + $values);
    }

    public function increment($table, array $condition = [], $field, $num = 1) {
        if (empty($condition) || empty($field)) return false;
        $table = $this->_table($table);
        $condition = $this->_where($condition);
        return $this->execute("UPDATE {$table} SET {$field} = {$field} + {$num} " . $condition['_where'], $condition['_bindParams']);
    }

    public function decrease($table, array $condition = [], $field, $num = 1) {
        if (empty($condition) || empty($field)) return false;
        $table = $this->_table($table);
        $condition = $this->_where($condition);
        return $this->execute("UPDATE {$table} SET {$field} = {$field} - {$num} " . $condition['_where'], $condition['_bindParams']);
    }

    public function delete($table, array $condition = []) {
        if (empty($condition)) return false;
        $table = $this->_table($table);
        $condition = $this->_where($condition);
        return $this->execute("DELETE FROM {$table} {$condition['_where']}", $condition['_bindParams']);
    }

    public function count($table, array $condition = []) {
        $table = $this->_table($table);
        $condition = $this->_where($condition);
        $count = $this->query("SELECT COUNT(*) AS __total FROM {$table} " . $condition['_where'], $condition['_bindParams']);
        return isset($count[0]['__total']) && $count[0]['__total'] ? $count[0]['__total'] : 0;
    }

    public function getFields($table) {
        $table = $this->_table($table);
        $obj = $this->_getReadLink()->prepare("DESCRIBE {$table}");
        $obj->execute();
        return $obj->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function getSql() {
        $sql = $this->sqlMeta['sql'];
        $arr = $this->sqlMeta['params'];
        uksort($arr, function ($a, $b) {
            return strlen($b) - strlen($a);
        });
        foreach ($arr as $k => $v) {
            $sql = str_replace($k, $this->sqlMeta['link']->quote($v), $sql);
        }
        return $sql;
    }

    public function beginTransaction() {
        if($this->transaction) {
            return true;
        }
        $this->transaction = true;
        return $this->_getWriteLink()->beginTransaction();
    }

    public function commit() {
        return $this->_getWriteLink()->commit();
    }

    public function rollBack() {
        return $this->_getWriteLink()->rollBack();
    }

    protected function _bindParams($sql, array $params, $link = null) {
        $this->sqlMeta = array('sql' => $sql, 'params' => $params, 'link' => $link);
        $sth = $link->prepare($sql);
        foreach ($params as $k => $v) {
            $sth->bindValue($k, $v);
        }
        return $sth;
    }

    protected function _table($table) {
        return (false === strpos($table, ' ')) ? "`{$table}`" : $table;
    }

    protected function _where(array $condition) {
        $result = array('_where' => '', '_bindParams' => []);
        $sql = null;
        $sqlArr = [];
        $params = [];
        foreach ($condition as $k => $v) {
            if(strtolower($k) == '_sql') {
                if(is_array($v)) {
                    foreach($v as $s) {
                        $sqlArr[] = $s;
                    }
                }else {
                    $sqlArr[] = $v;
                }
            }else{
                if (strpos($k, ':') === false) {
                    $k = str_replace('`', '', $k);
                    $key = ':_where_' . str_replace('.', '_', $k);
                    $field = '`' . str_replace('.', '`.`', $k) . '`';
                    $sqlArr[] = "{$field} = {$key}";
                }else{
                    $key = $k;
                }
                $params[$key] = $v;
            }
        }
        if (!$sql) $sql = implode(' AND ', $sqlArr);

        if ($sql) $result['_where'] = " WHERE " . $sql;
        $result['_bindParams'] = $params;
        return $result;
    }

    protected function _connect($isMaster = true) {
        $dbArr = [];
        if (false == $isMaster && !empty($this->config['slave'])) {
            $master = $this->config;
            unset($master['slave']);
            foreach ($this->config['slave'] as $k => $v) {
                $dbArr[] = array_merge($master, $this->config['slave'][$k]);
            }
            shuffle($dbArr);
        } else {
            $dbArr[] = $this->config;
        }
        $pdo = null;
        $error = '';
        foreach ($dbArr as $db) {
            $dsn = "mysql:host={$db['host']};port={$db['port']};dbname={$db['dbname']};charset={$db['charset']}";
            try {
                $pdo = new \PDO($dsn, $db['username'], $db['password']);
                break;
            } catch (\PDOException $e) {
                $error = $e->getMessage();
            }
        }
        if (!$pdo) {
            throw new \Exception('connect database error :' . $error, 500);
        }
        $pdo->exec("set names {$db['charset']}");
        return $pdo;
    }

    protected function _getReadLink() {
        if (!isset($this->readLink)) {
            try {
                $this->readLink = $this->_connect(false);
            } catch (\Exception $e) {
                $this->readLink = $this->_getWriteLink();
            }
        }
        return $this->readLink;
    }

    protected function _getWriteLink() {
        if (!isset($this->writeLink)) {
            $this->writeLink = $this->_connect(true);
        }
        return $this->writeLink;
    }

    public function __destruct() {
        if ($this->writeLink) {
            $this->writeLink = NULL;
        }
        if ($this->readLink) {
            $this->readLink = NULL;
        }
    }
}