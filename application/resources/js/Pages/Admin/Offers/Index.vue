<script setup>
import { ref, onMounted } from 'vue';
import AdminLayout from '../../../Layouts/AdminLayout.vue';
import CrudForm from '../../../Components/CrudForm.vue';
import Pagination from '../../../Components/Pagination.vue';

const items = ref([]);
const currentPage = ref(1);
const lastPage = ref(1);
const search = ref('');
const editing = ref(null);

const endpoint = 'https://api.zsubscriptions.local/offers';
const regions = ref([]);

const fetchRegions = async () => {
    const res = await fetch('https://api.zsubscriptions.local/global-regions?per_page=1000', {
        credentials: 'include',
        headers: { 'Accept': 'application/json' },
    });
    const data = await res.json();
    regions.value = Array.isArray(data) ? data : (data.data || []);
};

const regionCodes = (ids) => {
    if (! ids || ! ids.length) return '—';
    return ids.map(id => regions.value.find(r => String(r.id) === String(id))?.code ?? id).join(', ') || '—';
};

const fields = [
    { name: 'product_id', label: 'Product', type: 'select', optionsUrl: 'https://api.zsubscriptions.local/products' },
    { name: 'product_variant_id', label: 'Variant', type: 'select', optionsUrl: 'https://api.zsubscriptions.local/product-variants', nullable: true },
    { name: 'shop_id', label: 'Shop', type: 'select', optionsUrl: 'https://api.zsubscriptions.local/shops', nullable: true },
    { name: 'currency_id', label: 'Currency', type: 'select', optionsUrl: 'https://api.zsubscriptions.local/currencies', labelKey: 'code' },
    { name: 'price', label: 'Price', type: 'number', step: '0.01' },
    { name: 'valid_from', label: 'Valid From', type: 'date' },
    { name: 'valid_to', label: 'Valid To', type: 'date' },
    { name: 'is_active', label: 'Active', type: 'checkbox' },
    { name: 'is_available_on_web', label: 'Available on Web', type: 'checkbox' },
    { name: 'global_region_ids', label: 'Availability Regions', type: 'multiselect', optionsUrl: 'https://api.zsubscriptions.local/global-regions' },
];

const fetchItems = async (page = 1) => {
    currentPage.value = page;
    const res = await fetch(`${endpoint}?page=${page}&search=${encodeURIComponent(search.value)}`, {
        credentials: 'include',
        headers: { 'Accept': 'application/json' },
    });
    const data = await res.json();
    items.value = data.data;
    currentPage.value = data.current_page;
    lastPage.value = data.last_page;
};

const goToPage = (page) => fetchItems(page);

const handleSearch = () => { currentPage.value = 1; fetchItems(1); };

const startAdd = () => { editing.value = { is_active: true, is_available_on_web: true, is_setup_offer: false, base_price: 0, price: 0, global_region_ids: [] }; };
const startEdit = (item) => {
    const clone = { ...item };
    if (clone.setup_config && typeof clone.setup_config === 'object') {
        clone.setup_config = JSON.stringify(clone.setup_config);
    }
    editing.value = clone;
};
const cancel = () => { editing.value = null; };
const saved = () => { editing.value = null; fetchItems(currentPage.value); };
onMounted(() => { fetchItems(1); fetchRegions(); });
</script>

<template>
    <AdminLayout>
        <h1 class="text-2xl font-semibold mb-4">Product Offers</h1>

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
            Add Product Offer
        </button>

                <div class="mb-4 flex gap-2">
            <input
                v-model="search"
                type="text"
                placeholder="Search..."
                @input="handleSearch"
                class="px-3 py-2 border rounded w-full max-w-md"
            />
        </div>
<table class="w-full bg-white rounded shadow">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3 text-left">Product</th>
                    <th class="p-3 text-left">Shop</th>
                    <th class="p-3 text-left">Currency</th>
                    <th class="p-3 text-left">Price</th>
                    <th class="p-3 text-left">Valid From</th>
                    <th class="p-3 text-left">Regions</th>
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
                    <td class="p-3">{{ item.price }}</td>
                    <td class="p-3">{{ item.valid_from }}</td>
                    <td class="p-3">{{ regionCodes(item.global_region_ids) }}</td>
                    <td class="p-3">{{ item.is_active ? 'Yes' : 'No' }}</td>
                    <td class="p-3">{{ item.is_available_on_web ? 'Yes' : 'No' }}</td>
                    <td class="p-3 flex gap-3">
                        <button @click="startEdit(item)" class="text-blue-600 hover:underline">Edit</button>
                    </td>
                </tr>
            </tbody>
        </table>

        <Pagination :currentPage="currentPage" :lastPage="lastPage" @page-change="goToPage" />
    </AdminLayout>
</template>
