<script setup lang="ts">
import { useForm, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { Locale } from '@/types';
import { update } from '@/routes/locale';

const page = usePage();
const { t } = useI18n();

const form = useForm<{ locale: Locale }>({
    locale: page.props.locale,
});

const languages: { value: Locale; label: string; name: string }[] = [
    { value: 'en', label: 'EN', name: 'English' },
    { value: 'pt-PT', label: 'PT', name: 'Português (Portugal)' },
];

function changeLocale(locale: Locale): void {
    if (form.processing || locale === page.props.locale) {
        return;
    }

    form.locale = locale;

    form.patch(update().url, {
        preserveScroll: true,
    });
}
</script>

<template>
    <div>
        <div
            role="group"
            :aria-label="t('language.label')"
            class="border-border bg-card inline-flex shrink-0 gap-1 rounded-lg border p-0.5"
        >
            <button
                v-for="language in languages"
                :key="language.value"
                :aria-label="language.name"
                :title="language.name"
                type="button"
                :lang="language.value"
                :aria-pressed="page.props.locale === language.value"
                :disabled="form.processing"
                class="focus-visible:outline-ring inline-flex h-8 w-9 shrink-0 items-center justify-center rounded-md text-xs font-medium transition-colors focus-visible:outline-2 focus-visible:outline-offset-2"
                :class="
                    page.props.locale === language.value
                        ? 'bg-primary text-primary-foreground'
                        : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground'
                "
                @click="changeLocale(language.value)"
            >
                {{ language.label }}
            </button>
        </div>

        <p
            v-if="form.errors.locale"
            role="alert"
            class="text-destructive mt-2 text-sm"
        >
            {{ form.errors.locale }}
        </p>
    </div>
</template>
