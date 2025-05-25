<?php

declare(strict_types=1);

namespace App\Models;

/**
 * @property integer $id
 * @property string $college
 * @property string $name
 * @property string $code
 */
final class Department extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
