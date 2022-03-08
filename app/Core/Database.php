<?php
namespace App\Core;

use PDO, PDOException;

class Database
{
    private $host;
    private $username;
    private $password;
    private $database;

    private $connection;
    private $statement;

    public function __construct($host, $username, $password, $database)
    {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;
        $this->connect();
    }

    private function connect()
    {
        $connectionString = "odbc:Driver={ODBC Driver 17 for SQL Server}; Server=".$this->host."; Database=".$this->database.";";
        $connectionOptions = [
            PDO::ATTR_PERSISTENT => TRUE,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ];

        try
        {
            $this->connection = new PDO($connectionString, $this->username, $this->password, $connectionOptions);
            return true;
        }
        catch(PDOException $exception)
        {
            if(DEBUG_MODE):
                die("Connection failed: " . $exception->getMessage());
            endif;
            return false;
        }
    }

    public function query($query)
    {
        $this->statement = $this->connection->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
    }

    public function bind($param, $value, $type = null)
    {
        if($type == null)
        {
            switch(true)
            {
                case is_int($value): $type = PDO::PARAM_INT; break;
                case is_string($value): $type = PDO::PARAM_STR; break;
                case is_bool($value): $type = PDO::PARAM_BOOL; break;
                default: PDO::PARAM_STR; break;
            }
        }

        $this->statement->bindParam($param, $value, $type);
    }

    public function execute()
    {
        return $this->statement->execute();
    }

    public function fetch()
    {
        $this->execute();
        return $this->statement->fetch(PDO::FETCH_OBJ);
    }

    public function fetchAll()
    {
        $this->execute();
        return $this->statement->fetchAll(PDO::FETCH_OBJ);
    }

    public function rowsAffected()
    {
        return $this->statement->rowCount();
    }

    public function lastInsertId()
    {
        return $this->connection->lastInsertId();
    }
}