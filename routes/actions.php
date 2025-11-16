<?php

use Illuminate\Support\Facades\Route;

use Alinandrei\RegistrationNumberValidator\Controllers\RegistrationNumberValidatorController;

Route::post('validate', [ RegistrationNumberValidatorController::class, 'registrationNumberValidate' ]);