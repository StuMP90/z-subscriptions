<script setup>
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    email: '',
    password: '',
});

const submit = () => {
    form.post('/login');
};
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-50">
        <form @submit.prevent="submit" class="w-full max-w-md p-8 bg-white rounded shadow">
            <h1 class="text-2xl font-semibold mb-6">Admin Login</h1>

            <div>
                <label class="block text-sm font-medium">Email</label>
                <input
                    v-model="form.email"
                    type="email"
                    class="mt-1 w-full border rounded p-2"
                    required
                />
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium">Password</label>
                <input
                    v-model="form.password"
                    type="password"
                    class="mt-1 w-full border rounded p-2"
                    required
                />
            </div>

            <div v-if="form.errors.email" class="mt-4 text-red-600 text-sm">
                {{ form.errors.email }}
            </div>

            <button
                type="submit"
                class="mt-6 w-full bg-blue-600 text-white py-2 rounded"
                :disabled="form.processing"
            >
                Login
            </button>
        </form>
    </div>
</template>
