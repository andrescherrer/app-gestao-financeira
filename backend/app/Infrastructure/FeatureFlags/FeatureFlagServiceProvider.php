<?php

namespace App\Infrastructure\FeatureFlags;

use Illuminate\Support\ServiceProvider;
use Laravel\Pennant\Feature;

class FeatureFlagServiceProvider extends ServiceProvider
{
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
        // Definir features iniciais
        Feature::define('new-dashboard', function ($user) {
            // Por padrão, desabilitado
            return false;
        });

        Feature::define('quick-transaction-v2', function ($user) {
            // Por padrão, desabilitado
            return false;
        });

        Feature::define('ofx-import', function ($user) {
            // Por padrão, habilitado para todos
            return true;
        });

        Feature::define('loan-accounts', function ($user) {
            // Por padrão, habilitado para todos
            return true;
        });
    }
}

