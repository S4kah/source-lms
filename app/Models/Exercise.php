<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Exercise extends Model
{
    use HasFactory;

    protected $table = 'exercises';

    protected $guarded = ['id'];

    public function course() : HasOne
    {
        return $this->hasOne(Course::class, 'id', 'course_id');
    }

    public function results(): HasMany
    {
        return $this->hasMany(ExerciseResult::class, 'exercise_id', 'id');
    }
}
