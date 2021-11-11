<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $casts = [
        'created_at' => 'datetime:Y-m-d h:i',
    ];
    public function Courses(){
        return $this->hasMany(Course::class ,'level_id');
    }


    public function College(){
        return $this->belongsTo(College::class ,'college_id');
    }
}
