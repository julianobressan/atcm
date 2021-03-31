<?php

namespace ATCM\Data\Models;

use ATCM\Data\Interfaces\IModelBase;
use ATCM\Data\ORM\ModelBase;

class User extends ModelBase
{
    protected bool $timestamps = true;

    public static function findByLogin(string $login): ?IModelBase
    {
        $user = User::first("login LIKE '{$login}'");
        return $user;
    }
}