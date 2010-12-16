<?php

namespace DMM;
require_once __DIR__.'/../DMM/BaseDomainModel.php';

class MultiFieldIdentityModelTest extends \PHPUnit_Framework_TestCase 
{
    private $model;
    
    private $modelData = array(
        'partner' => 'books-r-us',
        'partnerId' => 'TY-443000',
        'title' => 'Fly fishing',
        'price' => 12.99
    );
    
    public function setUp()
    {
        $this->model = new BaseDomainModel(array('partner', 'partnerId'));
        $this->model->__load($this->modelData);
    }
    
    public function testIdentityMethodReturnsIdentityFieldsAndValues()
    {
        $expectedIdentity = array(
            'partner' => $this->modelData['partner'],
            'partnerId' => $this->modelData['partnerId'],
        );
        $this->assertSame($expectedIdentity, $this->model->__identity());
    }
}