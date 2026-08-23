<script setup>
import { ref, watch, onMounted } from 'vue';

const props = defineProps({
    endpoint: { type: String, required: true },
    fields: { type: Array, required: true },
    modelValue: { type: Object, default: () => ({}) },
});

const emit = defineEmits(['saved', 'cancelled']);

const local = ref({ ...props.modelValue });
const saving = ref(false);
const error = ref(null);
const selectOptions = ref({});
const defaults = ref({});
const generatedSlug = ref('');

const getOptions = (field) => {
    if (field.options) return field.options;
    return selectOptions.value[field.name] || [];
};

const normalise = (value) => {
    const clone = { ...value };
    for (const field of props.fields) {
        if (field.type === 'multiselect') {
            clone[field.name] = Array.isArray(clone[field.name]) ? clone[field.name] : [];
        }
    }
    return clone;
};

const isEmpty = (v) => v === undefined || v === null || v === '' || (Array.isArray(v) && v.length === 0);

const slugify = (value) => {
    return String(value)
        .toLowerCase()
        .trim()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');
};

const applyDefaults = (value) => {
    if (value.id) return value;
    for (const [key, defaultValue] of Object.entries(defaults.value)) {
        if (isEmpty(value[key])) {
            value[key] = defaultValue;
        }
    }
    return value;
};

const fetchDefaults = async () => {
    try {
        const res = await fetch('https://api.zsubscriptions.local/defaults', {
            credentials: 'include',
            headers: { 'Accept': 'application/json' },
        });
        defaults.value = await res.json();
    } catch (e) {
        defaults.value = {};
    }
};

watch(() => props.modelValue, (value) => {
    local.value = applyDefaults(normalise(value));
    generatedSlug.value = local.value.slug || '';
}, { deep: true });

watch(() => local.value?.name, (name) => {
    if (! name || local.value?.id) return;
    const newSlug = slugify(name);
    if (isEmpty(local.value?.slug) || local.value.slug === generatedSlug.value) {
        local.value.slug = newSlug;
        generatedSlug.value = newSlug;
    }
});

onMounted(async () => {
    local.value = applyDefaults(normalise(local.value));

    for (const field of props.fields) {
        if ((field.type === 'select' || field.type === 'multiselect') && field.optionsUrl) {
            try {
                const res = await fetch(field.optionsUrl, {
                    credentials: 'include',
                    headers: { 'Accept': 'application/json' },
                });
                const json = await res.json();
                const data = Array.isArray(json) ? json : (json.data || []);
                const valueKey = field.valueKey || 'id';
                const labelKey = field.labelKey || 'name';
                selectOptions.value[field.name] = data.map(item => ({
                    value: item[valueKey],
                    label: item[labelKey],
                }));
            } catch (e) {
                selectOptions.value[field.name] = [];
            }
        }
    }

    await fetchDefaults();
    local.value = applyDefaults(local.value);
});

const token = typeof document !== 'undefined'
    ? document.querySelector('meta[name="csrf-token"]')?.content
    : null;

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
                ...(token ? { 'X-CSRF-TOKEN': token } : {}),
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
                <option v-if="field.nullable" value="">-- None --</option>
                <option v-for="opt in getOptions(field)" :key="opt.value" :value="opt.value">
                    {{ opt.label }}
                </option>
            </select>
            <select
                v-else-if="field.type === 'multiselect'"
                v-model="local[field.name]"
                multiple
                class="border p-2 w-full rounded"
                size="6"
            >
                <option v-for="opt in getOptions(field)" :key="opt.value" :value="opt.value">
                    {{ opt.label }}
                </option>
            </select>
            <textarea
                v-else-if="field.type === 'textarea'"
                v-model="local[field.name]"
                class="border p-2 w-full rounded"
                rows="4"
            ></textarea>
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
