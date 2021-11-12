<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instructor extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function getImageAttribute($image)
    {
        if (!empty($image)){
            return asset('uploads/instructors').'/'.$image;
        }
        return asset('uploads/users/default.jpg');
    }

    public function setImageAttribute($image)
    {
        if (is_file($image)) {
            $imageFields = upload($image, 'instructors');
            $this->attributes['image'] = $imageFields;
        }
    }
}
