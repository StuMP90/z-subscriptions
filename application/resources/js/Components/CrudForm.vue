<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    endpoint: { type: String, required: true },
    fields: { type: Array, required: true },
    modelValue: { type: Object, default: () => ({}) },
});

const emit = defineEmits(['saved', 'cancelled']);

const local = ref({ ...props.modelValue });
const saving = ref(false);
const error = ref(null);

watch(() => props.modelValue, (value) => {
    local.value = { ...value };
}, { deep: true });

const save = async () => {
    saving.value = true;
    error.value = null;

    try {
        const method = local.value.id ? 'PUT' : 'POST';
        const url = local.value.id ? `${props.endpoint}/${local.value.id}` : props.endpoint;

        const res = await fetch(url, {
            method,
            credentials: 'include',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(local.value),
        });

        if (! res.ok) {
            const data = await res.json();
            error.value = data.message || 'Save failed';
            return;
        }

        emit('saved');
    } catch (e) {
        error.value = e.message;
    } finally {
        saving.value = false;
    }
};

const cancel = () => {
    emit('cancelled');
};
</script>

<template>
    <form @submit.prevent="save" class="bg-white p-4 rounded shadow mb-4">
        <h2 class="text-lg font-semibold mb-2">
            {{ local.id ? 'Edit' : 'Add' }}
        </h2>

        <div v-for="field in fields" :key="field.name" class="mb-3">
            <label class="block text-sm font-medium mb-1">{{ field.label }}</label>
            <input
                v-if="field.type === 'checkbox'"
                v-model="local[field.name]"
                :type="field.type"
                class="h-5 w-5"
            />
            <select
                v-else-if="field.type === 'select'"
                v-model="local[field.name]"
                class="border p-2 w-full rounded"
            >
                <option v-for="opt in field.options" :key="opt.value" :value="opt.value">
                    {{ opt.label }}
                </option>
            </select>
            <input
                v-else
                v-model="local[field.name]"
                :type="field.type"
                class="border p-2 w-full rounded"
            />
        </div>

        <div v-if="error" class="text-red-600 mb-3 text-sm">{{ error }}</div>

        <div class="flex gap-2">
            <button
                type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded"
                :disabled="saving"
            >
                {{ saving ? 'Saving...' : 'Save' }}
            </button>
            <button type="button" @click="cancel" class="px-4 py-2 bg-gray-200 rounded">
                Cancel
            </button>
        </div>
    </form>
</template>
