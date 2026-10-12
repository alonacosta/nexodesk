<script setup lang="ts">
import { index } from '@/routes/companies';
import { useI18n } from 'vue-i18n';
import { Head } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';

type CompanyListItem = {
    id: number;
    name: string;
    slug: string;
    role: 'owner' | 'admin' | 'member';
};

defineProps<{
    companies: CompanyListItem[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'navigation.companies',
                href: index(),
            },
        ],
    },
});

const { t } = useI18n();
</script>

<template>
    <Head :title="t('companies.title')" />

    <div class="flex flex-1 flex-col p-4">
        <Heading
            :title="t('companies.title')"
            :description="t('companies.description')"
        />

        <div
            v-if="companies.length"
            class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3"
        >
            <article
                v-for="company in companies"
                :key="company.id"
                class="border-border bg-card text-card-foreground rounded-xl border p-5"
            >
                <div class="flex items-start justify-between gap-3">
                    <h2 class="font-semicolon min-w-0 text-lg wrap-break-word">
                        {{ company.name }}
                    </h2>

                    <span
                        class="bg-muted text-muted-foreground shrink-0 rounded-md px-2 py-1 text-xs font-medium"
                    >
                        {{ t(`companies.roles.${company.role}`) }}
                    </span>
                </div>

                <p class="text-muted-foreground mt-2 text-sm break-all">
                    {{ company.slug }}
                </p>
            </article>
        </div>

        <div
            v-else
            class="border-border roundex-xl border border-dashed p-8 text-center"
        >
            <p class="text-muted-foreground text-sm">
                {{ t('companies.empty') }}
            </p>
        </div>
    </div>
</template>
