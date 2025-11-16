<script setup>
defineProps({
    id: String,
    modelValue: String,
    placeholder: String,
});

defineOptions({ inheritAttrs: false })

defineEmits(['update:modelValue']);
</script>

<template>
    <div class="relative">
        <input
            :id="id"
            type="text"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            :placeholder="placeholder"
            :class="$attrs.class"
        />
    </div>
</template>