<?php

namespace DMM;

require_once __DIR__.'/FixtureBasedTestCase.php';
require_once __DIR__.'/../DMM/Mapper.php';
require_once __DIR__.'/fixtures/Post.php';

class FixtureBasedMapperTest extends FixtureBasedTestCase
{
    protected $mapper;

    public function setUp()
    {
        parent::setUp();
        $this->mapper = new Mapper($this->pdo, 'post', 'post_id');
    }

    public function testInsertNewModel()
    {
        $model = new \Post; 
        $model->title = "A new post";
        $model->contents = "Here is some content";
        $model->rating = 4;
        $model->date_created = date("Y-m-d H:i:s", time()); 

        $this->mapper->insert($model);
        $this->assertTrue($model->post_id > 0);
    }
}
