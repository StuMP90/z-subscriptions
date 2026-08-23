<script setup>
import { ref, onMounted } from 'vue';
import AdminLayout from '../../../Layouts/AdminLayout.vue';
import CrudForm from '../../../Components/CrudForm.vue';

const items = ref([]);
const editing = ref(null);

const endpoint = 'https://api.zsubscriptions.local/shops';

const fields = [
    { name: 'name', label: 'Name', type: 'text' },
    { name: 'slug', label: 'Slug', type: 'text' },
    { name: 'domain', label: 'Domain', type: 'text' },
    { name: 'default_currency_id', label: 'Currency ID', type: 'number' },
    { name: 'global_region_id', label: 'Region ID', type: 'number' },
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
    if (! confirm('Delete this shop?')) return;
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

        <table class="w-full bg-white rounded shadow">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3 text-left">Name</th>
                    <th class="p-3 text-left">Domain</th>
                    <th class="p-3 text-left">Region</th>
                    <th class="p-3 text-left">Currency</th>
                    <th class="p-3 text-left">Active</th>
                    <th class="p-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in items" :key="item.id" class="border-b">
                    <td class="p-3">{{ item.name }}</td>
                    <td class="p-3">{{ item.domain }}</td>
                    <td class="p-3">{{ item.global_region?.name }}</td>
                    <td class="p-3">{{ item.default_currency?.code }}</td>
                    <td class="p-3">{{ item.is_active ? 'Yes' : 'No' }}</td>
                    <td class="p-3 flex gap-3">
                        <button @click="startEdit(item)" class="text-blue-600 hover:underline">Edit</button>
                        <button @click="remove(item)" class="text-red-600 hover:underline">Delete</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </AdminLayout>
</template>
