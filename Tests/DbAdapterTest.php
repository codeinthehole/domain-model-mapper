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
    
    public function testFetchField()
    {
        $sql =
            "SELECT title
             FROM post
             WHERE post_id = :id";
        $bindings = array(
            'id' => 3
        );
        $this->assertSame("My Third Post", $this->db->fetchField($sql, $bindings));
    }
    
    public function testUpdateWithoutCondition()
    {
        $this->db->update('post', array('rating' => 0));
        $ratings = $this->db->fetchColumn("SELECT DISTINCT rating FROM post");
        $this->assertSame(array('0'), $ratings);
    }
    
    public function testUpdateWithBindings()
    {
        $this->db->update('post', array('rating' => 0), "post_id = :id", array('id' => 3));
        $newRating = $this->db->fetchField("SELECT rating FROM post WHERE post_id = 3");
        $this->assertSame('0', $newRating);
    }
    
    public function testDeleteWithoutCondition()
    {
        $this->db->delete('post');
        $ratings = $this->db->fetchColumn("SELECT DISTINCT rating FROM post");
        $this->assertSame(array(), $ratings);
    }
    
    public function testDeleteWithBindings()
    {
        $this->db->delete('post', "post_id = :id", array('id' => 2));
        $rating = $this->db->fetchField("SELECT rating FROM post WHERE post_id = 2");
        $this->assertNull($rating);
    }
    
    public function testInsert()
    {
        $bindings = array(
            'post_id' => 10,
            'title' => 'Test title',
            'contents' => 'Test content',
            'rating' => 0,
            'date_created' => date("Y-m-d")
        );
        $this->db->insert('post', $bindings);
        $title = $this->db->fetchField("SELECT title FROM post WHERE post_id = 10");
        $this->assertSame('Test title', $title);
    }
}
