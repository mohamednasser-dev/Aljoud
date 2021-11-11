<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class College extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d h:i',
    ];

    public function Levels(){
        return $this->hasMany(Level::class ,'college_id');
    }


    public function University(){
        return $this->belongsTo(University::class ,'university_id');
    }
}
