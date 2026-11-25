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
import { store } from '@/routes/companies/invitations';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Spinner } from '@/components/ui/spinner';

defineProps<{ companySlug: string }>();

const { t } = useI18n();
const isOpen = ref(false);
</script>

<template>
    <Dialog v-model:open="isOpen">
        <DialogTrigger as-child>
            <Button>
                {{ t('invitations.create.title') }}
            </Button>
        </DialogTrigger>

        <DialogContent>
            <DialogHeader>
                <DialogTitle>
                    {{ t('invitations.create.title') }}
                </DialogTitle>
                <DialogDescription>{{
                    t('invitations.create.description')
                }}</DialogDescription>
            </DialogHeader>

            <Form
                v-bind="store.form(companySlug)"
                v-slot="{ errors, processing }"
                class="space-y-6"
                reset-on-success
                @success="isOpen = false"
            >
                <div class="grid gap-2">
                    <Label for="invite-email">{{ t('auth.email') }}</Label>
                    <Input
                        id="invite-email"
                        type="email"
                        name="email"
                        :placeholder="t('auth.email_placeholder')"
                        maxlength="255"
                        required
                    />
                </div>

                <DialogFooter>
                    <DialogClose as-child>
                        <Button
                            type="button"
                            variant="secondary"
                            :disabled="processing"
                        >
                            {{ t('invitations.create.cancel') }}
                        </Button>
                    </DialogClose>
                    <Button type="submit" :disabled="processing">
                        <Spinner v-if="processing" />
                        {{ t('invitations.create.submit') }}
                    </Button>
                </DialogFooter>
            </Form>
        </DialogContent>
    </Dialog>
</template>
