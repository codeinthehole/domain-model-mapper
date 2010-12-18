<?php

namespace DMM;
require_once __DIR__.'/../DMM/ModelCollection.php';

class VanillaModelCollectionTest extends \PHPUnit_Framework_TestCase 
{
    private $firstModel;
    
    private $secondModel;
    
    private $collection;
    
    public function setUp()
    {
        $this->collection = new ModelCollection();
        $this->firstModel = new BaseDomainModel('id');
        $this->secondModel = new BaseDomainModel('id');
        $this->collection[] = $this->firstModel;
        $this->collection[] = $this->secondModel;
    }
    
    public function testCollectionCanBeCounted()
    {
        $this->assertEquals(2, count($this->collection));
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
    
    public function testModelsCanBeRemovedByIdentityCheck()
    {
        $this->collection->removeModel($this->firstModel);
        $this->assertEquals(1, count($this->collection));
    }
    
    public function testModelsCanBeRemovedByIndex()
    {
        unset($this->collection[0]);
        $this->assertEquals(1, count($this->collection));
    }
    
    public function testContainsReturnsTrueForContainedModel()
    {
        $this->assertTrue($this->collection->contains($this->firstModel));
    }
    
    public function testContainsReturnsFalseForNewModel()
    {
        $this->assertFalse($this->collection->contains(new BaseDomainModel('id')));
    }
    
    public function testPluckReturnsArrayOfFieldValues()
    {
        $this->firstModel->__load(array('name' => 'Adam'));
        $this->secondModel->__load(array('name' => 'Barry'));
        $this->assertSame(array('Adam', 'Barry'), $this->collection->pluckField('name'));
    }
    
    public function testSetFieldUpdatesBothModels()
    {
        $this->firstModel->__load(array('name' => 'Adam'));
        $this->secondModel->__load(array('name' => 'Barry'));
        $this->collection->setField('name', 'Callum');
        $this->assertSame(array('Callum', 'Callum'), $this->collection->pluckField('name'));
    }
}