<?php

namespace ATCM\Data\ORM;

use ATCM\Core\Exceptions\DataAccessException;
use ATCM\Data\Interfaces\IModelBase;
use ATCM\Core\Helpers\StringHelper;

/**
 * Base object to models, with methods to handle the database
 * 
 * @author Juliano Bressan <bressan.rs@gmail.com>
 * @version 1.0.0
 * @copyright MIT
 */
abstract class ModelBase implements IModelBase
{
    private array $properties = [];
    protected string $table = "";
    protected string $idField = "";
    protected bool $timestamps;

    public function __construct()
    { 
        $this->timestamps = true;        
 
        if (empty($this->table)) {
            $this->table = StringHelper::toSnakeCase((new \ReflectionClass($this))->getShortName());
        }
        if (empty($this->idField)) {
            $this->idField = 'id';
        }
    }

    public function __set($parameter, $value)
    {
        $this->properties[$parameter] = $value;
    }
 
    public function __get($parameter)
    {
        return $this->properties[$parameter];
    }
 
    public function __isset($parameter)
    {
        return isset($this->properties[$parameter]);
    }
 
    public function __unset($parameter)
    {
        if (isset($parameter)) {
            unset($this->properties[$parameter]);
            return true;
        }
        return false;
    }
     
    /**
     * This methos avoid that ID will be passed to new object if a model is cloned
     *
     * @return void
     */
    private function __clone()
    {
        if (isset($this->properties[$this->idField])) {
            unset($this->properties[$this->idField]);
        }
    }
        
    /**
     * Return an array with all public field of object
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->properties;
    }
    
    /**
     * Return a JSON representation with all public fields of the object
     *
     * @return void
     */
    public function toJson()
    {
        return json_encode($this->properties);
    }

    /**
     * Retrieves a row in database identified by your ID (primary key)
     *
     * @param int $id The 
     * @return IModelBase|null An object that represent the related row in the database
     */
    public static function find(int $id, $includeDeleted = false)
    {
        $class = get_called_class();
        $idField = (new $class())->idField;
        $table = (new $class())->table;
    
        $sql = 'SELECT * FROM ' . (is_null($table) ? strtolower($class) : $table);
        $sql .= ' WHERE ' . (is_null($idField) ? 'id' : $idField);
        $sql .= " = {$id} " . ($includeDeleted ?: "AND deleted_at IS NULL") . ";";
    
        if ($database = Database::getInstance()) {
            $result = $database->query($sql);
    
            if ($result) {
                $fields = $result->fetch(\PDO::FETCH_ASSOC);
                if(!$fields) return null;

                return self::parseObject($fields);
            }            
        } else {
            throw new DataAccessException("There is no connection to database.");
        }
    }

    private static function parseObject(array $row): IModelBase
    {
        $class = get_called_class();
        $newObject = new $class();
        foreach($row as $key => $value) {                    
            $keyConverted = StringHelper::toCamelCase($key, "_");
            
            $newObject->$keyConverted = $value;
        }
        return $newObject;
    }
        
    /**
     * Deletes a row in database identified by your ID (primary key)
     *
     * @param int $id
     * @return void
     */
    public static function destroy(int $id): void
    {
        $class = get_called_class();
        $object = new $class();
        $object->id = $id;
        $object->delete();
    }

     /**
     * Deletes a row in database identified by DELETEyour ID (primary key)
     *
     * @param int $id
     * @return void
     */
    public function delete(): void
    {
        if (isset($this->properties[$this->idField])) { 
            //$sql = "DELETE FROM {$this->table} WHERE {$this->idField} = {$this->properties[$this->idField]};";
            $sql = "UPDATE {$this->table} SET deleted_at = NOW() WHERE {$this->idField} = {$this->properties[$this->idField]};";
            if ($database = Database::getInstance()) {
                $database->exec($sql);
                $this->properties = [];
            } else {
                throw new DataAccessException("There is no connection to database.");
            }
        }
    }
        
