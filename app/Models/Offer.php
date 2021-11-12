<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class Offer extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $hidden = ['created_at','updated_at'];

    public function Courses(){
        return $this->belongsToMany(Course::class, 'offer_courses', 'offer_id', 'course_id');
    }

    public function Level()
    {
        return $this->belongsTo(Level::class, 'level_id');
    }

    public function Currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    protected $appends = ['name', 'desc'];

    public function getNameAttribute()
    {
        if ($locale = App::currentLocale() == "ar") {
            return $this->name_ar;
        } else {
            return $this->name_en;
        }
    }
    public function getDescAttribute()
    {
        if ($locale = App::currentLocale() == "ar") {
            return $this->desc_ar;
        } else {
            return $this->desc_en;
        }
    }



}
