<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { ref } from 'vue';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Form } from '@inertiajs/vue3';
import { Spinner } from '@/components/ui/spinner';
import { destroy } from '@/routes/companies';

defineProps<{
    company: {
        name: string;
        slug: string;
    };
}>();

const { t } = useI18n();
const isOpen = ref(false);
</script>
<template>
    <Dialog v-model:open="isOpen">
        <DialogTrigger as-child>
            <Button variant="destructive">
                {{ t('companies.delete.title') }}
            </Button>
        </DialogTrigger>

        <DialogContent>
            <DialogHeader>
                <DialogTitle>
                    {{ t('companies.delete.title') }}
                </DialogTitle>
                <DialogDescription>
                    {{
                        t('companies.delete.description', {
                            name: company.name,
                        })
                    }}
                </DialogDescription>
            </DialogHeader>

            <Form
                v-bind="destroy.form(company.slug)"
                :options="{ preserveScroll: true }"
                @success="isOpen = false"
                v-slot="{ processing }"
            >
                <DialogFooter class="gap-2">
                    <DialogClose as-child>
                        <Button
                            type="button"
                            variant="secondary"
                            :disabled="processing"
                        >
                            {{ t('companies.delete.cancel') }}
                        </Button>
                    </DialogClose>

                    <Button
                        type="submit"
                        variant="destructive"
                        :disabled="processing"
                    >
                        <Spinner v-if="processing" />
                        {{ t('companies.delete.submit') }}
                    </Button>
                </DialogFooter>
            </Form>
        </DialogContent>
    </Dialog>
</template>
