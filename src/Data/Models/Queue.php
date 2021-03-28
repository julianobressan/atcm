<?php

namespace ATCM\Data\Models;

use ATCM\Data\ORM\ModelBase;
use ATCM\Data\Interfaces\IModelBase;

class Queue extends ModelBase
{
    protected bool $timestamps = true;

    public function aircraft(): ?IModelBase
    {
        return $this->belongsTo(Aircraft::class);
    }
}