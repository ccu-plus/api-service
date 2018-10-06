<?php

namespace App\Models;

use Laravel\Scout\Searchable;

class Course extends Model
{
    use Searchable;

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
     * Get the department that owns the course.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the dimension that owns the course.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function dimension()
    {
        return $this->belongsTo(Dimension::class);
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

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        foreach (['department', 'dimension', 'professors'] as $relation) {
            if (!$this->relationLoaded($relation)) {
                $this->load($relation);
            }
        }

        return [
            'code' => $this->code,
            'name' => $this->name,
            'college' => $this->department->college,
            'department' => $this->department->name,
            'dimension' => optional($this->dimension)->name,
            'professors' => $this->professors->pluck('name')->toArray(),
        ];
    }

    /**
     * Get the value used to index the model.
     *
     * @return mixed
     */
    public function getScoutKey()
    {
        return $this->code;
    }
}
