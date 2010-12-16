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
}

class old
{
    /**
     * Convenience method to load a collection in one go
     * 
     * @param array $rows
     * @return DMM\ModelCollection
     */
    public function __load(array $rows)
    {
        foreach ($rows as $row) {
            $model = new $this->modelClass;
            $this[] = $model->__load($row);
        }
        return $this;
    }
    
    /**
     * @param domain_model_MagicAccess $model
     * @return domain_model_Collection
     */
    public function remove(BaseDomainModel $model)
    {
    	if (is_a($model, $this->modelClass)) {
	        $collectionCopy = clone $this;
	        foreach ($collectionCopy as $index => $copiedModel) {
	            if ($model == $copiedModel) {
	                unset($this[$index]);
	            }
	       	}   
	        return $this;
    	} else {
    		throw new InvalidArgumentException("Supplied object should be a " . $this->modelClass);
    	}
    }
    
    /**
	 * @param mixed $object
	 * @return void
     */
    public function append(BaseDomainModel $object)
    {
        $this->checkType($object);
        return parent::append($object);
    }
    
    /**
     * @param domain_model_MagicAccess $model
     * @return boolean
     */
    public function contains(BaseDomainModel $model)
    {
        foreach ($this as $collectionModel) {
            if ($collectionModel == $model) {
                return true;
            } 
        }
		return false;
    }

    /**
     * @param string $property
     * @param mixed $value
     * @return domain_Model
     */
    public function findByProperty($property, $value)
    {
        foreach ($this as $model) {
            if ($model->{$property} == $value) {
                return $model;
            }
        }
        return null;
    }
    
    /**
     * A prototype-like pluck function.
     * 
     * @param string $property
     * @return array
     */
    public function pluck($property)
    {
	    $array = array();
	    foreach ($this as $model) {
	        if (array_key_exists($property, $model)) {
	        	$array[] = $model[$property];
	        }
	    }
	    return $array;
    }
    
    /**
     * Same as pluck only for setting properties
     * 
     * @param string $property
     * @param mixed $value
     * @return void
     */
    public function setProperty($property, $value)
    {
	    foreach ($this as $model) {
	        $model->$property = $value;
	    }
    }
    
    /**
     * @param domain_model_MagicAccess $model
     * @return void
     */
    private function checkType(BaseDomainModel $model)
    {
        if (!is_a($model, $this->modelClass)) {
            throw new RuntimeException('Provided object is not a ' . $this->modelClass);
        }
    }

    /**
     * @param iterable $collection
     * @return domain_model_Collection
     */
    public function merge($collection)
    {
        foreach ($collection as $model) {
            $this->append($model);
        }
        return $this;
    }
}