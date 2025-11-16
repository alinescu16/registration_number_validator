<?php

namespace Alinandrei\RegistrationNumberValidator\Services;

use Alinandrei\RegistrationNumberValidator\Services\Validation\CountryValidatorInterface;
use Alinandrei\RegistrationNumberValidator\Services\Validation\CountryValidatorResultDataTransferObject;
use Illuminate\Support\Facades\Log;

class RegistrationNumberValidatorService
{
    /**
     * @var VIESValidationService
     */
    protected $viesService;

    /**
     * @var array
     */
    protected $validators = [];

    public function __construct(VIESValidationService $viesService)
    {
        $this->viesService = $viesService;
    }

    /**
     * Registers a country-specific validator strategy.
     *
     * @param string $countryCode (e.g., "RO", "DE")
     * @param CountryValidatorInterface $validator
     */
    public function registerValidator(string $countryCode, CountryValidatorInterface $validator)
    {
        $this->validators[strtoupper($countryCode)] = $validator;
    }

    /**
     * Getter to see the registered validators
     */
    public function getValidators(): array
    {
        return array_keys($this->validators);
    }

    /**
     * The main validation method.
     *
     * @param string $countryCode
     * @param string $number
     * @return CountryValidatorResultDataTransferObject
     */
    public function validate(string $countryCode, string $number): CountryValidatorResultDataTransferObject
    {
        $countryCode = strtoupper($countryCode);
        
        Log::info($countryCode);
        Log::info($this->validators);

        if (isset($this->validators[$countryCode])) {
            $nationalResult = $this->validators[$countryCode]->validate($number);
            
            Log::info($nationalResult);
            
            if (! $nationalResult->isValid) {
                return $nationalResult;
            }
        }
        
        $viesResult = $this->viesService->validateNumber($countryCode, $number);

        Log::info($viesResult);

        if (! $viesResult['valid']) {
            return new CountryValidatorResultDataTransferObject(false, $viesResult['error'] ?? 'Invalid VIES status.');
        }

        return new CountryValidatorResultDataTransferObject(true, null, $viesResult['data'] ?? null);
    }
}