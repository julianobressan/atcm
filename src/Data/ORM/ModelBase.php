<?php

namespace ATCM\Data\ORM;

use ATCM\Core\Exceptions\DataAccessException;
use ATCM\Data\Interfaces\IModelBase;
use ATCM\Core\Helpers\StringHelper;
use Exception;
use LogicException;
use Test\Data\Models\ExampleModel;

/**
 * Base object to models, with methods to handle the database
 * 
 * @author Juliano Bressan <bressan.rs@gmail.com>
 * @version 1.0.0
 * @copyright MIT
 */
abstract class ModelBase implements IModelBase
{
    private static ?\PDO $database = null;
    private array $properties = [];
    protected string $table = "";
    protected string $idField = "";
    protected bool $timestamps;

    public function __construct()
    {             
        self::getConnection();

        $this->timestamps = true;        
 
        if (empty($this->table)) {
            $this->table = StringHelper::toSnakeCase((new \ReflectionClass($this))->getShortName());
        }
        if (empty($this->idField)) {
            $this->idField = 'id';
        }
    }

    private static function getConnection(): \PDO
    {        
        try {
            if (is_null(self::$database)) {
                self::$database = Database::getInstance();
            }
            return self::$database;
        } catch (DataAccessException $e) {
            throw new DataAccessException("It was not possible to create an instance of " . get_called_class() . 
                " because connection problems.", 4, 503, $e);
        } catch (\PDOException $ex) {
            throw new DataAccessException('The database is offline or credentials are wrong.', 3, 503, $ex);
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
    public static function find(int $id, $includeDeleted = false): ?IModelBase
    {
        $class = get_called_class();
        $idField = (new $class())->idField;
        $table = (new $class())->table;
    
        $sql = 'SELECT * FROM ' . (is_null($table) ? strtolower($class) : $table);
        $sql .= ' WHERE ' . (is_null($idField) ? 'id' : $idField);
        $sql .= " = {$id} " . ($includeDeleted ? "" : "AND deleted_at IS NULL") . ";";
    
        $result = self::getConnection()->query($sql);

        if ($result) {
            $fields = $result->fetch(\PDO::FETCH_ASSOC);
            if(!$fields) return null;

            return self::parseObject($fields);
        }            
    }

    private static function parseObject(array $row): IModelBase
    {
        $class = get_called_class();
        $newObject = new $class();
        foreach ($row as $key => $value) {                    
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
    public static function destroy($objectOrId): void
    {
        if (is_scalar($objectOrId)) {
            $class = get_called_class();
            $object = new $class();
            $object->id = $objectOrId;
            $object->delete();
        } else {
            $objectOrId->delete();
        }
        
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

            self::getConnection()->exec($sql);
            $this->properties = [];

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
            if ($this->timestamps) $sets[] = "updated_at = NOW()";
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

        self::getConnection()->exec($sql);
        if (!isset($this->properties[$this->idField])) {
            $insertedId = intval(self::getConnection()->lastInsertId());
            $this->properties[$this->idField] = $insertedId;
        }
        return $this;        
    }
        
    /**
     * Creates an object with properties from initialization array. The objeft will not be saved
     * on the database. For saving, call method save after creation.
     *
     * @param array $params
     * @return IModelBase
     */
    public static function create(array $params = []): IModelBase
    {
        $class = get_called_class();
        $newObject = new $class();

        foreach ($params as $key => $value) {
            $newObject->$key = $value;
        }

        return $newObject;
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
    public static function all(string $filter = '', array $orderBy = [], int $limit = 0, int $offset = 0, bool $includeDeleted = false): array
    {
        if (!$includeDeleted) {
            $filter = empty(trim($filter)) ? "deleted_at IS NULL" : "({$filter}) AND deleted_at IS NULL" ;
        }
        $class = get_called_class();
        $table = (new $class())->table;
        $sql = 'SELECT * FROM ' . (is_null($table) ? strtolower($class) : $table);
        $sql .= ($filter !== '') ? " WHERE {$filter}" : "";
        $sql .= ($limit > 0) ? " LIMIT {$limit}" : "";
        $sql .= ($offset > 0) ? " OFFSET {$offset}" : "";
        $sql .= empty($orderBy) ? "" : " ORDER BY " . implode(", ",$orderBy);
        $sql .= ';';    
        
        $result = self::getConnection()->query($sql);
        $rows = $result->fetchAll(\PDO::FETCH_ASSOC);
        $objects = [];
        foreach ($rows as $row) {
            $objects[] = self::parseObject($row);
        }
        return $objects;
    }

    /**
     * Return the first occurrency in the table that attends the filter. 
     *
     * @param  mixed $filter Where clausules can be passed
     * @param  mixed $limit Number of registers to return can be informed
     * @param  mixed $offset Offset of registers can be passed, useful for pagination
     * @param  mixed $includeDeleted
     * @return IModelBase|null An object or null
     */
    public static function first(string $filter = '', array $orderBy=[]): ?IModelBase
    {
        $return = self::all($filter, $orderBy, 1, 0);
        return $return[0] ?? null;
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
    
    /**
     * Return the number of results in the table, acording the filter passed (default = empty)
     *
     * @param  mixed $fieldName
     * @param  mixed $filter
     * @return int Number of registers
     */
    public static function count(string $fieldName = '*', string $filter = '', bool $includeDeleted = false): int
    {
        $class = get_called_class();
        $table = (new $class())->table;
        $sql = "SELECT count($fieldName) as total FROM " . (is_null($table) ? strtolower($class) : $table);
        $sql .= ($filter !== '') 
            ? " WHERE {$filter} " . ($includeDeleted ? "" : " AND deleted_at IS NULL") 
            : ($includeDeleted ? "" : " WHERE deleted_at IS NULL");
        $sql .= ($includeDeleted ? "" : " AND deleted_at IS NULL");
        $sql .= ';';

        $query = self::$database->prepare($sql);
        $query->execute();
        $result = $query->fetch(\PDO::FETCH_ASSOC);
        return (int) $result['total'];

    }
    
    /**
     * Returns all objects related to this (foreign key) in the informed model
     *
     * @param  mixed $class
     * @return array
     */
    protected function hasMany($class): array
    {
        if (!is_subclass_of($class, ModelBase::class)) throw new LogicException($class . " not extends " . ModelBase::class);
        $foreignKeyName = self::getForeignKey($this, true);
        $relatedObjects = $class::all("{$foreignKeyName} = {$this->id}");
        return $relatedObjects;
    }
    
    /**
     * Return the 0-1 row relation to Model passed by parameter
     *
     * @param  mixed $class
     * @return IModelBase One object of informed class type informed, if exists. Null will returns if not exists.
     */
    protected function belongsTo($class): ?IModelBase
    {
        if (!is_subclass_of($class, ModelBase::class)) throw new LogicException($class . " not extends " . ModelBase::class);
        $foreignKeyName = self::getForeignKey($class);
        if (!is_null($this->$foreignKeyName)) {
            $object = $class::find($this->$foreignKeyName);
            return $object;
        }
        return null;
    }

    protected static function getForeignKey($class, $databaseFormat = false)
    {
        $reflectedClass = new \ReflectionClass($class);
        $shortName = $reflectedClass->getShortName() . "Id";
        $foreignKeyName = $databaseFormat ? StringHelper::toSnakeCase($shortName) : StringHelper::toCamelCase($shortName);
        return $foreignKeyName;
    }
}