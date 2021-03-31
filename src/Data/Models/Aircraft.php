<?php

namespace ATCM\Data\Models;

use ATCM\Data\ORM\ModelBase;

class Aircraft extends ModelBase
{
    protected bool $timestamps = true;

    public function flights(): array
    {
        return $this->hasMany(Flight::class);
    }
}