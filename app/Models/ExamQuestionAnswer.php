<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamQuestionAnswer extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function getNameAttribute($name)
    {
        if ($this->attributes['type'] == 'image') {
            return asset('uploads/exams') . '/' . $name;
        }
        return $name;
    }


    public function ExamQuestion()
    {
        return $this->belongsTo(ExamQuestion::class, 'exam_question_id');
    }
}
