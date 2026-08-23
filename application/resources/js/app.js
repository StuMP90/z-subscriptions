import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

const appName = import.meta.env.VITE_APP_NAME || 'Z-Subscriptions';

const originalFetch = window.fetch;

window.fetch = function (input, init = {}) {
    const url = typeof input === 'string' ? input : input.url;

    if (typeof url === 'string' && url.includes('api.zsubscriptions.local')) {
        const proxyUrl = new URL('/api/proxy', window.location.origin);
        const originalUrl = new URL(url);
        proxyUrl.searchParams.set('path', originalUrl.pathname.replace(/^\//, ''));
        proxyUrl.searchParams.set('method', (init.method || 'GET').toUpperCase());
        input = proxyUrl.toString();
        init.credentials = 'same-origin';
    }

    return originalFetch(input, init);
};

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);
    },
    progress: { color: '#4B5563' },
});
