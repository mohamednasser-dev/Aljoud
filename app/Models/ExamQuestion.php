<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamQuestion extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $with = ['answers', 'exam'];

    public function getNameAttribute($name)
    {
        if ($this->attributes['type'] == 'image') {
            return asset('uploads/exams') . '/' . $name;
        }
        return $name;
    }


    public function answers()
    {
        return $this->hasMany(ExamQuestionAnswer::class, 'exam_question_id');
    }

    public function Exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

}
