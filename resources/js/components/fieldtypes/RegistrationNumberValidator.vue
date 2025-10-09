<template>
    <div>
        <VueCountryCode @onSelect="onSelect" />
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { VueCountryCode } from 'vue3-country-select'
import "vue3-country-select/style.css"

console.log('vue component');

const props = defineProps({
    value: String,
});

const emit = defineEmits(['input']);

function updateValue(newValue) {
    emit('input', newValue);
}

/** Country select */
const country = ref({});

const onSelect = (data) => {
  country.value = data;
}

</script>
