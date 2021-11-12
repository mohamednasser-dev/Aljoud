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
}
