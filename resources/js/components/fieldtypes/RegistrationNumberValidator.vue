<script setup>
import { ref, watch, computed, onMounted } from 'vue';
import { VueCountryCode } from 'vue3-country-select';
import "vue3-country-select/style.css";

// Import your custom components
import TextField from '../TextField.vue';
import Button from '../Button.vue';
import Status from '../Status.vue';

const europeanCountries = ref([
  'AL', 'AD', 'AM', 'AT', 'BY', 'BE', 'BA', 'BG', 'CH', 'CY', 'CZ', 'DE', 'DK', 
  'EE', 'ES', 'FO', 'FI', 'FR', 'GB', 'GE', 'GI', 'GR', 'HR', 'HU', 'IE', 'IS', 
  'IT', 'LI', 'LT', 'LU', 'LV', 'MC', 'MD', 'ME', 'MK', 'MT', 'NL', 'NO', 'PL', 
  'PT', 'RO', 'RS', 'RU', 'SE', 'SI', 'SK', 'SM', 'TR', 'UA', 'VA'
]);

// These props are passed from your Blade/Antlers tag
const props = defineProps({
    placeholder: String,
    old: Object, 
    show_validate_button: Boolean,
    validate_button_text: String,
    show_company_details_after_validation: Boolean,
    custom_prop: {type: String, default: ''},
    country_input_name: { type: String, default: 'country_code' },
    number_input_name: { type: String, default: 'number' },
    validation_url: { type: String, required: true },
    csrf_token: { type: String, required: true },
});

/** State */
const country = ref(null);
const number = ref('');
const isLoading = ref(false);
const validationResult = ref(null);
const validationError = ref(null);

onMounted(() => {
    if (props.old) {
        country.value = props.old.country || null;
        number.value = props.old.number || '';
    }
});

// Compute the initial country for the dropdown
const initialCountry = computed(() => country.value || 'RO');

const onCountrySelect = (data) => {
    country.value = data.iso2 || null; 
}

const validateButton = computed(() => {
    return {
        text: props.validate_button_text || 'Validate',
        click: validateNumber,
        disabled: !country.value || !number.value || isLoading.value,
    };
});

async function validateNumber() {
    isLoading.value = true;
    validationResult.value = null;
    validationError.value = null;

    if (!props.csrf_token) {
        validationError.value = "CSRF token not found. Please refresh the page.";
        isLoading.value = false;
        return;
    }

    try {
        const response = await fetch(props.validation_url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': props.csrf_token,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                country_code: country.value,
                number: number.value,
            })
        });

        const data = await response.json();

        if (!response.ok) {
            validationError.value = data.error || data.message || "An error occurred.";
            return;
        }
        
        if (data.valid) {
            validationResult.value = data.data;
        } else {
            validationError.value = data.error || "Invalid registration number.";
        }

    } catch (e) {
        validationError.value = e.message || "An unknown error occurred.";
    } finally {
        isLoading.value = false;
    }
}
</script>

<template>
    <div class="registration-validator-field">
        
        <!-- Hidden inputs to submit the values in the form -->
        <input type="hidden" :name="props.country_input_name" :value="country" />
        <input type="hidden" :name="props.number_input_name" :value="number" />

        <div class="flex items-start space-x-2">
            <!-- Country Selector -->
            <div class="w-max-content country-selector">
                <VueCountryCode 
                    @onSelect="onCountrySelect" 
                    searchPlaceholder="Search country..."
                    :defaultCountry="initialCountry"
                    :onlyCountries="europeanCountries"
                    :disabledFormatting="true"
                    :dropdownOptions="{
                        disabledDialCode: true,
                        showFlags: true,
                    }"/>
            </div>

            <!-- Use your custom 'TextField' component -->
            <div class="flex-1">
                <TextField
                    v-model="number"
                    @update:modelValue="number = $event"
                    :placeholder="props.placeholder"
                    class="w-full px-3 py-2 text-sm text-gray-700 border border-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-gray-500 focus:border-gray-500"
                />
            </div>

            <div class="flex-1">
                <Button 
                    v-if="props.show_validate_button" 
                    :buttons="[validateButton]"
                    class="px-4 py-2 text-sm font-semibold text-white bg-gray-600 rounded-md shadow-sm hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed focus:outline-none focus:ring-0 w-max-content"
                />
            </div>
        </div>

        <div v-if="isLoading" class="mt-2 text-sm text-gray-600">Validating...</div>
        
        <div v-if="props.show_company_details_after_validation && (validationResult || validationError)" class="validation-results mt-3 p-3 border rounded-md">
            <!-- Error Message -->
            <div v-if="validationError" class="flex items-center">
                <Status status="false" class="mr-2" />
                <span class="text-red-600">{{ validationError }}</span>
            </div>
            
            <!-- Success Message -->
            <div v-if="validationResult" class="space-y-1">
                <div class="flex items-center">
                    <Status status="true" class="mr-2" />
                    <strong class="text-green-600">Validation Successful</strong>
                </div>
                <div class="pl-6 text-sm">
                    <p><strong>Company:</strong> {{ validationResult.name }}</p>
                    <p><strong>Address:</strong> {{ validationResult.address }}</p>
                </div>
            </div>
        </div>
    </div>
</template>
<style>
/* Scoped styles for the country selector dropdown */
.registration-validator-field .country-selector .v3-country-code {
    border-radius: 0.375rem; /* rounded-md */
    border: 1px solid #d1d5db; /* border-gray-300 */
    padding: 0.5rem 0.75rem; /* py-2 px-3 */
    width: 100%;
}
.registration-validator-field .country-selector .v3-country-code:focus-within {
    border-color: #6d28d9; /* focus:border-purple-600 */
    box-shadow: 0 0 0 1px #6d28d9; /* focus:ring-1 focus:ring-purple-600 */
}
</style>