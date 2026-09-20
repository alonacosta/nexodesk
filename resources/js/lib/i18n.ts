import { createI18n } from 'vue-i18n';
import en from '@/locales/en.json';
import ptPT from '@/locales/pt-PT.json';
import type { Locale } from '@/types/locale';

export function createAppI18n(locale: Locale) {
    return createI18n({
        legacy: false,
        locale,
        fallbackLocale: 'en',
        messages: {
            en,
            'pt-PT': ptPT,
        },
    });
}
