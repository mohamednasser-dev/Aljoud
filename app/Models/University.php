<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class University extends Model
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


    public function Colleges()
    {
        return $this->hasMany(College::class, 'university_id');
    }

    public function Specialists()
    {
        return $this->hasMany(College::class, 'university_id')->where('show',1);
    }


    public function getImageAttribute($image)
    {
        if (!empty($image)) {
            return asset('uploads/universities') . '/' . $image;
        }
        return asset('uploads/users/default.jpg');
    }

    public function setImageAttribute($image)
    {

        if (is_file($image)) {
            $imageFields = upload($image, 'universities');
            $this->attributes['image'] = $imageFields;

        }

    }

}
