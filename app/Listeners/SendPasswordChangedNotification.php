<?php

namespace App\Listeners;

use App\Events\PasswordChanged;
use App\Mail\PasswordChanged as MailPasswordChanged;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendPasswordChangedNotification implements ShouldQueue
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
     * @param  \App\Events\PasswordChanged  $event
     * @return void
     */
    public function handle(PasswordChanged $event)
    {
        //
        Mail::to($event->user["email"])->send(new MailPasswordChanged($event->user, $event->data));
    }
}
