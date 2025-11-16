<?php

namespace Alinandrei\RegistrationNumberValidator\Services\Validators;

use Alinandrei\RegistrationNumberValidator\Services\Validation\CountryValidatorInterface;
use Alinandrei\RegistrationNumberValidator\Services\Validation\CountryValidatorResultDataTransferObject;

class RomanianRegistrationNumberValidator implements CountryValidatorInterface
{
    /**
     * Validates a Romanian VAT number (CIF).
     *
     * @param string $number The VAT number to validate.
     * @return CountryValidatorResultDataTransferObject
     */
    public function validate(string $number): CountryValidatorResultDataTransferObject
    {
        // TODO: Implement Romania-specific national validation (checksum, regex).
        // Example:
        // $regex = "/^(RO)?[0-9]{2,10}$/";
        // if (!preg_match($regex, $number)) {
        //     return new CountryValidatorResultDataTransferObject(false, "Invalid Romanian VAT number format.");
        // }

        // For now, we'll just say the format is valid and let VIES check it.
        return new CountryValidatorResultDataTransferObject(true);
    }
}