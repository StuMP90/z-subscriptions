<script setup>
import { ref, onMounted } from 'vue';
import AdminLayout from '../../../Layouts/AdminLayout.vue';
import CrudForm from '../../../Components/CrudForm.vue';
import Pagination from '../../../Components/Pagination.vue';

const items = ref([]);
const currentPage = ref(1);
const lastPage = ref(1);
const search = ref('');
const regions = ref([]);
const editing = ref(null);

const endpoint = 'https://api.zsubscriptions.local/shops';

const fields = [
    { name: 'name', label: 'Name', type: 'text' },
    { name: 'slug', label: 'Slug', type: 'text' },
    { name: 'domain', label: 'Domain', type: 'text' },
    { name: 'default_currency_id', label: 'Currency', type: 'select', optionsUrl: 'https://api.zsubscriptions.local/currencies', labelKey: 'code' },
    { name: 'global_region_ids', label: 'Regions', type: 'multiselect', optionsUrl: 'https://api.zsubscriptions.local/global-regions' },
    { name: 'theme', label: 'Theme', type: 'text' },
    { name: 'is_active', label: 'Active', type: 'checkbox' },
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

const fetchRegions = async () => {
    const res = await fetch('https://api.zsubscriptions.local/global-regions', {
        credentials: 'include',
        headers: { 'Accept': 'application/json' },
    });
    const data = await res.json();
    regions.value = Array.isArray(data) ? data : (data.data || []);
};

const globalRegionCodes = (ids) => {
    if (! Array.isArray(ids)) return '';
    return ids.map(id => regions.value.find(r => r.id === id)?.code ?? id).join(', ');
};

const startAdd = () => { editing.value = { is_active: true, global_region_ids: [] }; };
const startEdit = (item) => { editing.value = { ...item }; };
const cancel = () => { editing.value = null; };
const saved = () => { editing.value = null; fetchItems(currentPage.value); };
onMounted(() => { fetchItems(1); fetchRegions(); });
</script>

<template>
    <AdminLayout>
        <h1 class="text-2xl font-semibold mb-4">Shops</h1>

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
            Add Shop
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
                    <th class="p-3 text-left">Name</th>
                    <th class="p-3 text-left">Domain</th>
                    <th class="p-3 text-left">Regions</th>
                    <th class="p-3 text-left">Currency</th>
                    <th class="p-3 text-left">Theme</th>
                    <th class="p-3 text-left">Active</th>
                    <th class="p-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in items" :key="item.id" class="border-b">
                    <td class="p-3">{{ item.name }}</td>
                    <td class="p-3">{{ item.domain }}</td>
                    <td class="p-3">{{ globalRegionCodes(item.global_region_ids) }}</td>
                    <td class="p-3">{{ item.default_currency?.code }}</td>
                    <td class="p-3">{{ item.theme }}</td>
                    <td class="p-3">{{ item.is_active ? 'Yes' : 'No' }}</td>
                    <td class="p-3 flex gap-3">
                        <button @click="startEdit(item)" class="text-blue-600 hover:underline">Edit</button>
                    </td>
                </tr>
            </tbody>
        </table>

        <Pagination :currentPage="currentPage" :lastPage="lastPage" @page-change="goToPage" />
    </AdminLayout>
</template>
