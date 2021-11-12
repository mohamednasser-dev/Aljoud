<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizQuestionAnswer extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function getNameAttribute($name)
    {
        if ($this->attributes['type'] == 'image') {
            return asset('uploads/quizzes') . '/' . $name;
        }
        return $name;
    }


    public function QuizQuestion()
    {
        return $this->belongsTo(QuizQuestion::class, 'quiz_question_id');
    }
}
