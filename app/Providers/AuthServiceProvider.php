<?php

namespace App\Providers;
use App\Models\Resource;
use App\Policies\ResourcesPolicy;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Program::class => ProgramPolicy::class,
        Interview::class => InterviewPolicy::class,
        Resource::class => ResourcesPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot()
    {
        $this->registerPolicies();

        // Additional gates or policies can be registered here if needed
    }
}
