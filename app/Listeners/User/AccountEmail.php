<?php

namespace App\Listeners\User;

use App\Events\User\SaveAccount;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class AccountEmail
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
     * @param  SaveAccount  $event
     * @return void
     */
    public function handle(SaveAccount $event)
    {
        Notification::send($event->data['user'], new \App\Notifications\User\AccountEmail($event->data));
    }
}
