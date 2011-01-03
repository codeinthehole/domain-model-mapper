<?php

namespace DMM;

require_once __DIR__.'/FixtureBasedTestCase.php';
require_once __DIR__.'/../DMM/Mapper.php';
require_once __DIR__.'/../DMM/ModelCollection.php';
require_once __DIR__.'/fixtures/Post.php';

class PostMapper extends Mapper
{
    protected $modelClass = '\Post';

    public function __construct(\PDO $pdo)
    {
        parent::__construct($pdo, 'post', 'post_id');
    }

    public function findById($id)
    {
        $sql =
            "SELECT *
             FROM `post`
             WHERE post_id = :id";
        $bindings = array(
            'id' => $id
        );
        return $this->fetchItem($sql, $bindings);
    }

    public function findByRating($rating)
    {
        $sql =
            "SELECT *
             FROM `post`
             WHERE rating = :rating";
        $bindings = array(
            'rating' => $rating
        );
        return $this->fetchCollection($sql, $bindings);
    }
}

class SubclassMapperTest extends FixtureBasedTestCase
{
    protected $mapper;

    public function setUp()
    {
        parent::setUp();
        $this->mapper = new PostMapper($this->pdo);
    }

    public function testItemFindersReturnSpecifiedModelClass()
    {
        $model = $this->mapper->findById(1);
        $this->assertInstanceOf('\Post', $model);
    }

    public function testCollectionFindersReturnCollectionObject()
    {
        $models = $this->mapper->findByRating(3);
        $this->assertInstanceOf('\DMM\ModelCollection', $models);
    }
}
