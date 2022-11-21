<?php

namespace App\Listeners;

use App\Events\PasswordRecovery;
use App\Mail\PasswordReset;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendPasswordResetNotification implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public $afterCommit = true;
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\PasswordRecovery  $event
     * @return void
     */
    public function handle(PasswordRecovery $event)
    {
        //
        Mail::to($event->user["email"])->send(new PasswordReset($event->user, $event->data));
    }
}
