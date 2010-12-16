<?php

namespace DMM;
require_once __DIR__.'/../DMM/ModelCollection.php';

class VanillaModelCollectionTest extends \PHPUnit_Framework_TestCase 
{
    public function testModelsCanBeAdded()
    {
        $collection = new ModelCollection();
        $collection[] = new BaseDomainModel('id');
        $this->assertEquals(1, count($collection));
    }
    
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCollectionRequiresAValidClassName()
    {
        $collection = new ModelCollection('asdfasdf');
    }
    
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testOnlyModelsOfCorrectClassCanBeAdded()
    {
        $badObj = new \DateTime();
        $collection = new ModelCollection();
        $collection[] = $badObj;
    }
}