<script setup>
import { ref, reactive, watch, onMounted } from 'vue';

const props = defineProps({
    endpoint: { type: String, required: true },
    fields: { type: Array, required: true },
    modelValue: { type: Object, default: () => ({}) },
});

const emit = defineEmits(['saved', 'cancelled']);

const local = reactive({ ...props.modelValue });
const saving = ref(false);
const error = ref(null);
const selectOptions = ref({});
const selectSearch = reactive({});
const defaults = ref({});
const generatedSlug = ref('');

const optionCount = (field) => (field.options || selectOptions.value[field.name] || []).length;

const getOptions = (field) => {
    const all = field.options || selectOptions.value[field.name] || [];
    const q = selectSearch[field.name] || '';
    if (optionCount(field) < 20 || ! q) return all;
    const term = String(q).toLowerCase();
    return all.filter(opt => String(opt.label).toLowerCase().includes(term));
};

const normalise = (value) => {
    const clone = { ...value };
    for (const field of props.fields) {
        if (field.type === 'select') {
            clone[field.name] = clone[field.name] == null ? '' : String(clone[field.name]);
        }
        if (field.type === 'multiselect') {
            const arr = Array.isArray(clone[field.name]) ? clone[field.name] : [];
            clone[field.name] = arr.map(v => String(v));
        }
        if (field.type === 'date') {
            clone[field.name] = clone[field.name] ? String(clone[field.name]).slice(0, 10) : '';
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

const setLocal = (value) => {
    const next = applyDefaults(normalise(value));
    Object.keys(local).forEach(k => delete local[k]);
    Object.assign(local, next);
    generatedSlug.value = local.slug || '';
};

watch(() => props.modelValue, (value) => { setLocal(value); }, { deep: true });

watch(() => local.name, (name) => {
    if (! name || local.id) return;
    const newSlug = slugify(name);
    if (isEmpty(local.slug) || local.slug === generatedSlug.value) {
        local.slug = newSlug;
        generatedSlug.value = newSlug;
    }
});

onMounted(async () => {
    for (const field of props.fields) {
        if (field.type === 'select' || field.type === 'multiselect') {
            selectSearch[field.name] = '';
            selectOptions.value[field.name] = field.options || [];
        }
        if ((field.type === 'select' || field.type === 'multiselect') && field.optionsUrl) {
            try {
                const sep = field.optionsUrl.includes('?') ? '&' : '?';
                const res = await fetch(`${field.optionsUrl}${sep}per_page=1000`, {
                    credentials: 'include',
                    headers: { 'Accept': 'application/json' },
                });
                const json = await res.json();
                const data = Array.isArray(json) ? json : (json.data || []);
                const valueKey = field.valueKey || 'id';
                const labelKey = field.labelKey || 'name';
                selectOptions.value[field.name] = data.map(item => ({
                    value: String(item[valueKey]),
                    label: item[labelKey],
                }));
            } catch (e) {
                selectOptions.value[field.name] = [];
            }
        }
    }

    await fetchDefaults();
    setLocal({ ...local });
});

const token = typeof document !== 'undefined'
    ? document.querySelector('meta[name="csrf-token"]')?.content
    : null;

const save = async () => {
    saving.value = true;
    error.value = null;

    try {
        const method = local.id ? 'PUT' : 'POST';
        const url = local.id ? `${props.endpoint}/${local.id}` : props.endpoint;

        const res = await fetch(url, {
            method,
            credentials: 'include',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                ...(token ? { 'X-CSRF-TOKEN': token } : {}),
            },
            body: JSON.stringify(local),
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
            <template v-else-if="field.type === 'select' || field.type === 'multiselect'">
                <input
                    v-if="optionCount(field) >= 20"
                    :value="selectSearch[field.name]"
                    type="text"
                    placeholder="Type to search options..."
                    class="border p-2 w-full rounded mb-2"
                    @input="selectSearch[field.name] = $event.target.value"
                />
                <select
                    v-if="field.type === 'select'"
                    :value="local[field.name]"
                    class="border p-2 w-full rounded"
                    size="6"
                    @change="local[field.name] = $event.target.value"
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
            </template>
            <input
                v-else-if="field.type === 'date'"
                :value="local[field.name]"
                type="date"
                class="border p-2 w-full rounded"
                @input="local[field.name] = $event.target.value"
            />
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
