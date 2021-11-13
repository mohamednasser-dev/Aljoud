<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class Level extends Model
{
    use HasFactory;
    protected $guarded = [];
//    protected $with =['College'];
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

    public function Courses(){
        return $this->hasMany(Course::class ,'level_id');
    }


    public function College(){
        return $this->belongsTo(College::class ,'college_id');
    }



    public function getImageAttribute($image)
    {
        if (!empty($image)) {
            return asset('uploads/levels') . '/' . $image;
        }
        return asset('uploads/users/default.jpg');
    }

    public function setImageAttribute($image)
    {

        if (is_file($image)) {
            $imageFields = upload($image, 'levels');
            $this->attributes['image'] = $imageFields;

        }

    }
}
