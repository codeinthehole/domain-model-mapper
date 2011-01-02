<?php

namespace DMM;
require_once __DIR__.'/../DMM/DbAdapter.php';

class DbAdapterTest extends \PHPUnit_Extensions_Database_TestCase
{
    protected $pdo;
    protected $dbName = 'dmm_tests';
    protected $db;

    public function setUp()
    {
        $host = 'localhost';
        $username = 'dmm-user';
        $password = 'dmm-pw';
        $dsn = sprintf("mysql:dbname=%s;host=%s", $this->dbName, $host);
        $this->pdo = new \PDO($dsn, $username, $password);
        $this->db = new DbAdapter($this->pdo);
    }
    
    protected function getConnection()
    {
        return $this->createDefaultDBConnection($this->pdo, $this->dbName);
    }

    protected function getDataSet()
    {
        return $this->createFlatXMLDataSet(__DIR__.'/fixtures/seed.xml');
    }

    public function testFetchColumn()
    {
        $sql =
            "SELECT post_id
             FROM post";
        $expected = array('1','2','3');
        $actual = $this->db->fetchColumn($sql);
        $this->assertSame($expected, $this->db->fetchColumn($sql));
    }
}
