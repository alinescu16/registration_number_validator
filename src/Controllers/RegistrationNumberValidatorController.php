<?php

namespace Alinandrei\RegistrationNumberValidator\Controllers;

use Illuminate\Http\Request;
use Statamic\Http\Controllers\CP\CpController;
use Illuminate\Support\Facades\Log;
use Alinandrei\RegistrationNumberValidator\Services\RegistrationNumberValidatorService;

class RegistrationNumberValidatorController extends CpController
{
    /**
     * Handle the validation request.
     *
     * @param Request $request
     * @param VIESValidationService $validationService
     * @return \Illuminate\Http\JsonResponse
     */
    public function registrationNumberValidate(Request $request, RegistrationNumberValidatorService $validationManager)
    {
        $request->validate([
            'country_code' => 'required|string|size:2',
            'number'       => 'required|string',
        ]);

        $result = $validationManager->validate(
            $request->input('country_code'),
            $request->input('number')
        );

        Log::info('RegistrationNumberValidatorController - Validation Result:');
        Log::info((array) $result);

        if (! $result->isValid) {
            return response()->json([
                'valid' => false,
                'error' => $result->message
            ], 422);
        }

        return response()->json([
            'valid' => true,
            'data' => array_merge(
                $result->data,
                array(
                    'vies_status' => $result->message ?? $result->data['vies_status'] ?? null,
                ),
                array(
                    'country_code' => strtoupper($request->input('country_code'))
                )
            )
        ]);
    }
}