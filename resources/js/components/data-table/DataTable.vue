<script setup lang="ts" generic="TData extends { id: number | string }">
import { ColumnDef, FlexRender } from '@tanstack/vue-table';
import { useTable } from '@tanstack/vue-table';
import { computed } from 'vue';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { dataTableFeatures } from '@/components/data-table/features';

const props = defineProps<{
    columns: ColumnDef<typeof dataTableFeatures, TData>[];
    data: TData[];
    emptyMessage: string;
}>();

const table = useTable<typeof dataTableFeatures, TData>({
    features: dataTableFeatures,
    data: computed(() => props.data),
    columns: computed(() => props.columns),
    getRowId: (row) => String(row.id),
});
</script>

<template>
    <div class="border-border overflow-hidden rounded-lg border">
        <Table>
            <TableHeader>
                <TableRow
                    v-for="headerGroup in table.getHeaderGroups()"
                    :key="headerGroup.id"
                >
                    <TableHead
                        v-for="header in headerGroup.headers"
                        :key="header.id"
                        :colspan="header.colSpan"
                    >
                        <FlexRender
                            v-if="!header.isPlaceholder"
                            :header="header"
                        />
                    </TableHead>
                </TableRow>
            </TableHeader>

            <TableBody>
                <template v-if="table.getRowModel().rows.length">
                    <TableRow
                        v-for="row in table.getRowModel().rows"
                        :key="row.id"
                    >
                        <TableCell
                            v-for="cell in row.getAllCells()"
                            :key="cell.id"
                        >
                            <FlexRender :cell="cell" />
                        </TableCell>
                    </TableRow>
                </template>

                <TableRow v-else>
                    <TableCell
                        :colspan="columns.length"
                        class="text-muted-foreground h-24 text-center"
                    >
                        {{ emptyMessage }}
                    </TableCell>
                </TableRow>
            </TableBody>
        </Table>
    </div>
</template>
