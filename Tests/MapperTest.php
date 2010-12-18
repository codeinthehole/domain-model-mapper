<?php

namespace DMM;
require_once __DIR__.'/../DMM/BaseDomainModel.php';
require_once __DIR__.'/../DMM/Mapper.php';

class DummyPDO extends \PDO
{
    // We override the constructor to avoid the checks that PDO runs 
    // in its constructor
    public function __construct()
    {}
}

class MapperTest extends \PHPUnit_Framework_TestCase 
{
    public function testInsertCallsPDOWithCorrectSql()
    {
        $mockPdo = $this->getMock('\DMM\DummyPDO', array('prepare', 'execute'));
        $mapper = new Mapper($mockPdo, 'Table', 'id');
        
        $model = new BaseDomainModel('id');
        $data = array(
            'name' => 'Peter Shilton',
            'age' => 32
        );
        $model->__load($data);
        
        $mockPdo->expects($this->once())
                ->method('prepare')
                ->with($this->matchesRegularExpression('/^INSERT INTO `Table` (.*) VALUES (.*)$/'));
        $mockPdo->expects($this->once())
                ->method('execute')
                ->with($this->equalTo(array_values($data)));
        
        $mapper->insert($model);
    }
}