<script setup lang="ts">
import { onMounted, ref } from 'vue';

const model = defineModel<string>({ required: true });

const input = ref<HTMLInputElement | null>(null);

onMounted(() => {
    if (input.value?.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value?.focus() });

defineProps<{
    gray?: boolean;
}>();

</script>

<template>
    <input
        class="rounded-md shadow-sm border-gray-300 transition-colors duration-300 ease-in"
        :class="gray
            ? 'focus:border-gray-500 focus:ring-0'
            : 'focus:border-indigo-500 focus:ring-indigo-500'"
        v-model="model"
        ref="input"
    />
</template>
