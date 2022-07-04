<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function Course(){
//        ->select('id','name_ar','name_en','price','discount','image','instructor_id','currency_id')
        return $this->belongsTo(Course::class ,'course_id');
    }
}
