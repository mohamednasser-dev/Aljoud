<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class Video extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function getUrlAttribute($url){
        if (!empty($url)) {
            return asset('uploads/videos') . '/' . $url;
        }
        return asset('uploads/videos/default.jpg');
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
