<?php

namespace DMM;

require_once __DIR__.'/MissingDataException.php';

/**
 * The top-level domain model object.
 * 
 * @package DMM
 */
class BaseDomainModel
{
    /**
     * Identifying fieldnames for this models
     *
     * @var array
     */
    private $identityKeys;
    
    /**
     * Optional array of fieldnames to use as a filter when
     * loading data using the __load command
     * 
     * @var array
     */
    private $fieldNames;
    
    /**
     * The field names and values for this object
     *
     * @var array
     */
    protected $data = array();
    
    /**
     * @param string|array $identityKeys
     */
    public function __construct($identityKeys=array())
    {
        if (!is_array($identityKeys)) {
            $identityKeys = array($identityKeys);
        }
        $this->identityKeys = $identityKeys;
    }
    
    /**
     * @param array $fieldNames
     * @return BaseDomainModel
     */
    protected function __setFieldNames(array $fieldNames)
    {
        $this->fieldNames = $fieldNames;
        return $this;
    }
    
    /**
     * Create a clean domain object from a database result set.
     * 
     * Note that the fieldNames property can be used to filter out 
     * unwanted values from a database row.
     *
     * @param array $data
     * @return BaseDomainModel
     */
    public function __load(array $data)
    {
        if ($this->fieldNames) {
            foreach ($this->fieldNames as $field) {
                if (array_key_exists($field, $data)) {
                    $this->data[$field] = $data[$field];
                } else {
                    throw new MissingDataException("Supplied row must contain a $field field");
                }
            }
        } else {
            $this->data = $data;
        }
        return $this;
    }
    
    /**
     * Returns the identity of this object
     *
     * @return array
     */
    public function __identity()
    {
        $identity = array();
        foreach ($this->identityKeys as $key) {
            if (array_key_exists($key, $this->data) && !empty($this->data[$key])) {
                $identity[$key] = $this->data[$key];
            }
        }
        return $identity;
    }
    
    /**
     * Returns the fields of this object as an array
     *
     * @return array
     */
    public function __toArray()
    {
        return $this->data;
    }
    
    /**
     * Function for validating a model's contents before it is saved
     * 
     * @return array An array of error messages 
     */
    public function getValidationErrors()
    {
        return array();
    }
    
    // ==================
    // MAGIC FIELD ACCESS
    // ==================
    
    /**
     * Magic getter method that looks for field within data array or for 
     * a specific getter method with name __get$FieldName.
     *
     * @param string $fieldName
     * @return mixed|false
     */
    public function __get($fieldName)
    {
        $methodName = "__get$fieldName";
        if (method_exists($this, $methodName)) {
            return call_user_func(array($this, $methodName));
        }
        if (array_key_exists($fieldName, $this->data)) {
            return $this->data[$fieldName];
        } else {
            return null;
        }
    }
    
    /**
     * Magic function to allow fields to have their values set as if they
     * are public properties.
     *
     * If the method __set$FieldName($value) exists then that
     * method is called instead to do the setting
     *
     * @param string $fieldName
     * @param mixed $value
     * @return void
     */
    public function __set($fieldName, $value)
    {
        $methodName = "__set$fieldName";
        if (method_exists($this, $methodName)) {
            call_user_func(array($this, $methodName), $value);
        } else {
            $this->data[$fieldName] = $value;
        }
    }
    
    /**
     * @param array $identityValues
     * @return void
     */
    public function __setIdentity($identityValues)
    {
        if (!is_array($identityValues)) {
            $identityValues = array($identityValues);
        }
        
        if (count($this->identityKeys) !== count($identityValues)) {
            throw new Exception("Invalid number of identity values");
        }
        
        for ($i=0; $i<count($this->identityKeys); $i++) {
            $this->data[$this->identityKeys[$i]] = $identityValues[$i];   
        }
    }

    /**
     * Magic function to check if field exists
     *
     * @param string $fieldName The Field name to check
     * @return boolean True if it is set, else false
     */
    public function __isset($fieldName) 
    {
        return array_key_exists($fieldName, $this->data);
    }

    /**
     * Magic function to unsets the value of a field
     *
     * Currently sets the value of $fileName to null
     *
     * @param string $fieldName
     * @return void
     */
    public function __unset($fieldName)
    {
        unset($this->data[$fieldName]);
    }
}