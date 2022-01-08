<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class Article extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $with = ['lesson'];
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


    public function lesson()
    {
        return $this->belongsTo(Lesson::class, 'lesson_id');
    }


    public function getFileAttribute($image)
    {
        if (!empty($image)) {
            return asset('uploads/articles') . '/' . $image;
        }
        return asset('uploads/users/default.jpg');
    }

    public function setFileAttribute($image)
    {

        if (is_file($image)) {
            $imageFields = upload($image, 'articles');
            $this->attributes['file'] = $imageFields;
        }

    }
}
