<script setup>
import { ref, onMounted } from 'vue';
import AdminLayout from '../../../Layouts/AdminLayout.vue';
import CrudForm from '../../../Components/CrudForm.vue';

const items = ref([]);
const editing = ref(null);

const endpoint = 'https://api.zsubscriptions.local/orders';

const fields = [
    { name: 'customer_id', label: 'Customer ID', type: 'number' },
    { name: 'shop_id', label: 'Shop ID', type: 'number' },
    { name: 'currency_id', label: 'Currency ID', type: 'number' },
    { name: 'order_number', label: 'Order #', type: 'text' },
    { name: 'status', label: 'Status', type: 'text' },
    { name: 'total', label: 'Total', type: 'number' },
    { name: 'is_active', label: 'Active', type: 'checkbox' },
];

const fetchItems = async () => {
    const res = await fetch(endpoint, {
        credentials: 'include',
        headers: { 'Accept': 'application/json' },
    });
    items.value = await res.json();
};

const startAdd = () => { editing.value = { is_active: true }; };
const startEdit = (item) => { editing.value = { ...item }; };
const cancel = () => { editing.value = null; };
const saved = () => { editing.value = null; fetchItems(); };

const remove = async (item) => {
    if (! confirm('Delete this order?')) return;
    await fetch(`${endpoint}/${item.id}`, {
        method: 'DELETE',
        credentials: 'include',
        headers: { 'Accept': 'application/json' },
    });
    await fetchItems();
};

onMounted(fetchItems);
</script>

<template>
    <AdminLayout>
        <h1 class="text-2xl font-semibold mb-4">Orders</h1>

        <CrudForm
            v-if="editing"
            :endpoint="endpoint"
            :fields="fields"
            :modelValue="editing"
            @saved="saved"
            @cancelled="cancel"
        />
        <button
            v-else
            @click="startAdd"
            class="mb-4 px-4 py-2 bg-blue-600 text-white rounded"
        >
            Add Order
        </button>

        <table class="w-full bg-white rounded shadow">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3 text-left">Order #</th>
                    <th class="p-3 text-left">Customer</th>
                    <th class="p-3 text-left">Shop</th>
                    <th class="p-3 text-left">Status</th>
                    <th class="p-3 text-left">Total</th>
                    <th class="p-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in items" :key="item.id" class="border-b">
                    <td class="p-3">{{ item.order_number }}</td>
                    <td class="p-3">{{ item.customer?.first_name }} {{ item.customer?.last_name }}</td>
                    <td class="p-3">{{ item.shop?.name }}</td>
                    <td class="p-3">{{ item.status }}</td>
                    <td class="p-3">{{ item.total }}</td>
                    <td class="p-3 flex gap-3">
                        <button @click="startEdit(item)" class="text-blue-600 hover:underline">Edit</button>
                        <button @click="remove(item)" class="text-red-600 hover:underline">Delete</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </AdminLayout>
</template>
