<?php
require_once 'Database.php';

class DBConnection extends Database
{
    private static $instance;
    private $connection;

    public function __construct()
    {
        $config = include('config/database.php');
        $this->connect($config['host'], $config['username'], $config['password'], $config['database']);
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function connect($host, $username, $password, $database)
    {
        $this->connection = new mysqli($host, $username, $password, $database);
        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        }
    }

    public function disconnect()
    {
        $this->connection->close();
    }

    public function query($stmt)
    {
        $stmt->execute();

        // SELECT statement
        if ($stmt->field_count > 0) {
            $result = $stmt->get_result();
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            $result->free();
            return $data;
        } else {
            // Other type of statement
            return $stmt->affected_rows;
        }

        $stmt->close();
    }

    public function select($table, $columns, $joins = [], $joinType = 'INNER', $condition = '', $group = '', $having = '', $order = '', $params = [])
    {
        try {
            $sql = "SELECT " . implode(", ", $columns) . " FROM " . $table;
            if (!empty($joins)) {
                $sql .= " $joinType JOIN " . implode(" $joinType JOIN ", $joins);
            }
            if (!empty($condition)) {
                $sql .= " WHERE $condition";
            }
            if (!empty($group)) {
                $sql .= " GROUP BY $group";
            }
            if (!empty($having)) {
                $sql .= " HAVING $having";
            }
            if (!empty($order)) {
                $sql .= " ORDER BY $order";
            }

            $stmt = $this->connection->prepare($sql);

            if (!empty($params)) {
                $types = str_repeat('s', count($params));
                $stmt->bind_param($types, ...$params);
            }

            return $this->query($stmt);
        } catch (mysqli_sql_exception $e) {
            echo ' SQL error occurred: ' . $e->getMessage();
            return [];
        }
    }

    public function insert($table, $dataSets)
    {
        $columns = implode(', ', array_keys($dataSets[0]));
        $placeholders = rtrim(str_repeat('(' . rtrim(str_repeat('?, ', count($dataSets[0])), ', ') . '), ', count($dataSets)), ', ');
        $sql = "INSERT INTO $table ($columns) VALUES $placeholders";

        try {
            $stmt = $this->connection->prepare($sql);
            $flattenedArray = array_merge(...array_map('array_values', $dataSets));
            $types = str_repeat('s', count($flattenedArray));
            $values = [];

            foreach ($dataSets as $data) {
                $values = array_merge($values, array_values($data));
            }

            $bindValues = array_merge($values);
            $stmt->bind_param($types, ...$bindValues);

            return $this->query($stmt);
        } catch (mysqli_sql_exception $e) {
            echo 'SQL error occurred: ' . $e->getMessage();
            return 0;
        }
    }

    public function update($table, $data, $conditions, $params = [])
    {
        $sql = "UPDATE $table SET ";
        foreach ($data as $key => $value) {
            $sql .= "$key = '$value', ";
        }
        $sql = rtrim($sql, ', ');
        $sql .= " WHERE ";
        foreach ($conditions as $condition) {
            $sql .= "{$condition['condition']} {$condition['operator']} ";
        }
        $sql = rtrim($sql, $conditions[count($conditions) - 1]['operator'] . ' ');

        try {
            $stmt = $this->connection->prepare($sql);

            $paramTypes = str_repeat('s', count($params));
            $stmt->bind_param($paramTypes, ...array_values($params));

            return $this->query($stmt);
        } catch (mysqli_sql_exception $e) {
            echo 'SQL error occurred: ' . $e->getMessage();
            return 0;
        }
    }

    public function delete($table, $conditions, $params = [])
    {
        $sql = "DELETE FROM $table ";
        $sql .= "WHERE ";
        foreach ($conditions as $condition) {
            $sql .= "{$condition['condition']} {$condition['operator']} ";
        }
        $sql = rtrim($sql, $conditions[count($conditions) - 1]['operator'] . ' ');

        try {
            $stmt = $this->connection->prepare($sql);

            $paramTypes = str_repeat('s', count($params));
            $stmt->bind_param($paramTypes, ...array_values($params));

            return $this->query($stmt);
        } catch (mysqli_sql_exception $e) {
            echo 'SQL error occurred: ' . $e->getMessage();
            return 0;
        }
    }
}
