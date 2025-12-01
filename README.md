Registration Number Validator for Statamic

A robust Statamic addon to validate company registration numbers (VAT, CUI, etc.) directly on your frontend forms. This addon integrates with VIES and country-specific validators (RO, DE, etc.) to ensure data accuracy before form submission.

Features

Real-time Validation: Validates registration numbers against VIES and national databases.

Frontend Ready: Includes a pre-styled Vue component for instant usage ("Plug & Play").

Headless API: Exposes a JSON endpoint for fully custom UI implementations (e.g., Alpine.js).

Data Enrichment: Retrieves and autofills company details (Name, Address, Registration Date) upon successful validation.

Multi-Country Support: Specialized validation logic for Romania (RO), Germany (DE), and generic EU VIES validation.

Installation

Install the addon via Composer:

composer require alinandrei/registration-number-validator


After installation, publish the assets (required for the Plug & Play component):

php artisan vendor:publish --tag=registration-number-validator-assets --force


Usage

You have two ways to use this addon on your frontend:

Option 1: Plug & Play (Recommended)

Use the included Antlers tag to render a complete, styled validation component. This includes the country selector, input field, validation logic, loading states, and error handling.

{{ registration_number_validator 
    handle="billing_vat"
    placeholder="Enter Registration Number" 
    :show_validate_button="true"
    :show_company_details_after_validation="true"
}}


Parameters

Parameter

Type

Default

Description

handle

String

Required

Unique identifier. Generates inputs named {handle}_country and {handle}_number.

placeholder

String

""

Placeholder text for the input field.

show_validate_button

Boolean

true

Whether to display the "Validate" button next to the input.

validate_button_text

String

"Validate"

Text to display on the button.

show_company_details_after_validation

Boolean

true

If true, displays company name and address below the input on success.

Option 2: Fully Custom (Headless)

If you need complete control over the UI (e.g., custom layout, your own icons, or integration with a multi-step wizard), you can bypass the Vue component and interact directly with the Validation API.

We recommend using Alpine.js for a lightweight implementation.

The Endpoint

URL: /!/registration_number_validator/validate

Method: POST

Headers: X-CSRF-TOKEN (Required), X-Requested-With: XMLHttpRequest

Implementation Example (Alpine.js)

Copy this boilerplate into your Antlers template to get started:

{{-- Ensure Alpine.js is loaded: <script src="//[unpkg.com/alpinejs](https://unpkg.com/alpinejs)" defer></script> --}}

<div x-data="vatValidator()" class="my-custom-form-group">
    
    <!-- Input Group -->
    <div class="flex gap-2 mb-2">
        <select x-model="country" name="vat_country" class="border p-2 rounded">
            <option value="RO">RO</option>
            <option value="DE">DE</option>
            <option value="GB">GB</option>
        </select>
        
        <input type="text" x-model="number" name="vat_number" class="border p-2 rounded flex-1" placeholder="VAT Number">
        
        <button type="button" @click="validate" :disabled="loading" class="bg-blue-600 text-white px-4 rounded disabled:opacity-50">
            <span x-show="!loading">Validate</span>
            <span x-show="loading">Checking...</span>
        </button>
    </div>

    <!-- Error State -->
    <div x-show="error" class="text-red-600 text-sm mb-2" x-text="error"></div>

    <!-- Success State -->
    <template x-if="result">
        <div class="bg-green-50 p-3 rounded border border-green-200 text-sm">
            <div class="font-bold text-green-700 mb-1">✓ Valid Registration</div>
            
            <!-- Universal Data Fields -->
            <div>
                <span class="font-semibold">Name:</span> 
                <span x-text="result.name || result.date_generale?.denumire"></span>
            </div>
            <div>
                <span class="font-semibold">Address:</span> 
                <span x-text="result.address || result.date_generale?.adresa"></span>
            </div>

            <!-- Country-Specific Data (Example: Romania) -->
            <template x-if="country === 'RO' && result.date_generale">
                <div class="text-xs text-gray-500 mt-2 pt-2 border-t border-green-200">
                    Registered: <span x-text="result.date_generale.data_inregistrare"></span>
                </div>
            </template>
        </div>
    </template>
</div>

<script>
function vatValidator() {
    return {
        country: 'RO',
        number: '',
        loading: false,
        error: null,
        result: null,
        
        async validate() {
            this.loading = true;
            this.error = null;
            this.result = null;

            try {
                // 1. Get CSRF Token from Statamic Meta Tag or Input
                // Ensure your layout has <meta name="csrf-token" content="{{ csrf_token }}">
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                
                if (!token) {
                    this.error = "CSRF token missing. Please add the meta tag to your layout.";
                    this.loading = false;
                    return;
                }

                // 2. Perform Request
                const response = await fetch('/!/registration_number_validator/validate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ country_code: this.country, number: this.number })
                });

                const data = await response.json();

                // 3. Handle Response
                if (data.valid) {
                    this.result = data.data;
                } else {
                    this.error = data.error || 'Invalid number';
                }
            } catch (e) {
                console.error(e);
                this.error = 'Validation failed. Please check connection.';
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>