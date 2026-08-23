<script setup>
import { Link } from '@inertiajs/vue3';
import AdminLayout from '../../Layouts/AdminLayout.vue';

const groups = [
    {
        title: 'Processing',
        headerClass: 'text-blue-600',
        itemClass: 'border-l-4 border-blue-500',
        items: [
            { label: 'Customers', href: '/customers', desc: 'Customer accounts' },
            { label: 'Orders', href: '/orders', desc: 'Sales orders' },
        ],
    },
    {
        title: 'System',
        headerClass: 'text-purple-600',
        itemClass: 'border-l-4 border-purple-500',
        items: [
            { label: 'Settings', href: '/settings', desc: 'Application settings' },
            { label: 'Cache Control', href: '/cache-control', desc: 'Redis cache management' },
            { label: 'Shops', href: '/shops', desc: 'Shops & domains' },
        ],
    },
    {
        title: 'Maintenance',
        headerClass: 'text-green-600',
        itemClass: 'border-l-4 border-green-500',
        items: [
            { label: 'Users', href: '/users', desc: 'Staff accounts' },
            { label: 'Products', href: '/products', desc: 'Product catalogue' },
            { label: 'Product Offers', href: '/offers', desc: 'Pricing offers' },
            { label: 'Publications', href: '/publications', desc: 'Publications & magazines' },
            { label: 'Issues', href: '/issues', desc: 'Publication issues' },
        ],
    },
    {
        title: 'Reference',
        headerClass: 'text-amber-600',
        itemClass: 'border-l-4 border-amber-500',
        items: [
            { label: 'Regions', href: '/global-regions', desc: 'Global regions' },
            { label: 'Countries', href: '/countries', desc: 'Countries' },
            { label: 'Counties / States', href: '/county-states', desc: 'Counties & states' },
        ],
    },
];
</script>

<template>
    <AdminLayout>
        <h1 class="text-2xl font-semibold mb-2">Admin Dashboard</h1>
        <p class="text-gray-600 mb-6">Manage users, orders, customers, and catalogue data.</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div v-for="group in groups" :key="group.title" class="bg-white rounded shadow">
                <div :class="['px-6 py-3 border-b font-semibold uppercase text-sm tracking-wide', group.headerClass]">
                    {{ group.title }}
                </div>
                <div class="divide-y">
                    <Link
                        v-for="item in group.items"
                        :key="item.href"
                        :href="item.href"
                        :class="['block p-4 hover:bg-gray-50', group.itemClass]"
                    >
                        <h2 class="font-medium">{{ item.label }}</h2>
                        <p class="text-sm text-gray-500 mt-1">{{ item.desc }}</p>
                    </Link>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
