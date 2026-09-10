<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({
            total: 0,
            active: 0,
            trash: 0,
            recent: [],
        }),
    },
})
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-8">

                <!-- Welcome -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <p class="text-lg font-medium">Welcome back!</p>
                        <p class="mt-1 text-sm text-gray-500">
                            Here's an overview of your posts.
                        </p>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div class="rounded-lg bg-white p-5 shadow">
                        <p class="text-sm font-medium text-gray-500">
                            Total Posts
                        </p>
                        <p class="mt-2 text-3xl font-bold text-gray-800">
                            {{ stats.total }}
                        </p>
                        <Link
                            href="/posts"
                            class="mt-3 inline-block text-sm text-blue-600 hover:text-blue-800"
                        >
                            View all posts →
                        </Link>
                    </div>

                    <div class="rounded-lg bg-white p-5 shadow">
                        <p class="text-sm font-medium text-gray-500">
                            Active Posts
                        </p>
                        <p class="mt-2 text-3xl font-bold text-green-600">
                            {{ stats.active }}
                        </p>
                        <Link
                            href="/posts?status=active"
                            class="mt-3 inline-block text-sm text-green-600 hover:text-green-800"
                        >
                            View active →
                        </Link>
                    </div>

                    <div class="rounded-lg bg-white p-5 shadow">
                        <p class="text-sm font-medium text-gray-500">
                            Trash
                        </p>
                        <p class="mt-2 text-3xl font-bold text-red-600">
                            {{ stats.trash }}
                        </p>
                        <Link
                            href="/posts?status=trash"
                            class="mt-3 inline-block text-sm text-red-600 hover:text-red-800"
                        >
                            View trash →
                        </Link>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <Link
                        href="/posts/create"
                        class="rounded-lg bg-blue-600 p-5 text-center font-medium text-white shadow transition hover:bg-blue-700"
                    >
                        + Add New Post
                    </Link>

                    <Link
                        href="/posts"
                        class="rounded-lg bg-green-600 p-5 text-center font-medium text-white shadow transition hover:bg-green-700"
                    >
                        ↓ Export CSV
                    </Link>

                    <Link
                        href="/posts?status=all"
                        class="rounded-lg bg-gray-700 p-5 text-center font-medium text-white shadow transition hover:bg-gray-800"
                    >
                        View All Posts
                    </Link>
                </div>

                <!-- Recent Posts -->
                <div v-if="stats.recent && stats.recent.length" class="overflow-hidden bg-white shadow sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-800">
                            Recent Posts
                        </h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Last 5 posts you've created.
                        </p>

                        <div class="mt-4 divide-y">
                            <div
                                v-for="post in stats.recent"
                                :key="post.id"
                                class="flex items-center justify-between py-3"
                            >
                                <div>
                                    <p class="font-medium text-gray-800">
                                        {{ post.title }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        {{ new Date(post.created_at).toLocaleDateString() }}
                                    </p>
                                </div>
                                <Link
                                    :href="`/posts/${post.id}/edit`"
                                    class="rounded bg-blue-100 px-3 py-1 text-sm font-medium text-blue-700 hover:bg-blue-200"
                                >
                                    Edit
                                </Link>
                            </div>
                        </div>

                        <Link
                            href="/posts"
                            class="mt-4 inline-block text-sm text-blue-600 hover:text-blue-800"
                        >
                            View all posts →
                        </Link>
                    </div>
                </div>

                <!-- No posts yet -->
                <div v-else class="overflow-hidden bg-white shadow sm:rounded-lg">
                    <div class="p-10 text-center text-gray-500">
                        <div class="mb-2 text-4xl">📝</div>
                        <p class="font-medium">No posts yet</p>
                        <p class="mt-1 text-sm">
                            Get started by creating your first post.
                        </p>
                        <Link
                            href="/posts/create"
                            class="mt-4 inline-block rounded bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-700"
                        >
                            Create Post
                        </Link>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>