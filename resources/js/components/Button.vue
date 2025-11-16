<script setup>
defineProps({
    buttons: {
        type: Array,
        required: true,
    },
});

defineOptions({ inheritAttrs: false })

</script>

<template>
    <div class="flex items-center space-x-2">
        <button
            v-for="(button, index) in buttons"
            :key="index"
            type="button"
            @click="button.click"
            :disabled="button.disabled"
            :class="$attrs.class"
        >
            <slot :button="button">
                {{ button.text }}
            </slot>
        </button>
    </div>
</template>