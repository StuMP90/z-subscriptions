<script setup>
import { ref, onMounted } from 'vue';
import AdminLayout from '../../../Layouts/AdminLayout.vue';

const items = ref([]);
const loading = ref(false);

const fetchItems = async () => {
    loading.value = true;
    const res = await fetch('https://api.zsubscriptions.local/cache-keys', {
        credentials: 'include',
        headers: { 'Accept': 'application/json' },
    });
    items.value = await res.json();
    loading.value = false;
};

const formatTtl = (ttl) => {
    if (ttl < 0) return 'No expiry';
    if (ttl < 60) return `${ttl}s`;
    if (ttl < 3600) return `${Math.floor(ttl / 60)}m ${ttl % 60}s`;
    const h = Math.floor(ttl / 3600);
    const m = Math.floor((ttl % 3600) / 60);
    const s = ttl % 60;
    return `${h}h ${m}m ${s}s`;
};

const formatSize = (bytes) => {
    if (bytes < 1024) return `${bytes} B`;
    if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(2)} KB`;
    return `${(bytes / (1024 * 1024)).toFixed(2)} MB`;
};

const clearOne = async (key) => {
    if (! confirm(`Clear this key?\n${key}`)) return;
    await fetch('https://api.zsubscriptions.local/cache-keys/delete', {
        method: 'POST',
        credentials: 'include',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({ key }),
    });
    await fetchItems();
};

const clearAll = async () => {
    if (! confirm('Clear all cache keys?')) return;
    await fetch('https://api.zsubscriptions.local/cache-keys/clear', {
        method: 'POST',
        credentials: 'include',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams(),
    });
    await fetchItems();
};

onMounted(fetchItems);
</script>

<template>
    <AdminLayout>
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-semibold">Cache Control</h1>
            <button
                @click="clearAll"
                class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
            >
                Clear All
            </button>
        </div>

        <div v-if="loading" class="text-gray-600">Loading...</div>

        <table v-else class="w-full bg-white rounded shadow">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3 text-left">Key</th>
                    <th class="p-3 text-left">TTL</th>
                    <th class="p-3 text-left">Size</th>
                    <th class="p-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in items" :key="item.key" class="border-b">
                    <td class="p-3 font-mono text-sm break-all">{{ item.key }}</td>
                    <td class="p-3 whitespace-nowrap">{{ formatTtl(item.ttl) }}</td>
                    <td class="p-3 whitespace-nowrap">{{ formatSize(item.size) }}</td>
                    <td class="p-3">
                        <button
                            @click="clearOne(item.key)"
                            class="text-red-600 hover:underline"
                        >
                            Clear
                        </button>
                    </td>
                </tr>
                <tr v-if="items.length === 0">
                    <td class="p-3 text-gray-500" colspan="4">No cache keys found.</td>
                </tr>
            </tbody>
        </table>
    </AdminLayout>
</template>
