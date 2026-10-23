<script setup lang="ts">
import { PaginatedData } from '@/types/pagination';
import { index } from '@/routes/companies';
import { index as membersIndex } from '@/routes/companies/members';
import { useI18n } from 'vue-i18n';
import { computed, ref, watch } from 'vue';
import { dataTableFeatures } from '@/components/data-table/features';
import { ColumnDef } from '@tanstack/vue-table';
import { Head, Link, router } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import DataTable from '@/components/data-table/DataTable.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';

type CompanyMember = {
    id: number;
    name: string;
    email: string;
    role: 'owner' | 'admin' | 'member';
};

const props = defineProps<{
    company: {
        id: number;
        name: string;
        slug: string;
    };
    members: PaginatedData<CompanyMember>;
    filters: {
        search: string;
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

const search = ref(props.filters.search);

watch(
    () => props.filters.search,
    (value) => {
        search.value = value;
    },
);

function applySearch(): void {
    const value = search.value.trim();

    router.get(
        membersIndex.url(props.company.slug),
        value ? { search: value } : {},
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
}

const columns = computed<ColumnDef<typeof dataTableFeatures, CompanyMember>[]>(
    () => [
        {
            accessorKey: 'name',
            header: t('auth.name'),
        },
        {
            accessorKey: 'email',
            header: t('auth.email'),
        },
        {
            accessorKey: 'role',
            header: t('companies.members.role'),
            cell: ({ row }) => t(`companies.roles.${row.original.role}`),
        },
    ],
);
</script>

<template>
    <Head :title="t('companies.members.title')" />

    <div class="flex min-w-0 flex-1 flex-col gap-6 p-4">
        <Heading
            :title="t('companies.members.title')"
            :description="
                t('companies.members.description', { company: company.name })
            "
        />

        <form
            role="search"
            class="flex flex-wrap items-end gap-3"
            @submit.prevent="applySearch"
        >
            <div class="grid w-full gap-2 sm:max-w-sm">
                <Label for="member-search">{{
                    t('companies.members.search_label')
                }}</Label>
                <Input
                    id="member-search"
                    v-model="search"
                    type="search"
                    name="search"
                    maxlength="255"
                    :placeholder="t('companies.members.search_placeholder')"
                />
            </div>

            <Button type="submit">
                {{ t('companies.members.search_submit') }}
            </Button>
        </form>

        <DataTable
            :columns="columns"
            :data="members.data"
            :empty-message="t('companies.members.empty')"
        />

        <div class="flex flex-wrap items-center justify-between gap-4">
            <p class="text-muted-foreground text-sm">
                {{
                    t('pagination.page', {
                        current: members.current_page,
                        last: members.last_page,
                    })
                }}
            </p>

            <nav
                :aria-label="t('pagination.label')"
                class="flex items-center gap-2"
            >
                <Button
                    v-if="members.prev_page_url"
                    variant="outline"
                    as-child
                    size="sm"
                >
                    <Link :href="members.prev_page_url" preserve-scroll>
                        {{ t('pagination.previous') }}
                    </Link>
                </Button>

                <Button v-else variant="outline" size="sm" disabled>
                    {{ t('pagination.previous') }}
                </Button>

                <Button
                    v-if="members.next_page_url"
                    variant="outline"
                    size="sm"
                    as-child
                >
                    <Link :href="members.next_page_url" preserve-scroll>
                        {{ t('pagination.next') }}
                    </Link>
                </Button>

                <Button v-else variant="outline" size="sm" disabled>
                    {{ t('pagination.next') }}
                </Button>
            </nav>
        </div>

        <div>
            <Button variant="outline" as-child>
                <Link :href="index()">
                    {{ t('companies.members.back') }}
                </Link>
            </Button>
        </div>
    </div>
</template>
