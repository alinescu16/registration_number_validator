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

    public function register()
    {
        parent::register();

        $this->app->singleton(\Alinandrei\RegistrationNumberValidator\Services\VIESHeartBeatService::class, function ($app) {
            return new \Alinandrei\RegistrationNumberValidator\Services\VIESHeartBeatService();
        });

        $this->app->singleton(\Alinandrei\RegistrationNumberValidator\Services\VIESValidationService::class, function ($app) {
            return new \Alinandrei\RegistrationNumberValidator\Services\VIESValidationService();
        });

        Log::info("Adding RegistrationNumberValidatorService");

        $this->app->singleton(Alinandrei\RegistrationNumberValidator\Services\RegistrationNumberValidatorService::class, function ($app) {
            
            Log::info("Before managers");
            
            $manager = new Alinandrei\RegistrationNumberValidator\Services\RegistrationNumberValidatorService(
                $app->make(\Alinandrei\RegistrationNumberValidator\Services\VIESValidationService::class)
            );

            Log::info("Adding managers");

            $manager->registerValidator('RO', new Alinandrei\RegistrationNumberValidator\Services\Validators\RomanianRegistrationNumberValidator());
            $manager->registerValidator('DE', new Alinandrei\RegistrationNumberValidator\Services\Validators\GermanyRegistrationNumberValidator());
            Log::info($manager->getValidators());
            return $manager;
        });
    }

    public function bootAddon()
    {
        RegistrationNumberValidatorField::register();

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'registration_number_validator');
    }
}
