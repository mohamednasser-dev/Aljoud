<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class Course extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d h:i',
    ];

    protected $appends = ['name', 'desc'];
    protected $with = ['Instructor', 'Content'];

    public function getNameAttribute()
    {
        if ($locale = App::currentLocale() == "ar") {
            return $this->name_ar;
        } else {
            return $this->name_en;
        }
    }

    public function getDescAttribute()
    {
        if ($locale = App::currentLocale() == "ar") {
            return $this->desc_ar;
        } else {
            return $this->desc_en;
        }
    }

    public function Lesson()
    {
        return $this->hasMany(Lesson::class, 'course_id');
    }

    public function Content()
    {
        return $this->hasMany(CourseContent::class, 'course_id');
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

    public function Offers()
    {
        return $this->belongsToMany(Offer::class, 'offer_courses', 'course_id', 'offer_id');
    }

    public function getImageAttribute($image)
    {
        if (!empty($image)) {
            return asset('uploads/courses') . '/' . $image;
        }
        return asset('uploads/users/default.jpg');
    }

    public function setImageAttribute($image)
    {

        if (is_file($image)) {
            $imageFields = upload($image, 'courses');
            $this->attributes['image'] = $imageFields;

        }

    }

}
