<?php

namespace App\config;

use Exception;
use mysqli;

/**
 * Summary of DatabaseConnection
 * database connection class using mysqli extension
 * methods for database CRUD operations
 */
final class DatabaseConnection
{
    private $dbhost;
    private $dbport;
    private $dbname;
    private $dbuser;
    private $dbpass;

    public $connection;

    public function __construct()
    {
        $this->dbhost = $_ENV['DB_HOST'];
        $this->dbport = $_ENV['DB_PORT'];
        $this->dbname = $_ENV['DB_NAME'];
        $this->dbuser = $_ENV['DB_USER'];
        $this->dbpass = $_ENV['DB_PASS'];
        //connect to database
        try {
            $this->connection = new mysqli($this->dbhost, $this->dbuser, $this->dbpass, $this->dbname, $this->dbport);
        } catch (Exception $e) {
            throw new Exception($e);
        }
    }

    public function query($sql)
    {
        return $this->connection->query($sql);
    }

    public function create($table, $data)
    {
        $columns = implode(', ', array_keys($data));
        $values  = "'" . implode("', '", array_map([$this->connection, 'real_escape_string'], array_values($data))) . "'";
        $sql     = "INSERT INTO $table ($columns) VALUES ($values)";
        return $this->connection->query($sql);
    }

    public function read($table, $conditions = '', $orderBy = '', $limit = '')
    {
        $sql = "SELECT * FROM $table" . ($conditions ? " WHERE $conditions" : '') . ($orderBy ? ' ORDER BY ' . (string) $orderBy : '') . ($limit ? ' LIMIT ' . (string) $limit : '');
        return $this->connection->query($sql);
    }

    public function update($table, $data, $conditions)
    {
        $set = '';
        foreach ($data as $column => $value) {
            $set .= "$column = '" . $this->connection->real_escape_string($value) . "', ";
        }
        $set = rtrim($set, ', ');
        $sql = "UPDATE $table SET $set WHERE $conditions";
        return $this->connection->query($sql);
    }

    public function delete($table, $conditions)
    {
        $sql = "DELETE FROM $table WHERE $conditions";
        return $this->connection->query($sql);
    }

    public function __destruct()
    {
        $this->connection->close();
    }
}
