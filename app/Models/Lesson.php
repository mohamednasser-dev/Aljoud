<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class Lesson extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d h:i',
    ];

    protected $appends = ['name'];

    public function getNameAttribute()
    {
        if ($locale = App::currentLocale() == "ar") {
            return $this->name_ar;
        } else {
            return $this->name_en;
        }
    }

    public function videos()
    {
        return $this->hasMany(Video::class, 'lesson_id');
    }
    public function Lesson_videos()
    {
        return $this->hasMany(Video::class, 'lesson_id')->where('show',1)->orderBy('sort','asc');;
    }

    public function quizes()
    {
        return $this->hasMany(Quiz::class, 'lesson_id');
    }
    public function lesson_quizzes()
    {
        return $this->hasMany(Quiz::class, 'lesson_id')->where('show',1)->orderBy('sort','asc');
    }

    public function articles()
    {
        return $this->hasMany(Article::class, 'lesson_id');
    }


    public function Course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_lessons', 'lesson_id', 'user_id');
    }

    public function getImageAttribute($image)
    {
        if (!empty($image)) {
            return asset('uploads/lessons') . '/' . $image;
        }
        return asset('uploads/users/default.jpg');
    }

    public function setImageAttribute($image)
    {

        if (is_file($image)) {
            $imageFields = upload($image, 'lessons');
            $this->attributes['image'] = $imageFields;

        }

    }
}
