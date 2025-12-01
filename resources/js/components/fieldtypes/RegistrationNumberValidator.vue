<script setup>
import { ref, watch, computed, onMounted } from 'vue';
import { VueCountryCode } from 'vue3-country-select';
import "vue3-country-select/style.css";

// Import your custom components
import TextField from '../TextField.vue';
import Button from '../Button.vue';

const europeanCountries = ref([
  'AL', 'AD', 'AM', 'AT', 'BY', 'BE', 'BA', 'BG', 'CH', 'CY', 'CZ', 'DE', 'DK', 
  'EE', 'ES', 'FO', 'FI', 'FR', 'GB', 'GE', 'GI', 'GR', 'HR', 'HU', 'IE', 'IS', 
  'IT', 'LI', 'LT', 'LU', 'LV', 'MC', 'MD', 'ME', 'MK', 'MT', 'NL', 'NO', 'PL', 
  'PT', 'RO', 'RS', 'RU', 'SE', 'SI', 'SK', 'SM', 'TR', 'UA', 'VA'
]);

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
const validationStatus = ref('idle');
const validationResult = ref(null);
const validationError = ref(null);

onMounted(() => {
    if (props.old) {
        country.value = props.old.country || null;
        number.value = props.old.number || '';
    }
});

const initialCountry = computed(() => country.value || 'RO');

const onCountrySelect = (data) => {
    country.value = data.iso2 || null; 
    
    if (validationStatus.value !== 'validating') {
        validationStatus.value = 'idle';
    }
}

watch(number, () => {
    if (validationStatus.value !== 'validating') {
        validationStatus.value = 'idle';
    }
});

const validateButton = computed(() => {
    return {
        text: props.validate_button_text || 'Validate',
        click: validateNumber,
        disabled: !country.value || !number.value || isLoading.value,
    };
});

async function validateNumber() {
    isLoading.value = true;
    validationStatus.value = 'validating';
    validationResult.value = null;
    validationError.value = null;

    if (!props.csrf_token) {
        validationError.value = "CSRF token not found. Please refresh the page.";
        validationStatus.value = 'error';
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
            validationStatus.value = 'error';
            return;
        }
        
        if (data.valid) {
            validationResult.value = data.data; // Assign the inner data object directly
            validationStatus.value = 'success';
        } else {
            validationError.value = data.error || "Invalid registration number.";
            validationStatus.value = 'error';
        }
    } catch (e) {
        validationError.value = e.message || "An unknown error occurred.";
        validationStatus.value = 'error';
    } finally {
        isLoading.value = false;
    }
}
</script>

<template>
    <div class="registration-validator-field">
        
        <input type="hidden" :name="props.country_input_name" :value="country" />
        <input type="hidden" :name="props.number_input_name" :value="number" />

        <div class="flex items-center space-x-2">
            <!-- Country Selector -->
            <div class="max-w-max country-selector">
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

            <!-- TextField -->
            <div class="flex-1 max-w-60">
                <TextField
                    v-model="number"
                    @update:modelValue="number = $event"
                    :placeholder="props.placeholder"
                    class="w-full px-3 py-2 text-sm text-gray-700 border border-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-gray-500 focus:border-gray-500"
                />
            </div>

            <!-- Validate Button -->
            <div class="flex-1 max-w-max">
                <Button 
                    v-if="props.show_validate_button" 
                    :buttons="[validateButton]"
                    class="px-4 py-2 text-sm font-semibold text-white bg-gray-600 rounded-md shadow-sm hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed focus:outline-none focus:ring-0 w-max-content"
                />
            </div>

            <div v-if="validationStatus !== 'idle'" class="flex-1 items-center">
            
                <!-- Loading State: Simple Tailwind Spinner -->
                <div v-if="validationStatus === 'validating'" class="flex items-center text-gray-500">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-sm">Validating...</span>
                </div>

                <!-- Success State: Green Checkmark with Scale-in Animation -->
                <div v-if="validationStatus === 'success'" class="flex items-center text-green-600 animate-scale-in">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-sm font-medium">Valid</span>
                </div>

                <!-- Error State: Red X with Shake Animation -->
                <div v-if="validationStatus === 'error'" class="flex items-center text-red-600 animate-shake">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <!-- Error text is shown in the details block below, but we can show a quick label here -->
                    <span class="text-sm font-medium">Error</span>
                </div>
            </div>
        </div>


        <!-- DETAILS / TEXT OUTPUT AREA -->
        <div v-if="props.show_company_details_after_validation && (validationStatus === 'success' || validationStatus === 'error')" 
             class="validation-results mt-3 p-3 border rounded-md transition-all duration-300">
            
            <!-- Error Text -->
            <div v-if="validationStatus === 'error'" class="flex items-center text-red-600">
                <span class="text-sm font-medium">{{ validationError }}</span>
            </div>
            
            <!-- Success Text -->
            <div v-if="validationStatus === 'success'" class="space-y-1">
                <div class="pl-2 text-sm text-gray-800" v-if="validationResult.country_code === 'RO'">
                    <p><strong>Company:</strong> {{ validationResult.date_generale?.denumire }}</p>
                    <p><strong>Address:</strong> {{ validationResult.date_generale?.adresa }}</p>
                    <p><strong>Registered:</strong> {{ validationResult.date_generale?.data_inregistrare }}</p>
                    <p class="mt-2 text-xs text-gray-500">
                        {{ validationResult.stare_inregistrare }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
/* Scoped styles for the country selector dropdown */
.registration-validator-field .country-selector .v3-country-code {
    border-radius: 0.375rem; 
    border: 1px solid #d1d5db; 
    padding: 0.5rem 0.75rem; 
    width: 100%;
}
.registration-validator-field .country-selector .v3-country-code:focus-within {
    border-color: #6d28d9; 
    box-shadow: 0 0 0 1px #6d28d9; 
}
/* Simple keyframe animations for the icons */
@keyframes scaleIn {
  0% { transform: scale(0); opacity: 0; }
  100% { transform: scale(1); opacity: 1; }
}
.animate-scale-in {
  animation: scaleIn 0.3s ease-out forwards;
}

@keyframes shake {
  0%, 100% { transform: translateX(0); }
  25% { transform: translateX(-2px); }
  75% { transform: translateX(2px); }
}
.animate-shake {
    animation: shake 0.3s ease-in-out;
}
</style>