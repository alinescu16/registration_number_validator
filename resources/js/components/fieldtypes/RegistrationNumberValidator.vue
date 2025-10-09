<template>
    <div>
        <VueCountryCode @onSelect="onSelect" searchPlaceholder="Search country..." />
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { VueCountryCode } from 'vue3-country-select'
import "vue3-country-select/style.css"

/** Field Config */
const props = defineProps({
    'id': String,
    'default': String,
    'handle': String,
    'instructions': String,
    'instructions_position': String,
    'placeholder': String,
    'label': String,
    'old': String,
    'value': String,
    'validate': Array,
    'show_validate_button': Boolean,
    'validate_button_text': String,
    'show_company_details_after_validation': String,
});

const emit = defineEmits(['input']);

function updateValue(newValue) {
    console.log(newValue);
    emit('input', newValue);
}

/** Country select */
const country = ref({});

const onSelect = (data) => {
  country.value = data;
}

</script>
