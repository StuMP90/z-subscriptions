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

const endpoint = 'https://api.zsubscriptions.local/customers';

const fields = [
    { name: 'shop_id', label: 'Shop', type: 'select', optionsUrl: 'https://api.zsubscriptions.local/shops', nullable: true },
    { name: 'first_name', label: 'First Name', type: 'text' },
    { name: 'last_name', label: 'Last Name', type: 'text' },
    { name: 'email', label: 'Email', type: 'email' },
    { name: 'phone', label: 'Phone', type: 'text' },
    { name: 'password', label: 'Password', type: 'password' },
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

const startAdd = () => { editing.value = { is_active: true }; };
const startEdit = (item) => { editing.value = { ...item }; };
const cancel = () => { editing.value = null; };
const saved = () => { editing.value = null; fetchItems(currentPage.value); };
onMounted(() => fetchItems(1));
</script>

<template>
    <AdminLayout>
        <h1 class="text-2xl font-semibold mb-4">Customers</h1>

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
            Add Customer
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
                    <th class="p-3 text-left">Email</th>
                    <th class="p-3 text-left">Shop</th>
                    <th class="p-3 text-left">Active</th>
                    <th class="p-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in items" :key="item.id" class="border-b">
                    <td class="p-3">{{ item.first_name }} {{ item.last_name }}</td>
                    <td class="p-3">{{ item.email }}</td>
                    <td class="p-3">{{ item.shop?.name }}</td>
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
