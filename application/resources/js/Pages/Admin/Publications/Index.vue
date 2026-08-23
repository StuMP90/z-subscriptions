<script setup>
import { ref, onMounted } from 'vue';
import AdminLayout from '../../../Layouts/AdminLayout.vue';
import CrudForm from '../../../Components/CrudForm.vue';
import Pagination from '../../../Components/Pagination.vue';

const items = ref([]);
const currentPage = ref(1);
const lastPage = ref(1);
const regions = ref([]);
const editing = ref(null);

const endpoint = 'https://api.zsubscriptions.local/publications';

const fields = [
    { name: 'name', label: 'Name', type: 'text' },
    { name: 'slug', label: 'Slug', type: 'text' },
    { name: 'description', label: 'Description', type: 'textarea' },
    { name: 'image', label: 'Image', type: 'text' },
    { name: 'is_active', label: 'Active', type: 'checkbox' },
    { name: 'is_available_on_web', label: 'Available on Web', type: 'checkbox' },
    { name: 'global_region_ids', label: 'Availability Regions', type: 'multiselect', optionsUrl: 'https://api.zsubscriptions.local/global-regions' },
];

const fetchItems = async (page = 1) => {
    currentPage.value = page;
    const res = await fetch(`${endpoint}?page=${page}`, {
        credentials: 'include',
        headers: { 'Accept': 'application/json' },
    });
    const data = await res.json();
    items.value = data.data;
    currentPage.value = data.current_page;
    lastPage.value = data.last_page;
};

const goToPage = (page) => fetchItems(page);

const fetchRegions = async () => {
    const res = await fetch('https://api.zsubscriptions.local/global-regions', {
        credentials: 'include',
        headers: { 'Accept': 'application/json' },
    });
    const data = await res.json();
    regions.value = Array.isArray(data) ? data : (data.data || []);
};

const regionCodes = (ids) => {
    if (! ids || ! ids.length) return '—';
    return regions.value
        .filter(region => ids.includes(region.id))
        .map(region => region.code)
        .join(', ') || '—';
};

const startAdd = () => { editing.value = { is_active: true, is_available_on_web: true, global_region_ids: [] }; };
const startEdit = (item) => { editing.value = { ...item }; };
const cancel = () => { editing.value = null; };
const saved = () => { editing.value = null; fetchItems(currentPage.value); };

const remove = async (item) => {
    if (! confirm('Delete this publication?')) return;
    await fetch(`${endpoint}/${item.id}`, {
        method: 'DELETE',
        credentials: 'include',
        headers: { 'Accept': 'application/json' },
    });
    await fetchItems(currentPage.value);
};

onMounted(() => {
    fetchItems(1);
    fetchRegions();
});
</script>

<template>
    <AdminLayout>
        <h1 class="text-2xl font-semibold mb-4">Publications</h1>

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
            Add Publication
        </button>

        <table class="w-full bg-white rounded shadow">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3 text-left">Name</th>
                    <th class="p-3 text-left">Slug</th>
                    <th class="p-3 text-left">Active</th>
                    <th class="p-3 text-left">Web</th>
                    <th class="p-3 text-left">Regions</th>
                    <th class="p-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in items" :key="item.id" class="border-b">
                    <td class="p-3">{{ item.name }}</td>
                    <td class="p-3">{{ item.slug }}</td>
                    <td class="p-3">{{ item.is_active ? 'Yes' : 'No' }}</td>
                    <td class="p-3">{{ item.is_available_on_web ? 'Yes' : 'No' }}</td>
                    <td class="p-3">{{ regionCodes(item.global_region_ids) }}</td>
                    <td class="p-3 flex gap-3">
                        <button @click="startEdit(item)" class="text-blue-600 hover:underline">Edit</button>
                        <button @click="remove(item)" class="text-red-600 hover:underline">Delete</button>
                    </td>
                </tr>
            </tbody>
        </table>

        <Pagination :currentPage="currentPage" :lastPage="lastPage" @page-change="goToPage" />
    </AdminLayout>
</template>
