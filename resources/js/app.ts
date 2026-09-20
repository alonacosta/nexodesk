import { createInertiaApp, usePage } from '@inertiajs/vue3';
import { initializeTheme } from '@/composables/useAppearance';
import AppLayout from '@/layouts/AppLayout.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { initializeFlashToast } from '@/lib/flashToast';
import { createAppI18n } from '@/lib/i18n';
import { Locale } from '@/types';
import { watch } from 'vue';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

void createInertiaApp({
    withApp(app, { page }) {
        const i18n = createAppI18n(page.props.locale);
        app.use(i18n);

        if (typeof document !== 'undefined') {
            const inertiaPage = usePage();
            const syncLocale = (locale: Locale) => {
                i18n.global.locale.value = locale;
                document.documentElement.lang = locale;
            };

            syncLocale(page.props.locale);

            const stopWatching = watch(
                () => inertiaPage.props?.locale,
                (locale) => {
                    if (locale) {
                        syncLocale(locale);
                    }
                },
                { immediate: true },
            );

            app.onUnmount(stopWatching);
        }
    },
    title: (title) => (title ? `${title} - ${appName}` : appName),
    layout: (name) => {
        switch (true) {
            case name === 'Welcome':
                return null;
            case name.startsWith('auth/'):
                return AuthLayout;
            case name.startsWith('settings/'):
                return [AppLayout, SettingsLayout];
            default:
                return AppLayout;
        }
    },
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on page load...
initializeTheme();

// This will listen for flash toast data from the server...
initializeFlashToast();
