<script setup>
// Import Inertia form helper
import { useForm } from '@inertiajs/vue3'

// Import SweetAlert error helper
import { errorAlert } from '@/plugins/sweetalert'

// Receive post from controller
const props = defineProps({
    post: Object
})

// Initialize form with existing post data
const form = useForm({
    title: props.post.title,
    description: props.post.description,
})

/**
 * Submit form to update post
 */
const submit = () => {
    form.put(`/posts/${props.post.id}`, {
        // If validation fails
        onError: () => {
            errorAlert('Please fix validation errors')
        }
        // Success SweetAlert handled globally
    })
}
</script>

<template>
    <div class="p-6">
        <h1 class="text-xl mb-4">Edit Post</h1>

        <!-- Title input -->
        <input
            v-model="form.title"
            class="border p-2 w-full mb-2"
        />
        <div v-if="form.errors.title" class="text-red-600 text-sm">
            {{ form.errors.title }}
        </div>

        <!-- Description input -->
        <textarea
            v-model="form.description"
            class="border p-2 w-full"
        ></textarea>
        <div v-if="form.errors.description" class="text-red-600 text-sm">
            {{ form.errors.description }}
        </div>

        <!-- Update button -->
        <button
            @click="submit"
            :disabled="form.processing"
            class="bg-blue-600 text-white px-4 py-2 mt-3"
        >
            Update
        </button>
    </div>
</template>
