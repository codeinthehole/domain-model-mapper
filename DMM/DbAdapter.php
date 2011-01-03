<?php

namespace DMM;

/**
 * A simple database adapter, wrapping a PDO object
 *
 * @package DMM
 */
class DbAdapter
{
    /**
     * @var PDO
     */
    private $pdo;

    /**
     * Reference to the last executed PDOStatement
     * 
     * @var PDOStatement
     */
    private $lastPdoStatement = null;

    /**
     * @param PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Executes an SQL query and returns the PDO statement object
     *
     * @param string $sql
     * @param array $bindings
     * @return PDOStatement
     */
    protected function runQuery($sql, $bindings=array())
    {
        // Ensure bindings are in an array
        if (!is_array($bindings)) {
            $bindings = array($bindings);
        }
        
        $this->lastPdoStatement = null;
        if (count($bindings) > 0) {
            // Use a prepared statement if bindings are set
            $this->lastPdoStatement = $this->pdo->prepare($sql);
            $executedOk = $this->lastPdoStatement->execute($bindings);
            if (false === $executedOk) {
                throw new PDOException;
            }
        } else {
            // Execute a normal query
            $this->lastPdoStatement = $this->pdo->query($sql); 
            if (false === $this->lastPdoStatement) {
                throw new PDOException;
            }
        }
        return $this->lastPdoStatement;
    }

    /**
     * Quotes a table or fieldname
     *
     * @param string $identifier
     * @return string
     */
    protected function quoteIdentifier($identifier)
    {
        return sprintf("`%s`", $identifier);
    }

    // =======
    // READING
    // =======

    /**
     * Returns a single field value
     *
     * @param string $sql The query to run
     * @param array $bindings Parameter values to bind into query
     * @return string
     */
    public function fetchField($sql, $bindings=array())
    {
        $row = $this->fetchRow($sql, $bindings); 
        return ($row && count($row) > 0) ? array_shift($row) : null;
    }

    /**
     * Returns a row
     *
     * @param string $sql The query to run
     * @param array $bindings Parameter values to bind into query
     * @return array
     */
    public function fetchRow($sql, $bindings=array())
    {
        $statement = $this->runQuery($sql, $bindings);
        $row = $statement->fetch(\PDO::FETCH_ASSOC);
        return $row ? $row : null;
    }

    /**
     * Returns a column of values as an array
     *
     * @param string $sql The query to run
     * @param array $bindings Parameter values to bind into query
     * @return array
     */
    public function fetchColumn($sql, $bindings=array())
    {
        $statement = $this->runQuery($sql, $bindings);
        $columnData = array();
        while (false !== ($value = $statement->fetchColumn())) {
            $columnData[] = $value;
        }
        return $columnData;
    }

    /**
     * Returns an array of rows
     *
     * @param string $sql The query to run
     * @param array $bindings Parameter values to bind into query
     * @return array
     */
    public function fetchAll($sql, $bindings=array())
    {
        $statement = $this->runQuery($sql, $bindings);
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    // =======
    // WRITING
    // =======

    /**
     * Inserts data into a table
     *
     * @param string $tableName
     * @param array $bindings A Hash of field name to value
     * @return PDOStatement
     */
    public function insert($tableName, $bindings)
    {
        // Extract fields and values from bindings
        $fields = array();
        $values = array();
        foreach ($bindings as $field => $value) {
            $fields[] = $this->quoteIdentifier($field);
            $values[] = '?';
        }
        // Construct SQL and execute
        $escapedTableName = $this->quoteIdentifier($tableName);
        $sql = sprintf("INSERT INTO %s (%s) VALUES (%s)", $escapedTableName, implode(', ', $fields), implode(', ', $values));
        return $this->runQuery($sql, array_values($bindings));
    }

    /**
     * Returns the last insert id
     *
     * @return string
     */
    public function getLastInsertId()
    {
        return $this->pdo->lastInsertId();
    }

    /**
     * Executes an UPDATE statement
     *
     * @param string $tableName
     * @param array $bindings
     * @param string $whereCondition
     * @return int The number of rows affected
     */
    public function update($tableName, $bindings, $whereCondition='')
    {
        // Determine field assignments
        $assignments = array();
        foreach ($bindings as $field => $value) {
            $assignments[] = sprintf("%s = %s", $this->quoteIdentifier($field), '?');
        }
        // Construct SQL
        $escapedTableName = $this->quoteIdentifier($tableName);
        $sql = sprintf("UPDATE %s SET %s", $escapedTableName, implode(', ', $assignments));
        if ($whereCondition) {
            $sql .= " WHERE $whereCondition";
        }
        $statement = $this->runQuery($sql, array_values($bindings));
        
        // Return the number of rows affected
        return $statement->rowCount();
    }

    /**
     * Executes a DELETE statement
     *
     * @param string $tableName
     * @param string $whereCondition
     * @return int The number of rows deleted
     */
    public function delete($tableName, $whereCondition)
    {
        $escapedTableName = $this->quoteIdentifier($tableName);
        $sql = sprintf("DELETE FROM %s WHERE %s", $escapedTableName, $whereCondition);
        $statement = $this->runQuery($sql);
        
        // Return the number of rows affected
        return $statement->rowCount();
    }

    /**
     * @return db_Adapter
     */
    public function beginTransaction()
    {
        $this->pdo->beginTransaction();
        return $this;
    }
    
    /**
     * @return db_Adapter
     */
    public function commit()
    {
        $this->pdo->commit();
        return $this;
    }
    
    /**
     * @return db_Adapter
     */
    public function rollBack()
    {
        $this->pdo->rollBack();
        return $this;
    }
    
    /**
     * @return int
     */
    public function getLastRowCount()
    {
        if (!$this->lastPdoStatement) {
            return null;
        }
        return $this->lastPdoStatement->rowCount();
    }
    
    /**
     * @return int
     */
    public function getLastColumnCount()
    {
        if (!$this->lastPdoStatement) {
            return null;
        }
        return $this->lastPdoStatement->columnCount();
    }

    /**
     * Execute arbitrary SQL
     *
     * @param string $sql
     * @param array $bindings
     * @return PDOStatement
     */
    public function execute($sql, array $bindings=array())
    {
        return $this->runQuery($sql, $bindings);
    }
}