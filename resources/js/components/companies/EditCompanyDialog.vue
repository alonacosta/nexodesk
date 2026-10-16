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
import { update } from '@/routes/companies';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import InputError from '@/components/InputError.vue';
import { Spinner } from '@/components/ui/spinner';

defineProps<{
    company: {
        id: number;
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
            <Button variant="outline">
                {{ t('companies.edit.title') }}
            </Button>
        </DialogTrigger>

        <DialogContent>
            <DialogHeader>
                <DialogTitle>
                    {{ t('companies.edit.title') }}
                </DialogTitle>
                <DialogDescription>
                    {{ t('companies.edit.description') }}
                </DialogDescription>
            </DialogHeader>

            <Form
                v-bind="update.form(company.slug)"
                :options="{ preserveScroll: true }"
                @success="isOpen = false"
                v-slot="{ errors, processing }"
                class="space-y-6"
            >
                <div class="grid gap-2">
                    <Label for="company-name">
                        {{ t('companies.name') }}
                    </Label>

                    <Input
                        id="edit-company-name"
                        name="name"
                        type="text"
                        :default-value="company.name"
                        required
                        maxlength="255"
                        autocomplete="organization"
                        :aria-invalid="!!errors.name"
                        :aria-describedby="
                            errors.name ? 'edit-company-name-error' : undefined
                        "
                    />

                    <InputError
                        id="edit-company-name-error"
                        :message="errors.name"
                    />
                </div>

                <DialogFooter class="gap-2">
                    <DialogClose as-child>
                        <Button
                            type="button"
                            variant="secondary"
                            :disabled="processing"
                        >
                            {{ t('companies.edit.cancel') }}
                        </Button>
                    </DialogClose>

                    <Button type="submit" :disabled="processing">
                        <Spinner v-if="processing" />
                        {{ t('companies.edit.submit') }}
                    </Button>
                </DialogFooter>
            </Form>
        </DialogContent>
    </Dialog>
</template>
