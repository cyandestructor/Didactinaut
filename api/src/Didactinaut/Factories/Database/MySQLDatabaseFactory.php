<?php
namespace Didactinaut\Factories\Database;

use Didactinaut\Configuration\Database\DatabaseInterface;
use Didactinaut\Configuration\Database\MySQLDatabase;

class MySQLDatabaseFactory implements DatabaseFactoryInterface
{
    private $host;
    private $dbName;
    private $username;
    private $password;
    
    public function __construct($host, $database)
    {
        $this->host = $host;
        $this->dbName = $database;
        $this->username = getenv("DB-USER");
        $this->password = getenv("DB-PASSWORD");
    }

    public function create() : DatabaseInterface
    {
        return new MySQLDatabase(
            $this->host,
            $this->dbName,
            $this->username,
            $this->password
        );
    }
}
