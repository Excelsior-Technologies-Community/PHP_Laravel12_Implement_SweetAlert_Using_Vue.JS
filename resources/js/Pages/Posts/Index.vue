<script setup>
// Import Inertia helpers
import { Link, router } from '@inertiajs/vue3'

// Import SweetAlert delete confirmation helper
import { confirmDelete } from '@/plugins/sweetalert'

// Receive posts from controller as props
defineProps({
    posts: Array
})

/**
 * Delete post after SweetAlert confirmation
 */
const deletePost = async (id) => {
    const result = await confirmDelete()

    // If user confirms deletion
    if (result.isConfirmed) {
        router.delete(`/posts/${id}`)
        // Success SweetAlert will be shown via
        // controller flash + app.js global SweetAlert
    }
}
</script>

<template>
    <div class="p-6">
        <h1 class="text-2xl mb-4">Posts</h1>

        <!-- Navigate to create page -->
        <Link
            href="/posts/create"
            class="bg-blue-600 text-white px-4 py-2 rounded"
        >
            Add Post
        </Link>

        <!-- Posts table -->
        <table class="w-full mt-4 border">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border p-2">Title</th>
                    <th class="border p-2">Description</th>
                    <th class="border p-2">Action</th>
                </tr>
            </thead>

            <tbody>
                <!-- Loop through posts -->
                <tr v-for="post in posts" :key="post.id">
                    <td class="border p-2">{{ post.title }}</td>
                    <td class="border p-2">{{ post.description }}</td>
                    <td class="border p-2">
                        <!-- Edit link -->
                        <Link
                            :href="`/posts/${post.id}/edit`"
                            class="text-blue-600 mr-2"
                        >
                            Edit
                        </Link>

                        <!-- Delete button -->
                        <button
                            @click="deletePost(post.id)"
                            class="text-red-600"
                        >
                            Delete
                        </button>
                    </td>
                </tr>

                <!-- Empty state -->
                <tr v-if="posts.length === 0">
                    <td colspan="3" class="text-center p-4">
                        No posts found
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
