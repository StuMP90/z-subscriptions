<script setup>
import { router, usePage, Link } from '@inertiajs/vue3';

const page = usePage();

const logout = () => {
    router.post('/logout');
};

const menu = [
    { label: 'Dashboard', href: '/' },
    {
        label: 'Processing',
        items: [
            { label: 'Customers', href: '/customers' },
            { label: 'Orders', href: '/orders' },
        ],
    },
    {
        label: 'Maintenance',
        items: [
            { label: 'Users', href: '/users' },
            { label: 'Products', href: '/products' },
            { label: 'Product Offers', href: '/offers' },
            { label: 'Publications', href: '/publications' },
            { label: 'Issues', href: '/issues' },
        ],
    },
    {
        label: 'System',
        items: [
            { label: 'Settings', href: '/settings' },
            { label: 'Cache Control', href: '/cache-control' },
            { label: 'Shops', href: '/shops' },
            { label: 'Regions', href: '/global-regions' },
            { label: 'Countries', href: '/countries' },
            { label: 'Counties / States', href: '/county-states' },
        ],
    },
];
</script>

<template>
    <div class="min-h-screen flex">
        <aside class="w-64 bg-gray-800 text-white p-4 flex flex-col">
            <div class="text-xl font-bold mb-6">Admin</div>
            <nav class="space-y-3 flex-1">
                <template v-for="item in menu" :key="item.label">
                    <Link
                        v-if="item.href"
                        :href="item.href"
                        class="block p-2 rounded hover:bg-gray-700"
                    >
                        {{ item.label }}
                    </Link>
                    <div v-else>
                        <div class="px-2 py-1 text-xs uppercase text-gray-400">{{ item.label }}</div>
                        <Link
                            v-for="sub in item.items"
                            :key="sub.href"
                            :href="sub.href"
                            class="block p-2 pl-4 rounded hover:bg-gray-700"
                        >
                            {{ sub.label }}
                        </Link>
                    </div>
                </template>
            </nav>
        </aside>
        <div class="flex-1 flex flex-col">
            <header class="bg-white shadow p-4 flex justify-between items-center">
                <span class="font-medium">{{ page.props.auth.user?.email }}</span>
                <button @click="logout" class="text-red-600 hover:underline">Logout</button>
            </header>
            <main class="p-6 bg-gray-50 flex-1">
                <slot />
            </main>
        </div>
    </div>
</template>
