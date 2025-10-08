<?php

namespace Alinandrei\RegistrationNumberValidator;

use Statamic\Providers\AddonServiceProvider;

use Illuminate\Support\Facades\Log;

use Alinandrei\RegistrationNumberValidator\Fieldtypes\RegistrationNumberValidator;


class ServiceProvider extends AddonServiceProvider
{
    protected $vite = [ 
        'input' => [
            'resources/js/addon.js',
            'resources/css/addon.css',
        ],
        'publicDirectory' => 'resources',
        'buildDirectory' => 'build',
    ];

    protected $routes = [
        // 'cp' => __DIR__.'/../routes/cp.php',
        'actions' => __DIR__.'/../routes/actions.php',
        // 'web' => __DIR__.'/../routes/web.php',
    ];

    protected $fieldtypes = [
        RegistrationNumberValidator::class,
    ];

    public function register()
    {
        parent::register();
    }

    public function bootAddon()
    {
        RegistrationNumberValidator::register();

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'registration_number_validator');
    }
}
