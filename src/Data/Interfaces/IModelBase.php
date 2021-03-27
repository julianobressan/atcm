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
    function find(int $id): IModelBase;
        
    /**
     * Deletes a row in database identified by your ID (primary key)
     *
     * @param int $id
     * @return void
     */
    function delete(int $id): void;
        
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
    function create(array $params): IModelBase;
}