    /**
     * Updates or creates a row in the database
     *
     * @return IModelBase An object that represent the related row in the database
     */
    public function save(): IModelBase
    {
        $newContent = $this->convertProperties();
 
        if (isset($this->properties[$this->idField])) {
            $sets = array();
            foreach ($newContent as $key => $value) {
                if ($key === $this->idField)
                    continue;
                $normalizedKey = StringHelper::toSnakeCase($key);
                $sets[] = "{$normalizedKey} = {$value}";
            }
            $sql = "UPDATE {$this->table} SET " . implode(', ', $sets) . " WHERE {$this->idField} = {$this->properties[$this->idField]};";
        } else {
            $normalizedKeys = [];
            foreach(array_keys($newContent) as $key) {
                $normalizedKeys[] = StringHelper::toSnakeCase($key);
            }
            $columns = implode(', ', $normalizedKeys);
            $sets = implode(', ', array_values($newContent));
            $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$sets});";
        }

        if ($database = Database::getInstance()) {
            $database->exec($sql);
            if(!isset($this->properties[$this->idField])) {
                $insertedId = intval($database->lastInsertId());
                $this->properties[$this->idField] = $insertedId;
            }
            return $this;
        } else {
            throw new DataAccessException("There is no connection to database.");
        }
    }
        
    /**
     * Creates an object with properties from initialization array. The objeft will not be saved
     * on the database. For saving, call method save after creation.
     *
     * @param array $params
     * @return IModelBase
     */
    public static function create(array $params): IModelBase
    {
        throw new \Exception("Method not implemented yet");

    }
    
    /**
     * Return all registers in the table
     *
     * @param  mixed $filter Where clausules can be passed
     * @param  mixed $limit Number of registers to return can be informed
     * @param  mixed $offset Offset of registers can be passed, useful for pagination
     * @param  mixed $includeDeleted
     * @return array
     */
    public static function all(string $filter = '', array $orderBy=[], int $limit = 0, int $offset = 0, bool $includeDeleted = false): array
    {
        if(!$includeDeleted) {
            $filter .= empty(trim($filter)) ? "deleted_at IS NULL" : "({$filter}) AND deleted_at IS NULL" ;
        }
        $class = get_called_class();
        $table = (new $class())->table;
        $sql = 'SELECT * FROM ' . (is_null($table) ? strtolower($class) : $table);
        $sql .= ($filter !== '') ? " WHERE {$filter}" : "";
        $sql .= ($limit > 0) ? " LIMIT {$limit}" : "";
        $sql .= ($offset > 0) ? " OFFSET {$offset}" : "";
        $sql .= empty($orderBy) ? "" : " ORDER BY " . implode(", ",$orderBy);
        $sql .= ';';
    
        if ($connection = Database::getInstance()) {
            $result = $connection->query($sql);
            $rows = $result->fetchAll(\PDO::FETCH_ASSOC);
            $objects = [];
            foreach ($rows as $row) {
                $objects[] = self::parseObject($row);
            }
            return $objects;
        } else {
            throw new DataAccessException("There is no connection to database.");
        }
    }

        
    /**
     * Format values according the sintax accepted by databases, preventing that user can pass
     * some incorret format in the properties
     *
     * @param  mixed $value
     * @return mixed
     */
    private function normalizeValues($value)
    {
        if (is_string($value) && !empty($value)) {
            return "'" . addslashes($value) . "'";
        } else if (is_bool($value)) {
            return $value ? true : false;
        } else if ($value !== '') {
            return $value;
        } else {
            return null;
        }
    }
    
    /**
     * Validates every property wich is a primitive type, normalizing your value
     *
     * @return array
     */
    private function convertProperties()
    {
        $convertedProperties = array();
        foreach ($this->properties as $key => $value) {
            if (is_scalar($value)) {
                $convertedProperties[$key] = $this->normalizeValues($value);
            }
        }
        return $convertedProperties;
    }

}