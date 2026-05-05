<?php

namespace App\Providers;

use App\Models\PayrollBatch;
use App\Models\PayrollSetting;
use App\Models\Pph21TerBracket;
use App\Models\User;
use App\Observers\AuditObserver;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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
        $this->configureDefaults();
        $this->configureAuthorization();
        $this->registerObservers();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        if (app()->isProduction()) {
            URL::forceScheme('https');
        }

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }

    protected function configureAuthorization(): void
    {
        Gate::define('access-payroll-settings', fn (User $user) => $user->canAccessPayrollSettings());
        Gate::define('manage-payroll-settings', fn (User $user) => $user->canAccessPayrollSettings());
        Gate::define('manage-user-roles', fn (User $user) => $user->role === 'super-admin');
    }

    protected function registerObservers(): void
    {
        PayrollSetting::observe(AuditObserver::class);
        Pph21TerBracket::observe(AuditObserver::class);
    }
}
