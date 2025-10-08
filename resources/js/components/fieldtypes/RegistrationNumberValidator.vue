<template>
    <div>
        <text-input :value="value" @input="updateValue" /> <a href="#">Validate</a>
    </div>
</template>

<script setup>

const props = defineProps({
    value: String,
});

const emit = defineEmits(['input']);

function updateValue(newValue) {
    emit('input', newValue);
}
</script>
