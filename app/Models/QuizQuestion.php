<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $with = ['answers', 'Quiz'];

    public function getNameAttribute($name)
    {
        if ($this->attributes['type'] == 'image') {
            return asset('uploads/quizzes') . '/' . $name;
        }
        return $name;
    }


    public function answers()
    {
        return $this->hasMany(QuizQuestionAnswer::class, 'quiz_question_id');
    }

    public function Quiz()
    {
        return $this->belongsTo(Quiz::class, 'quiz_id');
    }

}
