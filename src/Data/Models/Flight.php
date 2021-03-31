<?php

namespace ATCM\Data\Models;

use ATCM\Data\ORM\ModelBase;
use ATCM\Data\Interfaces\IModelBase;

class Flight extends ModelBase
{
    protected bool $timestamps = true;

    public function aircraft(): ?IModelBase
    {
        return $this->belongsTo(Aircraft::class);
    }

    public static function isAircraftOnQueue($aircraftId): bool
    {
        $foreignKey = self::getForeignKey(Aircraft::class);
        $aircraft = self::first("{$foreignKey} = {$aircraftId}");
        $existsOnQueue = !is_null($aircraft);
        return $existsOnQueue;
    }
}