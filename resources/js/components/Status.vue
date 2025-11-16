<script setup>
import { computed } from 'vue';

const props = defineProps({
    status: {
        type: [String, Boolean],
        required: true,
    },
});

const isSuccess = computed(() => {
    return props.status === 'true' || props.status === true;
});
</script>

<template>
    <span
        class="flex-shrink-0 w-3 h-3 rounded-full"
        :class="{
            'bg-green-500': isSuccess,
            'bg-red-500': !isSuccess,
        }"
        aria-hidden="true"
    ></span>
</template>