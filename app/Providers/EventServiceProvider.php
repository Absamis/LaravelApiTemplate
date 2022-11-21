<?php

namespace App\Providers;

use App\Events\AccountCreated;
use App\Events\AccountRegistered;
use App\Events\PasswordChanged;
use App\Events\PasswordRecovery;
use App\Listeners\SendAccountCreatedNotification;
use App\Listeners\SendAccountVerificationNotification;
use App\Listeners\SendPasswordChangedNotification;
use App\Listeners\SendPasswordResetNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        AccountRegistered::class => [
            SendAccountVerificationNotification::class,
        ],
        AccountCreated::class => [
            SendAccountCreatedNotification::class
        ],
        PasswordRecovery::class => [
            SendPasswordResetNotification::class
        ],
        PasswordChanged::class => [
            SendPasswordChangedNotification::class
        ]
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

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
