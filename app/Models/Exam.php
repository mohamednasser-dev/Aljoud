<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $with = ['Course'];
    protected $casts = [
        'created_at' => 'datetime:Y-m-d h:i',
    ];


    public function Course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function questions()
    {
        return $this->hasMany(ExamQuestion::class, 'exam_id');
    }
    public function ExamQuestion()
    {
        return $this->hasMany(ExamQuestion::class, 'exam_id')->where('show',1)->orderBy('sort','asc');
    }

}
