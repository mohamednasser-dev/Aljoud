<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function getUrlAttribute($url){
        if (!empty($url)) {
            return  'https://res.cloudinary.com/dwevccen7/video/upload/v1581928924/' . $url;
        }
        return '';
    }
    protected $appends = ['name'];

    public function getNameAttribute()
    {
        if ($locale = App::currentLocale() == "ar") {
            return $this->name_ar;
        } else {
            return $this->name_en;
        }
    }
}
