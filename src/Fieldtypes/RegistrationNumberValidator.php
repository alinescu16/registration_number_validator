<?php

namespace Alinandrei\RegistrationNumberValidator\Fieldtypes;

use Statamic\Fields\Fieldtype;


class RegistrationNumberValidator extends Fieldtype
{
    protected static $title = 'VAT Validator';

    protected $categories = ['special'];

    protected $icon = 'revealer';

    protected $selectable = false;

    protected $selectableInForms = true;

    /**
     * The blank/default value.
     *
     * @return array
     */
    public function defaultValue()
    {
        return null;
    }

    /**
     * Pre-process the data before it gets sent to the publish page.
     *
     * @param mixed $data
     * @return array|mixed
     */
    public function preProcess($data)
    {
        return $data;
    }

    /**
     * Process the data before it gets saved.
     *
     * @param mixed $data
     * @return array|mixed
     */
    public function process($data)
    {
        return $data;
    }

    public function view()
    {
        return 'registration_number_validator::registration_number_validator';
    }
}
