<?php

namespace App\Models;

class Course extends Model
{
    /**
     * The comments that belong to the course.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function comments()
    {
        return $this->belongsToMany(Comment::class)
            ->withPivot('professor_id');
    }

    /**
     * The professors that belong to the course.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function professors()
    {
        return $this->belongsToMany(Professor::class)
            ->withPivot('class', 'credit');
    }

    /**
     * The semesters that belong to the course.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function semesters()
    {
        return $this->belongsToMany(Semester::class);
    }
}
