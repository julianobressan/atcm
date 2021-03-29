<?php

namespace ATCM\Data\Models;

use ATCM\Data\ORM\ModelBase;

class Aircraft extends ModelBase
{
    protected bool $timestamps = true;

    public function enqueued(): array
    {
        return $this->hasMany(Queue::class);
    }
}