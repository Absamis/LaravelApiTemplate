<?php

namespace App\Listeners;

use App\Events\AccountCreated;
use App\Mail\WelcomeNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendAccountCreatedNotification implements ShouldQueue
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
     * @param  \App\Events\AccountCreated  $event
     * @return void
     */
    public function handle(AccountCreated $event)
    {
        //
        Mail::to($event->user["email"])->send(new WelcomeNotification($event->user));
    }
}
