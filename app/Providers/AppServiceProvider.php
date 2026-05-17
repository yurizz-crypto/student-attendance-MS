<?php

namespace App\Providers;

use App\Models\AttendanceSession;
use App\Models\ClassSection;
use App\Models\User;
use App\Policies\FacultyPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register authorization policies
        $this->registerPolicies();
    }

    /**
     * Register authorization policies.
     */
    protected function registerPolicies(): void
    {
        // User policy
        Gate::policy(User::class, UserPolicy::class);

        // Faculty policies
        Gate::policy(ClassSection::class, FacultyPolicy::class);
        Gate::policy(AttendanceSession::class, FacultyPolicy::class);
    }
}
