<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { accept } from '@/routes/invitations';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Building2, Loader2 } from '@lucide/vue';
import { Separator } from '@/components/ui/separator';

const props = defineProps<{
    companyName: string;
    token: string;
}>();

const { t } = useI18n();
const page = usePage();

const form = useForm({});

const acceptInvitation = () => {
    form.post(accept(props.token).url);
};
</script>

<template>
    <Head :title="t('invitations.show.title')" />

    <div class="flex flex-1 items-center justify-center p-6">
        <Card class="w-full max-w-sm shadow-sm">
            <CardContent class="space-y-6 p-6">
                <!-- Company icon -->
                <div class="flex justify-center">
                    <div
                        class="bg-primary/10 text-primary flex size-14 items-center justify-center rounded-2xl"
                    >
                        <Building2 class="size-8" />
                    </div>
                </div>

                <!-- Invitation heading -->
                <div class="space-y-4 text-center">
                    <h1 class="text-xl font-semibold">
                        {{ t('invitations.show.title') }}
                    </h1>

                    <p class="text-muted-foreground text-sm">
                        {{ t('invitations.show.description') }}
                    </p>

                    <h2 class="text-xl font-semibold">{{ companyName }}</h2>
                </div>

                <Separator />

                <!-- Invitation information -->
                <div class="space-y-4">
                    <p
                        class="text-muted-foreground text-center text-sm leading-relaxed"
                    >
                        {{ t('invitations.show.instructions') }}
                    </p>

                    <form @submit.prevent="acceptInvitation">
                        <Button
                            type="submit"
                            class="h-12 w-full text-base"
                            :disabled="form.processing"
                        >
                            <Loader2
                                v-if="form.processing"
                                class="mr-2 size-4 animate-spin"
                            />
                            {{ t('invitations.show.accept') }}
                        </Button>
                    </form>

                    <p class="text-muted-foreground text-center text-sm">
                        {{
                            t('invitations.show.signed_in_as', {
                                name: page.props.auth.user.name,
                            })
                        }}
                    </p>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
