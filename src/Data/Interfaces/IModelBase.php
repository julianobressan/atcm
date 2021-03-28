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
    static function find(int $id);
        
    /**
     * Deletes a row in database identified by your ID (primary key)
     *
     * @param int $id
     * @return void
     */
    static function destroy(int $id);

    /**
     * Deletes a row in database identified by your ID (primary key)
     *
     * @param int $id
     * @return void
     */
    function delete();
        
    /**
     * Updates or creates a row in the database
     *
     * @return IModelBase An object that represent the related row in the database
     */
    function save();
        
    /**
     * Creates an object with parameters passed by array
     *
     * @param array $params A named array with fields for the object creation
     * @return IModelBase
     */
    static function create(array $params);
}