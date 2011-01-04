<?php

namespace DMM;

require_once __DIR__.'/DbAdapter.php';

/**
 * @package DMM
 *
 */
class Mapper
{
    /**
     * @var DbAdapter
     */
    protected $db;
    
    /**
     * @var string
     */
    protected $tableName;
    
    /**
     * @var array
     */
    protected $identityFields;
    
    /**
     * Name of model class that this is the mapper for.
     * 
     * @var string
     */
    protected $modelClass = '\DMM\BaseDomainModel';
    
    /**
     * Name of collection class that this is the mapper for.
     * 
     * @var string
     */
    protected $modelCollectionClass = '\DMM\ModelCollection';

    /**
     * @param PDO $pdo
     * @param string $tableName
     * @param array $identityFields
     */
    public function __construct(\PDO $pdo, $tableName, $identityFields)
    {
        $this->db = new DbAdapter($pdo);
        $this->tableName = $tableName;
        if (!is_array($identityFields)) {
            $identityFields = array($identityFields);
        }
        $this->identityFields = $identityFields;
    }
    
    /**
     * Returns the SQL condition to identity this model
     * 
     * @param array $identity A hash of field => value for the identity of this object
     * @return string
     */
    private function getIdentityCondition(array $identity)
    {
        $conditions = array();
        $bindings = array();
        foreach ($identity as $key => $value) {
            if (in_array($key, $this->identityFields)) {
                $placeholder = strtolower($key);
                $conditions[] = sprintf("`%s` = :$placeholder", $key);
                $bindings[$placeholder] = $value;
            }
        }
        $sql = implode(' AND ', $conditions);
        return array($sql, $bindings);
    }
    
    /**
      * For hitting the db to fetch the row data.
      * 
      * This is extracted into its own method so that it is 
      * easy to subclass and introduce caching.
      * 
      * @param string $sql
      * @param array $bindings
      * @return BaseDomainModel
      */
    protected function fetchItem($sql, $bindings)
    {
        $row = $this->db->fetchRow($sql, $bindings);
        return $this->loadItem($row);
    }

    /**
      * For hitting the db to fetch the row data.
      * 
      * This is extracted into its own method so that it is 
      * easy to subclass and introduce caching.
      * 
      * @param string $sql
      * @param array $bindings
      * @return ModelCollection
      */
    protected function fetchCollection($sql, $bindings)
    {
        $rows = $this->db->fetchAll($sql, $bindings);
        return $this->loadCollection($rows);
    }

    /**
     * @param array $row
     * @return BaseDomainModel
     */
    protected function loadItem($row=null)
    {
        if (!$row) return null;
        $item = new $this->modelClass;
        $item->__load($row);
        return $item;
    }

    /**
     * @param array $rows
     * @return BaseDomainModel
     */
    protected function loadCollection(array $rows)
    {
        $collection = new $this->modelCollectionClass($this->modelClass);
        foreach ($rows as $row) {
            // Note we check to see if the model was successfully loaded before
            // adding.  This allows mappers to subclass the loadItem method and do
            // return null to prevent the model being loaded.
            $item = $this->loadItem($row);
            if ($item) $collection[] = $item;
        }
        return $collection;
    }

    /**
     * Returns a domain model that matches a given identity
     * 
     * @param array $identity A hash of fieldname => value to use to identify the model
     * @param BaseDomainModel
     * @return BaseDomainModel
     */
    public function find($identity, BaseDomainModel $model)
    {
        $sql = "SELECT * FROM `%s` WHERE %s";
        list($whereCondition, $bindings) = $this->getIdentityCondition($identity);
        $findSql = sprintf($sql, 
            $this->tableName, 
            $whereCondition); 
        $row = $this->db->fetchRow($findSql, $bindings);
        return $row ? $model->__load($row) : null;
    }
    
    /**
     * @param BaseDomainModel $model
     * @return Mapper
     */
    public function insert(BaseDomainModel $model)
    {
        $this->db->insert($this->tableName, $model->__toArray());
        $id = $this->db->getLastInsertId();
        if ($id) $model->__setIdentity($id);    
        return $this;
    }

    /**
     * @param BaseDomainModel $model
     * @return Mapper
     */
    public function update(BaseDomainModel $model)
    {
        list($whereCondition, $bindings) = $this->getIdentityCondition($model->__identity());
        $this->db->update($this->tableName, $model->__toArray(), $whereCondition, $bindings);
        return $this;
    }

    /**
     * @param BaseDomainModel $model
     * @return Mapper
     */
    public function save(BaseDomainModel $model)
    {
        // We check to see if we can load this model to
        // determine if we insert or update
        $existingModel = $this->find($model->__identity(), clone $model);
        if ($existingModel) {
            $this->update($model);
        } else {
            $this->insert($model);
        }
        return $this;
    }

    /**
     * Deletes a model from the db
     * 
     * @param BaseDomainModel $model
     * @return Mapper
     */
    public function delete(BaseDomainModel $model)
    {
        list($whereCondition, $bindings) = $this->getIdentityCondition($model->__identity());
        $this->db->delete($this->tableName, $whereCondition, $bindings);
        return $this;
    }
    
    /**
     * Method designed to be subclassed with validation rules
     * 
     * The returned array should be a hash of field name to error message
     * 
     * @param BaseDomainModel $model
     * @return array
     */
    public function getValidationErrors(BaseDomainModel $model)
    {
        // We default to calling the corresponding method on the model
        // object.
        return $model->getValidationErrors();
    }
}