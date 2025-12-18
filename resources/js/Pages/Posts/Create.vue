<script setup>
// Import Inertia form helper
import { useForm } from '@inertiajs/vue3'

// Import SweetAlert error helper
import { errorAlert } from '@/plugins/sweetalert'

// Form state
const form = useForm({
    title: '',
    description: '',
})

/**
 * Submit form to create post
 */
const submit = () => {
    form.post('/posts', {
        // If validation fails
        onError: () => {
            errorAlert('Please fill all required fields')
        }
        // Success SweetAlert handled globally
    })
}
</script>

<template>
    <div class="p-6">
        <h1 class="text-xl mb-4">Create Post</h1>

        <!-- Title input -->
        <input
            v-model="form.title"
            placeholder="Title"
            class="border p-2 w-full mb-2"
        />
        <div v-if="form.errors.title" class="text-red-600 text-sm">
            {{ form.errors.title }}
        </div>

        <!-- Description input -->
        <textarea
            v-model="form.description"
            placeholder="Description"
            class="border p-2 w-full"
        ></textarea>
        <div v-if="form.errors.description" class="text-red-600 text-sm">
            {{ form.errors.description }}
        </div>

        <!-- Submit button -->
        <button
            @click="submit"
            :disabled="form.processing"
            class="bg-green-600 text-white px-4 py-2 mt-3"
        >
            Save
        </button>
    </div>
</template>
