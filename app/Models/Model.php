<?php

namespace App\Models;

abstract class Model extends \Illuminate\Database\Eloquent\Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
}
