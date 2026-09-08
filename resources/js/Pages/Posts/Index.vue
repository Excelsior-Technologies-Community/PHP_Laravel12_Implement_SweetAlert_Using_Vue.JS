<script setup>
import { router, Link } from '@inertiajs/vue3'

import {
    confirmDelete,
    loadingAlert,
    closeAlert,
    toastAlert
} from '@/plugins/sweetalert'

defineProps({
    posts: Array
})

/**
 * Delete post
 */
const deletePost = async (id) => {

    const result = await confirmDelete()

    if (!result.isConfirmed) {
        return
    }

    loadingAlert('Deleting post...')

    router.delete(`/posts/${id}`, {

        preserveScroll: true,

        onError: () => {
            closeAlert()

            toastAlert(
                'Unable to delete the post',
                'error'
            )
        },

        onFinish: () => {
            closeAlert()
        }
    })
}
</script>

<template>
    <div class="min-h-screen bg-gray-100 py-10">

        <div class="mx-auto max-w-6xl px-4">

            <!-- Header -->
            <div
                class="mb-6 flex items-center justify-between"
            >
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">
                        Posts
                    </h1>

                    <p class="mt-1 text-sm text-gray-500">
                        Manage your posts using SweetAlert2.
                    </p>
                </div>

                <Link
                    href="/posts/create"
                    class="rounded bg-blue-600 px-5 py-2 text-white transition hover:bg-blue-700"
                >
                    + Add Post
                </Link>
            </div>

            <!-- Posts table -->
            <div
                class="overflow-hidden rounded-lg bg-white shadow"
            >

                <div class="overflow-x-auto">

                    <table class="w-full">

                        <thead>
                            <tr
                                class="border-b bg-gray-50 text-left"
                            >
                                <th class="p-4 font-semibold text-gray-700">
                                    #
                                </th>

                                <th class="p-4 font-semibold text-gray-700">
                                    Title
                                </th>

                                <th class="p-4 font-semibold text-gray-700">
                                    Description
                                </th>

                                <th class="p-4 text-center font-semibold text-gray-700">
                                    Action
                                </th>
                            </tr>
                        </thead>

                        <tbody>

                            <tr
                                v-for="(post, index) in posts"
                                :key="post.id"
                                class="border-b transition hover:bg-gray-50"
                            >

                                <td class="p-4 text-gray-500">
                                    {{ index + 1 }}
                                </td>

                                <td class="p-4 font-medium text-gray-800">
                                    {{ post.title }}
                                </td>

                                <td class="max-w-md p-4 text-gray-600">
                                    {{ post.description }}
                                </td>

                                <td class="p-4">
                                    <div
                                        class="flex justify-center gap-3"
                                    >

                                        <Link
                                            :href="`/posts/${post.id}/edit`"
                                            class="rounded bg-blue-100 px-3 py-1 text-sm font-medium text-blue-700 transition hover:bg-blue-200"
                                        >
                                            Edit
                                        </Link>

                                        <button
                                            type="button"
                                            @click="deletePost(post.id)"
                                            class="rounded bg-red-100 px-3 py-1 text-sm font-medium text-red-700 transition hover:bg-red-200"
                                        >
                                            Delete
                                        </button>

                                    </div>
                                </td>

                            </tr>

                            <!-- Empty state -->
                            <tr v-if="posts.length === 0">

                                <td
                                    colspan="4"
                                    class="p-10 text-center"
                                >
                                    <div
                                        class="text-gray-500"
                                    >
                                        <div class="mb-2 text-4xl">
                                            📝
                                        </div>

                                        <p class="font-medium">
                                            No posts found
                                        </p>

                                        <p class="mt-1 text-sm">
                                            Create your first post to get started.
                                        </p>

                                        <Link
                                            href="/posts/create"
                                            class="mt-4 inline-block rounded bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-700"
                                        >
                                            Create Post
                                        </Link>
                                    </div>
                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>
</template>