<?php

namespace Alinandrei\RegistrationNumberValidator\Services\Validators;

use Alinandrei\RegistrationNumberValidator\Services\Validation\CountryValidatorInterface;
use Alinandrei\RegistrationNumberValidator\Services\Validation\CountryValidatorResultDataTransferObject;

class GermanyRegistrationNumberValidator implements CountryValidatorInterface
{
    /**
     * Validates a German VAT number (USt-IdNr.).
     *
     * @param string $number The VAT number to validate.
     * @return CountryValidatorResultDataTransferObject
     */
    public function validate(string $number): CountryValidatorResultDataTransferObject
    {
        // TODO: Implement Germany-specific national validation.
        // Example:
        // $regex = "/^(DE)?[0-9]{9}$/";
        // if (!preg_match($regex, $number)) {
        //     return new CountryValidatorResultDataTransferObject(false, "Invalid German VAT number format.");
        // }
        
        return new CountryValidatorResultDataTransferObject(true);
    }
}