<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ExerciseResult extends Model
{
    use HasFactory;

    protected $table = 'exercise_results';

    protected $guarded = ['id'];

    public function user() : HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function exercise() : HasOne
    {
        return $this->hasOne(Exercise::class, 'id', 'exercise_id');
    }
}
