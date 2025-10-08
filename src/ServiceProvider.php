<?php

namespace Alinandrei\RegistrationNumberValidator;

use Statamic\Providers\AddonServiceProvider;

use Illuminate\Support\Facades\Log;

use Alinandrei\RegistrationNumberValidator\Fieldtypes\RegistrationNumberValidator;


class ServiceProvider extends AddonServiceProvider
{
    /**
     * Vite configuration
     * 
     */
    protected $vite = [ 
        'input' => [
            'resources/js/addon.js',
        ],
        'publicDirectory' => 'resources',
        'buildDirectory' => 'build',
    ];

    /**
     * Define routes
     * 
     */
    protected $routes = [
        // 'cp' => __DIR__.'/../routes/cp.php',
        'actions' => __DIR__.'/../routes/actions.php',
        // 'web' => __DIR__.'/../routes/web.php',
    ];

    protected $fieldtypes = [
        RegistrationNumberValidator::class,
    ];

    /**
     * Register in the Service Container
     * 
     */
    public function register()
    {
        parent::register();

    }
    public function bootAddon()
    {
        RegistrationNumberValidator::register();

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'registration_number_validator');

        Log::info("Addon Registration Number Validator loaded");
    }
}
