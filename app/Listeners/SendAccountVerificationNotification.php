<?php

namespace App\Listeners;

use App\Events\AccountRegistered;
use App\Mail\AccountVerification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendAccountVerificationNotification implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public $tries = 3;
    public $afterCommit = true;


    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\AccountRegistered  $event
     * @return void
     */
    public function handle(AccountRegistered $event)
    {
        //
        Mail::to($event->user["email"])->send(new AccountVerification($event->user, $event->data));
    }
}
