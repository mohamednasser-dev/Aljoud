<?php

namespace App\Listeners;

use App\Events\IncrementStudentsCountEvent;
use App\Models\University;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class IncrementStudentsCountListner
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(IncrementStudentsCountEvent $event)
    {
        $userCourses = $event->userCourses;
//        todo increment here
        $univ = University::whereId($userCourses->Course->Level->College->university_id)->first();
        $univ->students +=  1 ;
        $univ->save();
    }
}
