<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d h:i',
    ];

    public function Lesson()
    {
        return $this->hasMany(Lesson::class, 'course_id');
    }

    public function CourseContents()
    {
        return $this->hasMany(CourseContent::class, 'course_id');
    }

    public function CourseRates()
    {
        return $this->hasMany(CourseRate::class, 'course_id');
    }

    public function CourseExams()
    {
        return $this->hasMany(Exam::class, 'course_id');
    }

    public function Level()
    {
        return $this->belongsTo(Level::class, 'level_id');
    }
    public function Currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function Instructor()
    {
        return $this->belongsTo(Instructor::class, 'instructor_id');
    }
}
