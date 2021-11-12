<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLesson extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function Lesson()
    {
        return $this->belongsTo(Lesson::class, 'lesson_id');
    }

    public function Users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
