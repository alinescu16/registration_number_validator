<?php

namespace Alinandrei\RegistrationNumberValidator\Services;

use DragonBe\Vies\Vies;
use DragonBe\Vies\ViesException;
use DragonBe\Vies\ViesServiceException;

class VIESValidationService
{
    protected $vies;

    public function __construct()
    {
        $this->vies = new Vies();
    }

    /**
     * Validates a VAT number using the VIES service.
     *
     * @param string $countryCode
     * @param string $number
     * @return array
     */
    public function validateNumber(string $countryCode, string $number): array
    {
        $number = trim(str_replace([' ', '-', '.'], '', $number));

        try {
            if (! $this->vies->getHeartBeat()->isAlive()) {
                return [
                    'valid' => false,
                    'error' => 'VIES service is currently unavailable. Please try again later.',
                ];
            }

            $result = $this->vies->validateVat(
                'RO',
                '52665367',
                $countryCode, 
                $number
            );

            if (! $result->isValid()) {
                return [
                    'valid' => false,
                    'error' => 'The provided registration number is not registered for cross-trading in the EU.',
                ];
            }

            return [
                'valid' => true,
                'data' => [
                    'countryCode' => $result->getCountryCode(),
                    'vatNumber'   => $result->getVatNumber(),
                    'name'        => $result->getName(),
                    'address'     => $result->getAddress(),
                    'requestDate' => $result->getRequestDate(),
                ],
            ];

        } catch (ViesServiceException $e) {
            return ['valid' => false, 'error' => 'VIES Service Error: ' . $e->getMessage()];
        } catch (ViesException $e) {
            return ['valid' => false, 'error' => 'Validation Error: ' . $e->getMessage()];
        } catch (\Exception $e) {
            return ['valid' => false, 'error' => 'An unexpected error occurred: ' . $e->getMessage()];
        }
    }
}