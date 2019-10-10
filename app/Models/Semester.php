<?php

namespace App\Models;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Semester extends Model
{
    /**
     * Get newest semester.
     *
     * @return static
     */
    public static function newest(): self
    {
        return Cache::remember('newest-semester', 60 * 60 * 24, function () {
            return self::all()
                ->sort(function ($a, $b) {
                    $cmp = intval($a->name) <=> intval($b->name);

                    if ($cmp !== 0) {
                        return $cmp;
                    }

                    return Str::endsWith($a->name, '上') ? -1 : 1;
                })
                ->last();
        });
    }
}
