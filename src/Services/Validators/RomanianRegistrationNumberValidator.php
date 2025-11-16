<?php

namespace Alinandrei\RegistrationNumberValidator\Services\Validators;

use Alinandrei\RegistrationNumberValidator\Services\Validation\CountryValidatorInterface;
use Alinandrei\RegistrationNumberValidator\Services\Validation\CountryValidatorResultDataTransferObject;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RomanianRegistrationNumberValidator implements CountryValidatorInterface
{
    /**
     * Validates a Romanian CUI (VAT number) using the official ANAF v9 web service.
     *
     * @param string $number The CUI number to validate (may or may not include the "RO" prefix).
     * @return CountryValidatorResultDataTransferObject
     */
    public function validate(string $number): CountryValidatorResultDataTransferObject
    {
        // Initial Format Validation (Regex)
        $regex = "/^(RO)?[0-9]{2,10}$/";
        if (!preg_match($regex, $number)) {
            return new CountryValidatorResultDataTransferObject(false, "Invalid Romanian CUI format.");
        }

        // The ANAF service expects the CUI without the "RO" country prefix.
        $cui = preg_replace('/^RO/i', '', $number); 
        $currentDate = date('Y-m-d'); 
        $apiUrl = 'https://webservicesp.anaf.ro/api/PlatitorTvaRest/v9/tva';

        $payload = [
            [
                "cui" => $cui,
                "data" => $currentDate
            ]
        ];

        try {
            // Execute the HTTP POST Request
            $response = Http::timeout(10)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($apiUrl, $payload);
            
            $body = $response->json(); 

            Log::info($body);
            
            if (empty($body)) {
                return new CountryValidatorResultDataTransferObject(false, "The ANAF service returned an empty response.");
            }

            if (!empty($body['notFound']) && in_array($cui, $body['notFound'])) {
                return new CountryValidatorResultDataTransferObject(false, "The CUI is not registered in the ANAF database.");
            }

            if (empty($body['found']) || !isset($body['found'][0])) {
                return new CountryValidatorResultDataTransferObject(false, "The CUI was not found in the ANAF response.");
            }

            $foundData = $body['found'][0];
            
            return new CountryValidatorResultDataTransferObject(true, null, $foundData);

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error("ANAF Connection Exception: " . $e->getMessage());
            return new CountryValidatorResultDataTransferObject(false, "Connection error while contacting the ANAF service. Please try again.");
        } catch (\Exception $e) {
            Log::error("ANAF Validation Exception: " . $e->getMessage());
            return new CountryValidatorResultDataTransferObject(false, "A technical error occurred during ANAF validation: " . $e->getMessage());
        }
    }
}