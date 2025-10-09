<?php

namespace Alinandrei\RegistrationNumberValidator;

use Statamic\Providers\AddonServiceProvider;

use Illuminate\Support\Facades\Log;

use Alinandrei\RegistrationNumberValidator\Fieldtypes\RegistrationNumberValidatorField;
use Alinandrei\RegistrationNumberValidator\Widgets\RegistrationNumberValidatorWidget;


class ServiceProvider extends AddonServiceProvider
{
    protected $vite = [ 
        'input' => [
            'resources/js/addon.js',
            'resources/js/field.js',
            'resources/css/addon.css',
        ],
        'publicDirectory' => 'resources',
        'buildDirectory' => 'build',
    ];

    protected $widgets = [
        RegistrationNumberValidatorWidget::class,
    ];
    
    protected $routes = [
        // 'cp' => __DIR__.'/../routes/cp.php',
        'actions' => __DIR__.'/../routes/actions.php',
        // 'web' => __DIR__.'/../routes/web.php',
    ];

    protected $fieldtypes = [
        // RegistrationNumberValidatorField::class,
    ];

    public function register()
    {
        parent::register();

        $this->app->singleton(\Alinandrei\RegistrationNumberValidator\Services\VIESHeartBeatService::class, function ($app) {
            return new \Alinandrei\RegistrationNumberValidator\Services\VIESHeartBeatService();
        });
    }

    public function bootAddon()
    {
        RegistrationNumberValidatorField::register();

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'registration_number_validator');
    }
}
