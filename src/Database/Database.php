<?php
abstract class Database
{
    abstract public function connect($host, $username, $password, $database);

    abstract public function disconnect();

    abstract public function select($table, $columns, $joins = [], $joinType = 'INNER', $condition = '', $group = '', $having = '', $order = '', $params = []);

    abstract public function insert($table, $dataSets);

    abstract public function update($table, $data, $conditions, $params = []);

    abstract public function delete($table, $conditions, $params = []);
}
