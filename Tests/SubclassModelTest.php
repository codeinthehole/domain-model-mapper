<?php

namespace DMM;
require_once __DIR__.'/../DMM/BaseDomainModel.php';

class MyDomainModel extends BaseDomainModel
{
    // Only public so test can read these values
    public static $fieldNames = array('id', 'name', 'salary');
    
    public function __construct()
    {
        parent::__construct('id');
        $this->__setFieldNames(self::$fieldNames);
    }
    
    // This should be called when looking for "name"
    public function __getName()
    {
        return strtoupper($this->data['name']);
    }
    
    // This should be called when setting "name"
    public function __setSalary($newSalary)
    {
        if ($newSalary <= 0) {
            throw new \InvalidArgumentException;
        }
        $this->data['salary'] = $newSalary;
    }
}

class SubclassModelTest extends \PHPUnit_Framework_TestCase 
{
    private $model;
    
    public function setUp()
    {
        $this->model = new MyDomainModel('id');
    }
    
    /**
     * @expectedException DMM\MissingDataException
     */
    public function testLoadThrowsExceptionIfDataIsMissing()
    {
        $data = array(
            'id' => 100,
            'name' => 'Frank Lampard',
        );
        $this->model->__load($data);
    }
    
    public function testLoadFiltersExtraDataUsingFieldNames()
    {
        $data = array(
            'id' => 100,
            'name' => 'Frank Lampard',
            'salary' => 90000,
            'hair' => 'Dark',
        );
        $this->model->__load($data);
        $this->assertFalse(isset($this->model->hair));
    }
    
    public function testToArrayFiltersExtraDataUsingFieldNames()
    {
        $data = array(
            'id' => 100,
            'name' => 'Frank Lampard',
            'salary' => 90000,
            'hair' => 'Dark',
        );
        $this->model->__load($data);
        $this->assertFalse(isset($this->model->hair));
    }
    
    public function testMagicGetNameMethodIsCalled()
    {
        $data = array(
            'id' => 100,
            'name' => 'Frank Lampard',
            'salary' => 90000,
        );
        $this->model->__load($data);
        $this->assertSame("FRANK LAMPARD", $this->model->name);
    }
    
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testMagicSetNameMethodIsCalled()
    {
        $this->model->salary = -5;
    }
}