<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class College extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $with = ['University'];
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

    public function Levels(){
        return $this->hasMany(Level::class ,'college_id');
    }


    public function University(){
        return $this->belongsTo(University::class ,'university_id');
    }


    public function getImageAttribute($image)
    {
        if (!empty($image)) {
            return asset('uploads/colleges') . '/' . $image;
        }
        return asset('uploads/users/default.jpg');
    }

    public function setImageAttribute($image)
    {

        if (is_file($image)) {
            $imageFields = upload($image, 'colleges');
            $this->attributes['image'] = $imageFields;

        }

    }

}
