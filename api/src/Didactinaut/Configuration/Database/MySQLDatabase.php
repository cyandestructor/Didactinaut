<?php
namespace Didactinaut\Configuration\Database;

class MySQLDatabase implements DatabaseInterface
{
    private $host;
    private $dbName;
    private $username;
    private $password;

    private $connection;

    public function __construct($host, $database, $username, $password)
    {
        $this->host = $host;
        $this->dbName = $database;
        $this->username = $username;
        $this->password = $password;
    }
    
    public function connect()
    {
        $this->connection = null;

        // Establish the connection
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbName;
        $this->connection = new \PDO($dsn, $this->username, $this->password);

        // Configure the PDO :
        // Throw an exception when an error occurs
        $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        // Fetch the data as an associative array
        $this->connection->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);

        return $this->connection;
    }
}