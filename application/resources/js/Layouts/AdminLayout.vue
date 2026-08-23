<script setup>
import { router, usePage, Link } from '@inertiajs/vue3';

const page = usePage();

const logout = () => {
    router.post('/logout');
};

const nav = [
    { label: 'Dashboard', href: '/' },
    { label: 'Users', href: '/users' },
    { label: 'Customers', href: '/customers' },
    { label: 'Orders', href: '/orders' },
    { label: 'Shops', href: '/shops' },
    { label: 'Products', href: '/products' },
    { label: 'Offers', href: '/offers' },
    { label: 'Publications', href: '/publications' },
    { label: 'Issues', href: '/issues' },
];
</script>

<template>
    <div class="min-h-screen flex">
        <aside class="w-64 bg-gray-800 text-white p-4 flex flex-col">
            <div class="text-xl font-bold mb-6">Admin</div>
            <nav class="space-y-1 flex-1">
                <Link
                    v-for="item in nav"
                    :key="item.href"
                    :href="item.href"
                    class="block p-2 rounded hover:bg-gray-700"
                >
                    {{ item.label }}
                </Link>
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
