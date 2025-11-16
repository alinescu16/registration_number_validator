<?php

namespace Alinandrei\RegistrationNumberValidator\Services\Validation;

use Alinandrei\RegistrationNumberValidator\Services\Validation\CountryValidatorResultDataTransferObject;

/**
 * Interface (Contract) for all country-specific VAT validators.
 *
 * Each country validator must implement this interface, which provides
 * a way to check the national-level format (like regex or checksum)
 * before we check it against the VIES database.
 */
interface CountryValidatorInterface
{
    /**
     * Validates a national registration number's format.
     *
     * @param string $number The VAT number to validate.
     * @return CountryValidatorResultDataTransferObject A DTO containing the result.
     */
    public function validate(string $number): CountryValidatorResultDataTransferObject;
}