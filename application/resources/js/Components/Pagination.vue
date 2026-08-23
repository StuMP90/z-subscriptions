<script setup>
const props = defineProps({
    currentPage: { type: Number, default: 1 },
    lastPage: { type: Number, default: 1 },
});

const emit = defineEmits(['page-change']);

const change = (page) => {
    if (page < 1 || page > props.lastPage) return;
    emit('page-change', page);
};
</script>

<template>
    <div v-if="lastPage > 1" class="flex items-center gap-2 mt-4">
        <button
            :disabled="currentPage <= 1"
            @click="change(1)"
            class="px-3 py-1 bg-white border rounded disabled:opacity-50"
        >
            First
        </button>
        <button
            :disabled="currentPage <= 1"
            @click="change(currentPage - 1)"
            class="px-3 py-1 bg-white border rounded disabled:opacity-50"
        >
            Previous
        </button>
        <span class="text-sm">Page {{ currentPage }} of {{ lastPage }}</span>
        <button
            :disabled="currentPage >= lastPage"
            @click="change(currentPage + 1)"
            class="px-3 py-1 bg-white border rounded disabled:opacity-50"
        >
            Next
        </button>
        <button
            :disabled="currentPage >= lastPage"
            @click="change(lastPage)"
            class="px-3 py-1 bg-white border rounded disabled:opacity-50"
        >
            Last
        </button>
    </div>
</template>
