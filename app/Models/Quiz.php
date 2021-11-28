<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d h:i',
    ];


    public function lesson()
    {
        return $this->belongsTo(Lesson::class, 'lesson_id');
    }

    public function questions()
    {
        return $this->hasMany(QuizQuestion::class, 'quiz_id');
    }
    public function Quiz_questions()
    {
        return $this->hasMany(QuizQuestion::class, 'quiz_id')->where('show',1)->orderBy('sort','asc');
    }
}
