<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfferCourse extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $hidden = ['created_at','updated_at'];

    public function Offer()
    {
        return $this->belongsTo(Offer::class, 'offer_id');
    }

    public function Course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
}
