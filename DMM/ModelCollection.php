<?php

namespace DMM;

/**
 * @package DMM
 */
class ModelCollection extends \ArrayObject
{
	/**
     * @var string
     */
	protected $modelClassName;
	
 	/**
     * @param string $modelClass
     */
    public function __construct($modelClass='\DMM\BaseDomainModel')
    {
    	if (class_exists($modelClass)) { 
    		$this->modelClassName = $modelClass;
    	} else {
    		throw new \InvalidArgumentException("'$modelClass' is not a valid class name");	
    	}
    }
    
    /**
     * Overridden method to ensure that only the correct type of models are
     * added.
     * 
     * @see ArrayObject::offsetSet()
     * @return void
     */
    public function offsetSet($offset, $newval)
    {
        $this->checkType($newval);
        parent::offsetSet($offset, $newval);
    }
    
    /**
     * @param mixed $value
     * @throws \InvalidArgumentException
     * @return void
     */
    protected function checkType($value)
    {
        if (!is_a($value, $this->modelClassName)) {
            throw new \InvalidArgumentException('Provided object is not an instance of '.$this->modelClassName);
        }
    }
    
	/**
     * @param BaseDomainModel $model
     * @return ModelCollection
     */
    public function removeModel(BaseDomainModel $model)
    {
        $collectionCopy = clone $this;
        foreach ($collectionCopy as $index => $copiedModel) {
            if ($model === $copiedModel) {
                unset($this[$index]);
            }
       	}   
        return $this;
    }
    
	/**	
 	 * @param domain_model_MagicAccess $model
     * @return boolean
     */
    public function contains(BaseDomainModel $model)
    {
        foreach ($this as $collectionModel) {
            if ($collectionModel === $model) {
                return true;
            } 
        }
		return false;
    }
    
	/**
     * A prototypejs-like pluck function.
     * 
     * @param string $property
     * @return array
     */
    public function pluckField($property)
    {
	    $array = array();
	    foreach ($this as $model) {
	        if (isset($model->{$property})) {
	        	$array[] = $model->{$property};
	        }
	    }
	    return $array;
    }
    
	/**
     * Same as pluck only for setting properties
     * 
     * @param string $field
     * @param mixed $value
     * @return ModelCollection
     */
    public function setField($field, $value)
    {
	    foreach ($this as $model) {
	        $model->$field = $value;
	    }
	    return $this;
    }
}