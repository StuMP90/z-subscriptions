<script setup>
import { Link } from '@inertiajs/vue3';
import AdminLayout from '../../Layouts/AdminLayout.vue';

const sections = [
    { label: 'Users', href: '/users', desc: 'Staff accounts' },
    { label: 'Customers', href: '/customers', desc: 'Customer accounts' },
    { label: 'Orders', href: '/orders', desc: 'Sales orders' },
    { label: 'Shops', href: '/shops', desc: 'Shops & domains' },
    { label: 'Products', href: '/products', desc: 'Product catalogue' },
    { label: 'Offers', href: '/offers', desc: 'Pricing offers' },
];
</script>

<template>
    <AdminLayout>
        <h1 class="text-2xl font-semibold mb-2">Admin Dashboard</h1>
        <p class="text-gray-600 mb-6">Manage users, orders, customers, and catalogue data.</p>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <Link
                v-for="section in sections"
                :key="section.href"
                :href="section.href"
                class="block p-6 bg-white rounded shadow hover:shadow-md"
            >
                <h2 class="text-lg font-medium">{{ section.label }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ section.desc }}</p>
            </Link>
        </div>
    </AdminLayout>
</template>
