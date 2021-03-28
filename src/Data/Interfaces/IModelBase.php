<?php

namespace ATCM\Data\Interfaces;

/**
 * Interface to ModelBase
 * 
 * @author Juliano Bressan <bressan.rs@gmail.com>
 * @version 1.0.0
 * @copyright MIT
 */
interface IModelBase
{
    /**
     * Retrieves a row in database identified by your ID (primary key)
     *
     * @param int $id The 
     * @return IModelBase An object that represent the related row in the database
     */
    static function find(int $id): ?IModelBase;
        
    /**
     * Deletes a row in database identified by your ID (primary key)
     *
     * @param mixed $id
     * @return void
     */
    static function destroy($objectOrId): void;

    /**
     * Deletes a row in database identified by your ID (primary key)
     *
     * @return void
     */
    function delete(): void;
        
    /**
     * Updates or creates a row in the database
     *
     * @return IModelBase An object that represent the related row in the database
     */
    function save(): IModelBase;
        
    /**
     * Creates an object with parameters passed by array
     *
     * @param array $params A named array with fields for the object creation
     * @return IModelBase
     */
    static function create(array $params = []): IModelBase;

    /**
     * Return all registers in the table
     *
     * @param  mixed $filter Where clausules can be passed
     * @param  mixed $limit Number of registers to return can be informed
     * @param  mixed $offset Offset of registers can be passed, useful for pagination
     * @return array
     */
    static function all(string $filter = '', array $orderBy=[], int $limit = 0, int $offset = 0): array;

    /**
     * Return all registers in the table
     *
     * @param  mixed $filter Where clausules can be passed
     * @param  mixed $limit Number of registers to return can be informed
     * @param  mixed $offset Offset of registers can be passed, useful for pagination
     * @return IModelBase
     */
    static function first(string $filter = '', array $orderBy=[]): ?IModelBase;

    /**
     * Return the number of results in the table, acording the filter passed (default = empty)
     *
     * @param  string $fieldName
     * @param  string $filter
     * @return int Number of registers
     */
    static function count(string $fieldName = '*', string $filter = ''): int;
}