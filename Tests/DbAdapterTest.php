<?php

namespace DMM;
require_once __DIR__.'/FixtureBasedTestCase.php';
require_once __DIR__.'/../DMM/DbAdapter.php';

class DbAdapterTest extends FixtureBasedTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->db = new DbAdapter($this->pdo);
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
