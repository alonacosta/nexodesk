<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { home } from '@/routes';
import LanguageSwitcher from '@/components/LanguageSwitcher.vue';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="bg-background text-foreground flex min-h-svh flex-col">
        <div class="flex justify-end p-4 sm:p-6">
            <LanguageSwitcher />
        </div>
        <main class="flex flex-1 items-center justify-center px-6 py-10">
            <div class="w-full max-w-sm">
                <div class="flex flex-col gap-8">
                    <div class="flex flex-col items-center gap-4">
                        <Link
                            :href="home()"
                            class="text-primary inline-flex items-center gap-2 text-xl font-bold tracking-tight"
                        >
                            <span
                                class="bg-primary text-primary-foreground flex size-9 shrink-0 items-center justify-center rounded-lg"
                            >
                                <AppLogoIcon class="size-6" />
                            </span>
                            <span>NexoDesk</span>
                        </Link>
                        <div class="space-y-2 text-center">
                            <h1 class="text-xl font-medium">{{ title }}</h1>
                            <p
                                class="text-muted-foreground text-center text-sm"
                            >
                                {{ description }}
                            </p>
                        </div>
                    </div>
                    <slot />
                </div>
            </div>
        </main>
    </div>
</template>
