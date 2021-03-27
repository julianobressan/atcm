<?php

namespace ATCM\Data\ORM;

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
            $this->table = StringHelper::toSnakeCase(get_class($this));
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
     * @return IModelBase An object that represent the related row in the database
     */
    public function find(int $id): IModelBase
    {
        throw new \Exception("Method not implemented yet");
    }
        
    /**
     * Deletes a row in database identified by your ID (primary key)
     *
     * @param int $id
     * @return void
     */
    public function delete(int $id): void
    {

    }
        
    /**
     * Updates or creates a row in the database
     *
     * @return IModelBase An object that represent the related row in the database
     */
    public function save(): IModelBase
    {
        throw new \Exception("Method not implemented yet");

    }
        
    /**
     * Creates an object with properties from initialization array. The objeft will not be saved
     * on the database. For saving, call method save after creation.
     *
     * @param array $params
     * @return IModelBase
     */
    public function create(array $params): IModelBase
    {
        throw new \Exception("Method not implemented yet");

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
        $newProperties = array();
        foreach ($this->properties as $key => $value) {
            if (is_scalar($value)) {
                $newProperties[$key] = $this->normalizeValues($value);
            }
        }
        return $newProperties;
    }

}