import { createI18n } from 'vue-i18n';
import en from '@/locales/en.json';
import ptPT from '@/locales/pt-PT.json';

export function createAppI18n() {
    return createI18n({
        legacy: false,
        locale: 'en',
        fallbackLocale: 'en',
        messages: {
            en,
            'pt-PT': ptPT,
        },
    });
}
