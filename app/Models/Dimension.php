<?php

declare(strict_types=1);

namespace App\Models;

/**
 * @property integer $id
 * @property string $name
 */
final class Dimension extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
