<script setup>
import { ref, onMounted } from 'vue';
import AdminLayout from '../../../Layouts/AdminLayout.vue';
import CrudForm from '../../../Components/CrudForm.vue';

const items = ref([]);
const editing = ref(null);

const endpoint = 'https://api.zsubscriptions.local/offers';

const fields = [
    { name: 'product_id', label: 'Product', type: 'select', optionsUrl: 'https://api.zsubscriptions.local/products' },
    { name: 'product_variant_id', label: 'Variant', type: 'select', optionsUrl: 'https://api.zsubscriptions.local/product-variants', nullable: true },
    { name: 'shop_id', label: 'Shop', type: 'select', optionsUrl: 'https://api.zsubscriptions.local/shops' },
    { name: 'currency_id', label: 'Currency', type: 'select', optionsUrl: 'https://api.zsubscriptions.local/currencies', labelKey: 'code' },
    { name: 'frequency_id', label: 'Frequency', type: 'select', optionsUrl: 'https://api.zsubscriptions.local/subscription-frequencies' },
    { name: 'base_price', label: 'Base Price', type: 'number' },
    { name: 'price', label: 'Price', type: 'number' },
    { name: 'valid_from', label: 'Valid From', type: 'date' },
    { name: 'valid_to', label: 'Valid To', type: 'date' },
    { name: 'is_setup_offer', label: 'Setup Offer', type: 'checkbox' },
    { name: 'setup_config', label: 'Setup Config (JSON)', type: 'textarea' },
    { name: 'is_active', label: 'Active', type: 'checkbox' },
    { name: 'is_available_on_web', label: 'Available on Web', type: 'checkbox' },
    { name: 'global_region_ids', label: 'Availability Regions', type: 'multiselect', optionsUrl: 'https://api.zsubscriptions.local/global-regions' },
];

const fetchItems = async () => {
    const res = await fetch(endpoint, {
        credentials: 'include',
        headers: { 'Accept': 'application/json' },
    });
    items.value = await res.json();
};

const startAdd = () => { editing.value = { is_active: true, is_available_on_web: true, is_setup_offer: false, base_price: 0, price: 0, global_region_ids: [] }; };
const startEdit = (item) => {
    const clone = { ...item };
    if (clone.setup_config && typeof clone.setup_config === 'object') {
        clone.setup_config = JSON.stringify(clone.setup_config);
    }
    editing.value = clone;
};
const cancel = () => { editing.value = null; };
const saved = () => { editing.value = null; fetchItems(); };

const remove = async (item) => {
    if (! confirm('Delete this offer?')) return;
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
        <h1 class="text-2xl font-semibold mb-4">Offers</h1>

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
            Add Offer
        </button>

        <table class="w-full bg-white rounded shadow">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3 text-left">Product</th>
                    <th class="p-3 text-left">Shop</th>
                    <th class="p-3 text-left">Currency</th>
                    <th class="p-3 text-left">Frequency</th>
                    <th class="p-3 text-left">Base</th>
                    <th class="p-3 text-left">Price</th>
                    <th class="p-3 text-left">Valid From</th>
                    <th class="p-3 text-left">Active</th>
                    <th class="p-3 text-left">Web</th>
                    <th class="p-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in items" :key="item.id" class="border-b">
                    <td class="p-3">{{ item.product?.name }}</td>
                    <td class="p-3">{{ item.shop?.name }}</td>
                    <td class="p-3">{{ item.currency?.code }}</td>
                    <td class="p-3">{{ item.frequency?.name }}</td>
                    <td class="p-3">{{ item.base_price }}</td>
                    <td class="p-3">{{ item.price }}</td>
                    <td class="p-3">{{ item.valid_from }}</td>
                    <td class="p-3">{{ item.is_active ? 'Yes' : 'No' }}</td>
                    <td class="p-3">{{ item.is_available_on_web ? 'Yes' : 'No' }}</td>
                    <td class="p-3 flex gap-3">
                        <button @click="startEdit(item)" class="text-blue-600 hover:underline">Edit</button>
                        <button @click="remove(item)" class="text-red-600 hover:underline">Delete</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </AdminLayout>
</template>
