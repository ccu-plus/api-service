<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Scout\Searchable;

/**
 * @property string $code
 * @property string $name
 * @property Department $department
 * @property Dimension $dimension
 * @property Collection $semesters
 * @property Collection $professors
 * @property Collection $comments
 */
class Course extends Model
{
    use Searchable;

    /**
     * The comments that belong to the course.
     *
     * @return BelongsToMany
     */
    public function comments(): BelongsToMany
    {
        return $this->belongsToMany(Comment::class, 'course_comment')
            ->withPivot('professor_id');
    }

    /**
     * Get the department that owns the course.
     *
     * @return BelongsTo
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the dimension that owns the course.
     *
     * @return BelongsTo
     */
    public function dimension(): BelongsTo
    {
        return $this->belongsTo(Dimension::class);
    }

    /**
     * The professors that belong to the course.
     *
     * @return BelongsToMany
     */
    public function professors(): BelongsToMany
    {
        return $this->belongsToMany(Professor::class)
            ->withPivot('semester_id', 'class', 'credit');
    }

    /**
     * The semesters that belong to the course.
     *
     * @return BelongsToMany
     */
    public function semesters(): BelongsToMany
    {
        return $this->belongsToMany(Semester::class);
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray(): array
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
            'professors' => $this->professors->pluck('name')->unique()->values()->toArray(),
        ];
    }
}
