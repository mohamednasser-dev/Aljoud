<?php

namespace App\Listeners;

use App\Events\InboxCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyInboxCreated
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
     * @param InboxCreated $event
     * @return void
     */
    public function handle(InboxCreated $event)
    {
        $inbox = $event->inbox;
        $receiver = $inbox->Receiver;
        send($receiver->fcm_token, 'رسالة جديدة', $inbox->message, $inbox->message);
        $assistant = $inbox->Assistance;
        if ($assistant) {
            send($assistant->fcm_token, 'رسالة جديدة', $inbox->message, "inbox" , $inbox->id );
        }

    }
}
