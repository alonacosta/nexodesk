<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import AuthLayout from '@/layouts/auth/AuthSimpleLayout.vue';

const { title = '', description = '' } = defineProps<{
    title?: string;
    description?: string;
}>();

const { t, te } = useI18n();
</script>

<template>
    <AuthLayout
        :title="te(title) ? t(title) : title"
        :description="te(description) ? t(description) : description"
    >
        <slot />
    </AuthLayout>
</template>
