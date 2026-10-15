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
import { store } from '@/routes/companies';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import InputError from '@/components/InputError.vue';
import { Spinner } from '@/components/ui/spinner';

const { t } = useI18n();
const isOpen = ref(false);
</script>

<template>
    <Dialog v-model:open="isOpen">
        <DialogTrigger as-child>
            <Button>
                {{ t('companies.create.title') }}
            </Button>
        </DialogTrigger>

        <DialogContent>
            <DialogHeader>
                <DialogTitle>
                    {{ t('companies.create.title') }}
                </DialogTitle>
                <DialogDescription>
                    {{ t('companies.create.description') }}
                </DialogDescription>
            </DialogHeader>

            <Form
                v-bind="store.form()"
                :options="{ preserveScroll: true }"
                reset-on-success
                @success="isOpen = false"
                v-slot="{ errors, processing }"
                class="space-y-6"
            >
                <div class="grid gap-4">
                    <Label for="company-name">
                        {{ t('companies.name') }}
                    </Label>
                    <Input
                        id="company-name"
                        name="name"
                        type="text"
                        required
                        maxlength="255"
                        autocomplete="organization"
                        :placeholder="t('companies.create.name_placeholder')"
                        :aria-invalid="!!errors.name"
                        :aria-describedby="
                            errors.name ? 'company-name-error' : undefined
                        "
                    />

                    <InputError
                        id="company-name-error"
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
                            {{ t('companies.create.cancel') }}
                        </Button>
                    </DialogClose>

                    <Button type="submit" :disabled="processing">
                        <Spinner v-if="processing" />
                        {{ t('companies.create.submit') }}
                    </Button>
                </DialogFooter>
            </Form>
        </DialogContent>
    </Dialog>
</template>
