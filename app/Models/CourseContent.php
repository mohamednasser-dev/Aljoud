<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class CourseContent extends Model
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



    public function Course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
}
