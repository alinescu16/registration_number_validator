<?php

namespace Alinandrei\RegistrationNumberValidator;

use Statamic\Providers\AddonServiceProvider;
use Illuminate\Support\Facades\Log;
use Alinandrei\RegistrationNumberValidator\Fieldtypes\RegistrationNumberValidatorField;
use Alinandrei\RegistrationNumberValidator\Widgets\RegistrationNumberValidatorWidget;
use Alinandrei\RegistrationNumberValidator\Services\RegistrationNumberValidatorService;
use Alinandrei\RegistrationNumberValidator\Services\VIESHeartBeatService;
use Alinandrei\RegistrationNumberValidator\Services\VIESValidationService;
use Alinandrei\RegistrationNumberValidator\Services\Validators\RomanianRegistrationNumberValidator;
use Alinandrei\RegistrationNumberValidator\Services\Validators\GermanyRegistrationNumberValidator;

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

        $this->app->singleton(VIESHeartBeatService::class, function ($app) {
            return new VIESHeartBeatService();
        });

        $this->app->singleton(VIESValidationService::class, function ($app) {
            return new VIESValidationService();
        });

        $this->app->singleton(RegistrationNumberValidatorService::class, function ($app) {
            return new RegistrationNumberValidatorService(
                $app->make(VIESValidationService::class)
            );
        });
    }

    public function bootAddon()
    {
        RegistrationNumberValidatorField::register();

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'registration_number_validator');

        try {
            $manager = $this->app->make(RegistrationNumberValidatorService::class);

            $manager->registerValidator('RO', new RomanianRegistrationNumberValidator());
            $manager->registerValidator('DE', new GermanyRegistrationNumberValidator());
        } catch (\Exception $e) {
            Log::error('Failed to register VAT validators: ' . $e->getMessage());
        }
    }
}