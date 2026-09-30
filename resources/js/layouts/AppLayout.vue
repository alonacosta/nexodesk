<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { useI18n } from 'vue-i18n';
import { computed } from 'vue';

const { breadcrumbs = [] } = defineProps<{
    breadcrumbs?: BreadcrumbItem[];
}>();

const { t, te } = useI18n();

const translateBreadcrumbs = computed(() =>
    breadcrumbs.map((item) => ({
        ...item,
        title: te(item.title) ? t(item.title) : item.title,
    })),
);
</script>

<template>
    <AppLayout :breadcrumbs="translateBreadcrumbs">
        <slot />
    </AppLayout>
</template>
