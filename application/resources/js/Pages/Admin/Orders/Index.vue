<script setup>
import { ref, onMounted } from 'vue';
import AdminLayout from '../../../Layouts/AdminLayout.vue';
import CrudForm from '../../../Components/CrudForm.vue';

const items = ref([]);
const editing = ref(null);

const endpoint = 'https://api.zsubscriptions.local/orders';

const fields = [
    { name: 'customer_id', label: 'Customer', type: 'select', optionsUrl: 'https://api.zsubscriptions.local/customers', labelKey: 'first_name' },
    { name: 'shop_id', label: 'Shop', type: 'select', optionsUrl: 'https://api.zsubscriptions.local/shops' },
    { name: 'currency_id', label: 'Currency', type: 'select', optionsUrl: 'https://api.zsubscriptions.local/currencies', labelKey: 'code' },
    { name: 'order_number', label: 'Order #', type: 'text' },
    { name: 'source', label: 'Source', type: 'text' },
    { name: 'status', label: 'Status', type: 'text' },
    { name: 'subtotal', label: 'Subtotal', type: 'number' },
    { name: 'tax', label: 'Tax', type: 'number' },
    { name: 'shipping', label: 'Shipping', type: 'number' },
    { name: 'total', label: 'Total', type: 'number' },
    { name: 'notes', label: 'Notes', type: 'textarea' },
    { name: 'placed_at', label: 'Placed At', type: 'date' },
];

const fetchItems = async () => {
    const res = await fetch(endpoint, {
        credentials: 'include',
        headers: { 'Accept': 'application/json' },
    });
    items.value = await res.json();
};

const startAdd = () => { editing.value = { status: 'pending', subtotal: 0, tax: 0, shipping: 0, total: 0 }; };
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
                    <th class="p-3 text-left">Source</th>
                    <th class="p-3 text-left">Status</th>
                    <th class="p-3 text-left">Subtotal</th>
                    <th class="p-3 text-left">Tax</th>
                    <th class="p-3 text-left">Shipping</th>
                    <th class="p-3 text-left">Total</th>
                    <th class="p-3 text-left">Placed At</th>
                    <th class="p-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in items" :key="item.id" class="border-b">
                    <td class="p-3">{{ item.order_number }}</td>
                    <td class="p-3">{{ item.customer?.first_name }} {{ item.customer?.last_name }}</td>
                    <td class="p-3">{{ item.shop?.name }}</td>
                    <td class="p-3">{{ item.source }}</td>
                    <td class="p-3">{{ item.status }}</td>
                    <td class="p-3">{{ item.subtotal }}</td>
                    <td class="p-3">{{ item.tax }}</td>
                    <td class="p-3">{{ item.shipping }}</td>
                    <td class="p-3">{{ item.total }}</td>
                    <td class="p-3">{{ item.placed_at }}</td>
                    <td class="p-3 flex gap-3">
                        <button @click="startEdit(item)" class="text-blue-600 hover:underline">Edit</button>
                        <button @click="remove(item)" class="text-red-600 hover:underline">Delete</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </AdminLayout>
</template>
