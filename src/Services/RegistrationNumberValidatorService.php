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
        $validatedData = array();

        if (isset($this->validators[$countryCode])) {
            $nationalResult = $this->validators[$countryCode]->validate($number);
            
            if (! $nationalResult->isValid) {
                return $nationalResult;
            }

            $validatedData = $nationalResult->data ?? [];
        } else {
            Log::info("No national validator found for {$countryCode}, proceeding to VIES.");
        }
        
        $viesResult = $this->viesService->validateNumber($countryCode, $number);

        if (! $viesResult['valid']) {
            return new CountryValidatorResultDataTransferObject(
                empty($validatedData) ? false : true, 
                $viesResult['error'] ?? 'Invalid VIES status.', 
                $validatedData
            );
        }

        $validatedData = array_merge($validatedData ?? [], $viesResult['valid'] ? array( 'vies_status' => array( 'result' => 'The provided registration number is registered for cross-trading in the EU.', 'vies_success' => true ) ) : []);

        return new CountryValidatorResultDataTransferObject(true, null, $validatedData);
    }
}