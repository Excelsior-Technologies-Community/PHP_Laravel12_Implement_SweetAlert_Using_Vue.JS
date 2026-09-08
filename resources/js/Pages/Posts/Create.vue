<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { Link, useForm, router } from '@inertiajs/vue3'

import {
    errorAlert,
    loadingAlert,
    closeAlert,
    toastAlert,
    confirmUnsavedChanges
} from '@/plugins/sweetalert'

const form = useForm({
    title: '',
    description: ''
})

const isDirty = ref(false)
const isLeaving = ref(false)

/**
 * Detect form changes
 */
const markAsDirty = () => {
    isDirty.value = true
}

/**
 * Browser refresh / close protection
 */
const handleBeforeUnload = (event) => {
    if (isDirty.value && !isLeaving.value) {
        event.preventDefault()
        event.returnValue = ''
    }
}

/**
 * Submit create form
 */
const submit = () => {

    if (!form.title.trim() || !form.description.trim()) {

        toastAlert(
            'Please fill all required fields',
            'error'
        )

        return
    }

    loadingAlert('Creating post...')

    form.post('/posts', {

        preserveScroll: true,

        onStart: () => {
            isLeaving.value = true
        },

        onError: () => {

            closeAlert()

            toastAlert(
                'Please fix the validation errors',
                'error'
            )

            isLeaving.value = false
        },

        onSuccess: () => {

            isDirty.value = false
            isLeaving.value = true

        },

        onFinish: () => {

            /*
             * Only close the loading popup.
             * Do not interfere with the success toast.
             */
            closeAlert()
        }

    })
}

/**
 * Back button with unsaved changes protection
 */
const goBack = async () => {
    if (!isDirty.value) {
        router.visit('/posts')
        return
    }

    const result = await confirmUnsavedChanges()

    if (result.isConfirmed) {
        isDirty.value = false
        isLeaving.value = true

        router.visit('/posts')
    }
}

/**
 * Setup browser protection
 */
onMounted(() => {
    window.addEventListener(
        'beforeunload',
        handleBeforeUnload
    )
})

/**
 * Cleanup
 */
onBeforeUnmount(() => {
    window.removeEventListener(
        'beforeunload',
        handleBeforeUnload
    )
})
</script>

<template>
    <div class="min-h-screen bg-gray-100 py-10">
        <div class="mx-auto max-w-2xl px-4">

            <div class="rounded-lg bg-white p-6 shadow">

                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">
                            Create Post
                        </h1>

                        <p class="mt-1 text-sm text-gray-500">
                            Add a new post to your application.
                        </p>
                    </div>

                    <span
                        v-if="isDirty"
                        class="rounded-full bg-yellow-100 px-3 py-1 text-xs font-medium text-yellow-700"
                    >
                        Unsaved changes
                    </span>
                </div>

                <!-- Title -->
                <div class="mb-4">
                    <label
                        class="mb-1 block text-sm font-medium text-gray-700"
                    >
                        Title
                    </label>

                    <input
                        v-model="form.title"
                        @input="markAsDirty"
                        type="text"
                        placeholder="Enter post title"
                        class="w-full rounded border border-gray-300 p-3 focus:border-blue-500 focus:outline-none"
                    />

                    <div
                        v-if="form.errors.title"
                        class="mt-1 text-sm text-red-600"
                    >
                        {{ form.errors.title }}
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-4">
                    <label
                        class="mb-1 block text-sm font-medium text-gray-700"
                    >
                        Description
                    </label>

                    <textarea
                        v-model="form.description"
                        @input="markAsDirty"
                        rows="6"
                        placeholder="Enter post description"
                        class="w-full rounded border border-gray-300 p-3 focus:border-blue-500 focus:outline-none"
                    ></textarea>

                    <div
                        v-if="form.errors.description"
                        class="mt-1 text-sm text-red-600"
                    >
                        {{ form.errors.description }}
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex gap-3">

                    <button
                        type="button"
                        @click="submit"
                        :disabled="form.processing"
                        class="rounded bg-green-600 px-5 py-2 text-white transition hover:bg-green-700 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        {{ form.processing ? 'Creating...' : 'Save Post' }}
                    </button>

                    <button
                        type="button"
                        @click="goBack"
                        :disabled="form.processing"
                        class="rounded bg-gray-500 px-5 py-2 text-white transition hover:bg-gray-600 disabled:opacity-50"
                    >
                        Back
                    </button>

                </div>

            </div>

        </div>
    </div>
</template>