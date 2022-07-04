<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCourses extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d h:i',
    ];
    protected $dispatchesEvents = [
        'created'=>'App\Events\IncrementStudentsCountEvent'
    ];

    public function Course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function Users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

//    protected $dispatchesEvents = ['created' => 'App\Events\NotificationEvent'];
}
