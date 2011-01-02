<?php

namespace DMM;

class FixtureBasedTestCase extends \PHPUnit_Extensions_Database_TestCase
{
    protected $pdo;
    protected $dbName = 'dmm_tests';
    protected $host = 'localhost';
    protected $username = 'dmm-user';
    protected $password = 'dmm-pw';
    protected $fixtureFile = 'seed.xml';

    public function setUp()
    {
        $dsn = sprintf("mysql:dbname=%s;host=%s", $this->dbName, $this->host);
        $this->pdo = new \PDO($dsn, $this->username, $this->password);
    }
    
    protected function getConnection()
    {
        return $this->createDefaultDBConnection($this->pdo, $this->dbName);
    }

    protected function getDataSet()
    {
        return $this->createFlatXMLDataSet(__DIR__.'/fixtures/'.$this->fixtureFile);
    }
}
