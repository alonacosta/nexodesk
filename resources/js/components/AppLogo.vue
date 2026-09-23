<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';

const name = usePage().props.name;
</script>

<template>
    <div
        class="bg-primary text-primary-foreground flex size-8 shrink-0 items-center justify-center rounded-md"
    >
        <AppLogoIcon class="size-5" />
    </div>
    <div class="ml-1 flex h-8 min-w-0 flex-1 items-center text-left text-sm">
        <span class="mb-0.5 truncate leading-none font-semibold">{{
            name
        }}</span>
    </div>
</template>
