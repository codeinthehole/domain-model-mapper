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
        $expected = array('1','2','3','4');
        $this->assertSame($expected, $this->db->fetchColumn($sql));
    }

    public function testFetchRow()
    {
        $sql =
            "SELECT title, rating
             FROM post
             WHERE post_id = :id";
        $bindings = array('id' => 3);
        $expected = array(
            'title' => 'My Third Post',
            'rating' => '3'
        );
        $this->assertSame($expected, $this->db->fetchRow($sql, $bindings));
    }
}
