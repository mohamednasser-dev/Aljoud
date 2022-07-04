<?php

namespace App\Providers;

use App\Events\InboxCreated;
use App\Events\IncrementStudentsCountEvent;
use App\Listeners\IncrementStudentsCountListner;
use App\Listeners\NotifyInboxCreated;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        InboxCreated::class => [
            NotifyInboxCreated::class,
        ], IncrementStudentsCountEvent::class => [
            IncrementStudentsCountListner::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
