<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class University extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d h:i',
    ];
    public function Colleges(){
        return $this->hasMany(College::class ,'university_id');
    }


    public function getImageAttribute($image)
    {
        if (!empty($image)){
            return asset('uploads/universities').'/'.$image;
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
