<script setup lang="ts">
import { index } from '@/routes/companies';
import { useI18n } from 'vue-i18n';
import { Head, Link } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';

defineProps<{
    company: {
        id: number;
        name: string;
        slug: string;
    };
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
    <Head :title="company.name" />
    <div class="flex flex-1 flex-col p-4">
        <Heading
            :title="company.name"
            :description="t('companies.overview_description')"
        />
        <div
            class="border-border bg-card text-card-foreground max-w-xl rounded-xl border p-6"
        >
            <dl class="space-y-4">
                <div>
                    <dt class="text-muted-foreground text-sm">
                        {{ t('companies.name') }}
                    </dt>
                    <dd class="mt-1 font-medium wrap-break-word">
                        {{ company.name }}
                    </dd>
                </div>

                <div>
                    <dt class="text-muted-foreground text-sm">
                        {{ t('companies.identifier') }}
                    </dt>
                    <dd class="mt-1 font-medium wrap-break-word">
                        {{ company.slug }}
                    </dd>
                </div>
            </dl>
        </div>

        <div class="mt-6">
            <Button variant="outline" as-child>
                <Link :href="index()">
                    {{ t('companies.back') }}
                </Link>
            </Button>
        </div>
    </div>
</template>
