<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { dashboard, home, login } from '@/routes';
import { register } from '@/routes';
import { useI18n } from 'vue-i18n';
import LanguageSwitcher from '@/components/LanguageSwitcher.vue';

const { t } = useI18n();

const features = ['companies', 'customers', 'tickets'] as const;
</script>

<template>
    <Head :title="t('welcome.page_title')">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div
        class="bg-background text-foreground flex min-h-screen flex-col items-center p-6 lg:p-8"
    >
        <header class="mb-10 w-full max-w-6xl">
            <nav
                :aria-label="t('navigation.main')"
                class="flex flex-wrap items-center justify-between gap-4"
            >
                <Link
                    :href="home()"
                    class="text-primary text-2xl font-bold tracking-tight"
                >
                    NexoDesk
                </Link>

                <div class="ml-auto flex items-center gap-3">
                    <LanguageSwitcher />

                    <div class="hidden items-center gap-3 md:flex">
                        <Link
                            v-if="$page.props.auth.user"
                            :href="dashboard()"
                            class="bg-primary text-primary-foreground hover:bg-primary/90 rounded-lg px-4 py-2 font-medium transition-colors"
                        >
                            {{ t('navigation.dashboard') }}
                        </Link>

                        <template v-else>
                            <Link
                                :href="login()"
                                class="text-foreground hover:bg-accent hover:text-accent-foreground inline-flex h-9 w-28 shrink-0 items-center justify-center rounded-lg text-sm font-medium whitespace-nowrap transition-colors"
                            >
                                {{ t('navigation.login') }}
                            </Link>
                            <Link
                                :href="register()"
                                class="bg-primary text-primary-foreground hover:bg-primary/90 inline-flex h-9 w-34 shrink-0 items-center justify-center rounded-lg text-sm font-medium whitespace-nowrap transition-colors"
                            >
                                {{ t('navigation.register') }}
                            </Link>
                        </template>
                    </div>
                </div>
            </nav>
        </header>

        <main
            class="flex w-full max-w-6xl flex-col gap-12 py-8 sm:py-16 lg:py-24"
        >
            <section class="max-w-3xl">
                <p
                    class="text-primary mb-6 text-sm font-semibold tracking-wide"
                >
                    {{ t('welcome.eyebrow') }}
                </p>

                <h1
                    class="text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl"
                >
                    {{ t('welcome.title') }}
                </h1>

                <p
                    class="text-muted-foreground mt-6 max-w-2xl text-lg leading-relaxed"
                >
                    {{ t('welcome.description') }}
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="dashboard()"
                        class="bg-primary text-primary-foreground hover:bg-primary/90 rounded-lg px-6 py-3 font-medium transition-colors"
                    >
                        {{ t('navigation.dashboard') }}
                    </Link>

                    <template v-else>
                        <Link
                            :href="register()"
                            class="bg-primary text-primary-foreground hover:bg-primary/90 rounded-lg px-6 py-3 font-medium transition-colors"
                        >
                            {{ t('navigation.register') }}
                        </Link>

                        <Link
                            :href="login()"
                            class="border-border hover:bg-accent hover:text-accent-foreground rounded-lg border px-6 py-3 font-medium transition-colors"
                        >
                            {{ t('navigation.login') }}
                        </Link>
                    </template>
                </div>

                <p class="text-muted-foreground mt-6 text-sm">
                    {{ t('welcome.development_notice') }}
                </p>
            </section>

            <section aria-labelledby="features-heading">
                <h2
                    id="features-heading"
                    class="text-2xl font-semibold tracking-tight"
                >
                    {{ t('welcome.features_title') }}
                </h2>

                <div class="grid-col-1 mt-6 grid gap-4 md:grid-cols-3">
                    <article
                        v-for="feature in features"
                        :key="feature"
                        class="border-border bg-card text-card-foreground rounded-xl border p-6"
                    >
                        <h3 class="text-lg font-semibold">
                            {{ t(`welcome.features.${feature}.title`) }}
                        </h3>

                        <p
                            class="text-muted-foreground mt-3 text-sm leading-relaxed"
                        >
                            {{ t(`welcome.features.${feature}.description`) }}
                        </p>
                    </article>
                </div>
            </section>
        </main>
    </div>
</template>
