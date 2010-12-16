<?php


/**
 * A collection class to work with the MagicAccess objects
 * 
 * @package DMM
 */
class ModelCollection extends ArrayObject
{
	/**
     * @var string
     */
	protected $modelClassName;
	
 	/**
     * @param string $modelClass
     */
    public function __construct($modelClass='DMM\BaseDomainModel')
    {
    	if (class_exists($modelClass)) { 
    		$this->modelClassName = $modelClass;
    	} else {
    		throw new InvalidArgumentException("'$modelClass' is not a valid class name");	
    	}
    }
    
    /**
      * @param array $rows
      * @return domain_model_Collection
      */
    public function __load(array $rows)
    {
        foreach ($rows as $row) {
            $model = new $this->modelClass;
            $model->__load($row);
            $this[] = $model;
        }
        return $this;
    }
    
    /**
     * @param domain_model_MagicAccess $model
     * @return domain_model_Collection
     */
    public function remove(domain_model_MagicAccess $model)
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
    public function append(domain_model_MagicAccess $object)
    {
        $this->checkType($object);
        return parent::append($object);
    }
    
    /**
     * @param domain_model_MagicAccess $model
     * @return boolean
     */
    public function contains(domain_model_MagicAccess $model)
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
    private function checkType(domain_model_MagicAccess $model)
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