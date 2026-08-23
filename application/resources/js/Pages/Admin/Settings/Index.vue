<script setup>
import { ref, onMounted } from 'vue';
import AdminLayout from '../../../Layouts/AdminLayout.vue';
import CrudForm from '../../../Components/CrudForm.vue';
import Pagination from '../../../Components/Pagination.vue';

const items = ref([]);
const currentPage = ref(1);
const lastPage = ref(1);
const editing = ref(null);

const endpoint = 'https://api.zsubscriptions.local/settings';

const fields = [
    { name: 'shop_id', label: 'Shop', type: 'select', optionsUrl: 'https://api.zsubscriptions.local/shops', nullable: true },
    { name: 'group', label: 'Group', type: 'text' },
    { name: 'key', label: 'Key', type: 'text' },
    { name: 'value', label: 'Value', type: 'textarea' },
    { name: 'type', label: 'Type', type: 'select', options: [
        { value: 'string', label: 'String' },
        { value: 'integer', label: 'Integer' },
        { value: 'boolean', label: 'Boolean' },
    ]},
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

const startAdd = () => { editing.value = { shop_id: '', group: 'general', key: '', value: '', type: 'string' }; };
const startEdit = (item) => { editing.value = { ...item }; };
const cancel = () => { editing.value = null; };
const saved = () => { editing.value = null; fetchItems(currentPage.value); };

const remove = async (item) => {
    if (! confirm('Delete this setting?')) return;
    await fetch(`${endpoint}/${item.id}`, {
        method: 'DELETE',
        credentials: 'include',
        headers: { 'Accept': 'application/json' },
    });
    await fetchItems(currentPage.value);
};

onMounted(() => fetchItems(1));
</script>

<template>
    <AdminLayout>
        <h1 class="text-2xl font-semibold mb-4">Settings</h1>

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
            Add Setting
        </button>

        <table class="w-full bg-white rounded shadow">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3 text-left">Shop</th>
                    <th class="p-3 text-left">Group</th>
                    <th class="p-3 text-left">Key</th>
                    <th class="p-3 text-left">Value</th>
                    <th class="p-3 text-left">Type</th>
                    <th class="p-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in items" :key="item.id" class="border-b">
                    <td class="p-3">{{ item.shop?.name ?? '—' }}</td>
                    <td class="p-3">{{ item.group }}</td>
                    <td class="p-3">{{ item.key }}</td>
                    <td class="p-3">{{ item.value }}</td>
                    <td class="p-3">{{ item.type }}</td>
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
