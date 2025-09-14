<?php

namespace App\Providers;

use App\Services\AddressService;
use App\Services\ContactService;
use App\Services\Implements\AddressServiceImplement;
use App\Services\Implements\ContactServiceImplement;
use App\Services\Implements\UserServiceImplement;
use App\Services\UserService;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class ContactManagementServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public array $singletons = [
        UserService::class => UserServiceImplement::class,
        ContactService::class => ContactServiceImplement::class,
        AddressService::class => AddressServiceImplement::class
    ];

    public function provides(): array
    {
        return [
            UserService::class,
            ContactService::class,
            AddressService::class
        ];
    }

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
