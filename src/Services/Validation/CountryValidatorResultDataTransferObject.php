<?php

namespace Alinandrei\RegistrationNumberValidator\Services\Validation;

/**
 * A simple Data Transfer Object (DTO) to hold validation results.
 * This is cleaner than passing arrays around.
 */
class CountryValidatorResultDataTransferObject
{
    public bool $isValid;
    public ?string $message;
    public ?array $data;

    /**
     * @param bool $isValid Whether the validation passed.
     * @param string|null $message An error message if it failed.
     * @param array|null $data Any extra data (like VIES company info).
     */
    public function __construct(bool $isValid, ?string $message = null, ?array $data = null)
    {
        $this->isValid = $isValid;
        $this->message = $message;
        $this->data = $data;
    }
}