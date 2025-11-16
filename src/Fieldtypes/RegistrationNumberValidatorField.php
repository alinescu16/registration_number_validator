<?php

namespace Alinandrei\RegistrationNumberValidator\Fieldtypes;

use Statamic\Fields\Fieldtype;
use Illuminate\Support\Facades\Log;

use Alinandrei\RegistrationNumberValidator\Services\VIESHeartBeatService;


class RegistrationNumberValidatorField extends Fieldtype
{

    // Set the fieldtype title
    protected static $title = 'VAT Validator';

    // Categorize the fieldtype under "Special"
    protected $categories = ['special'];

    // Field Icon
    protected $icon = 'revealer';

    // Make the fieldtype not available in the CP fieldtype selector
    protected $selectable = false;

    // Make the fieldtype available in forms
    protected $selectableInForms = true;

    /**
     * Define the field configuration options.
     *
     * @return array
     */
    protected function configFieldItems() : array
    {
        $viesServiceStatus = $this->getVIESHeartBeat() ? 'Available' : 'Unavailable';
        $viesServiceStatusClass = $viesServiceStatus == 'Available' ? 'text-lime-500' : 'text-rose-500';

        $notice = "<div class='service_notice mt-4'>
            <div class='service_notice_title mb-2'>
                <h3 class='text-md font-semibold'>Service Limitations and Markup Conditions.</h3>
            </div>
            <div class='service_notice mb-2'>The validation of the registration numbers relies on <a href='https://ec.europa.eu/taxation_customs/vies/#/vat-validation' target='_blank' rel='noopener noreferrer'>VIES VAT Validation</a> service. If the service is unavailable, the validation button is not shown, company validation is skipped and input is treated as valid. <span class='service_notice_service_status'>VAT Validation Service Status: <strong class='$viesServiceStatusClass'>$viesServiceStatus</strong></span></div>
            <div class='service_notice_field_layout'>If you are using a custom template on your website, for the validation to work propertly make sure to include the following components:
                <ul class='list-disc ml-4 mt-1'>
                    <li>An input field of <code>type='text'</code> and <code>name='{{ handle }}'</code> for user input.</li>
                    <li>A <code>button</code> or <code>input</code> with <code>type='button'</code> and <code>name='{{ handle }}_validate'</code> for validation triggering.</li>
                </ul>
            </div>
        </div>
        ";
        
        return array(
            'service_notice' => [
                'type' => 'section',
                'display' => 'NOTICE!',
                'instructions' => $notice,
                'classes' => array('registration_number_validator_service_notice'),
            ],
            'placeholder' => [
                'display' => 'Placeholder Text',
                'instructions' => 'Set the placeholder text for the VAT field.',
                'type' => 'text',
                'width' => 50,
            ],
            'show_validate_button' => [
                'display' => 'Show Validate Button',
                'instructions' => 'Whether to show the Validate button next to the VAT field.',
                'type' => 'toggle',
                'default' => true,
                'width' => 50,
            ],
            'validate_button_text' => [
                'display' => 'Button Text',
                'instructions' => 'The text for the Validate button.',
                'type' => 'text',
                'default' => 'Validate',
                'width' => 50,
                'if' => [
                    'show_validate_button' => 'equals true',
                ],
            ],
            'show_company_details_after_validation' => [
                'display' => 'Show Validation Results',
                'instructions' => 'Whether to Show the Company\'s Details or a "Not Found" notice after validation, or not.',
                'type' => 'toggle',
                'default' => true,
                'width' => 50,
            ],
        );
    } 

    /**
     * Provides extra data to be passed to the fieldtype's view.
     * This is the correct method for Statamic 5, replacing viewData().
     *
     * @return array
     */
    public function extraRenderableFieldData(): array
    {
        return array(
            // Pass the validation URL (from web.php) and CSRF token
            'validation_url' => '/!/registration_number_validator/validate',
            'csrf_token' => csrf_token(),
        );
    }


    /**
     * The view that should be used for the fieldtype.
     *
     * @return string
     */
    public function view()
    {
        return 'registration_number_validator::registration_number_validator_field';
    }

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

    /**
     * Check if the VIES service is available.
     *
     * @return bool
     */
    private function getVIESHeartBeat(): bool
    {
        $viesService = app(VIESHeartBeatService::class);

        return $viesService->isServiceAvailable();
    }
}
