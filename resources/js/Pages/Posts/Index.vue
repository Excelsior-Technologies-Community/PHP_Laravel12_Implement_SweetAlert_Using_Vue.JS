<script setup>

import { computed, ref, watch } from 'vue'

import { router, Link } from '@inertiajs/vue3'

import {
    confirmDelete,
    confirmRestore,
    confirmBulkDelete,
    loadingAlert,
    closeAlert,
    toastAlert
} from '@/plugins/sweetalert'


/*
|--------------------------------------------------------------------------
| Props
|--------------------------------------------------------------------------
*/

const props = defineProps({

    posts: {
        type: Object,

        default: () => ({
            data: [],
            links: [],
            meta: {}
        })
    },

    filters: {
        type: Object,

        default: () => ({
            search: '',
            sort_by: 'id',
            sort_order: 'asc',
            status: 'active'
        })
    },

    counts: {
        type: Object,

        default: () => ({
            total: 0,
            active: 0,
            trash: 0
        })
    }

})


/*
|--------------------------------------------------------------------------
| Search
|--------------------------------------------------------------------------
*/

const search = ref(
    props.filters.search || ''
)


/*
|--------------------------------------------------------------------------
| Sort
|--------------------------------------------------------------------------
*/

const sortBy = ref(
    props.filters.sort_by || 'id'
)

const sortOrder = ref(
    props.filters.sort_order || 'asc'
)


/*
|--------------------------------------------------------------------------
| Status
|--------------------------------------------------------------------------
*/

const status = ref(
    props.filters.status || 'active'
)


/*
|--------------------------------------------------------------------------
| Selected posts
|--------------------------------------------------------------------------
*/

const selectedPosts = ref([])


/*
|--------------------------------------------------------------------------
| Check all
|--------------------------------------------------------------------------
*/

const checkAll = ref(false)


/*
|--------------------------------------------------------------------------
| Is current page fully selected?
|--------------------------------------------------------------------------
*/

const allCurrentPageSelected = computed(() => {

    if (!props.posts.data.length) {
        return false
    }

    return props.posts.data.every(post =>
        selectedPosts.value.includes(post.id)
    )

})


/*
|--------------------------------------------------------------------------
| Toggle single checkbox
|--------------------------------------------------------------------------
*/

const togglePost = (id) => {

    if (selectedPosts.value.includes(id)) {

        selectedPosts.value =
            selectedPosts.value.filter(
                postId => postId !== id
            )

    } else {

        selectedPosts.value.push(id)

    }

    checkAll.value =
        allCurrentPageSelected.value

}


/*
|--------------------------------------------------------------------------
| Toggle all
|--------------------------------------------------------------------------
*/

const toggleAll = () => {

    if (checkAll.value) {

        const currentIds =
            props.posts.data.map(post => post.id)

        selectedPosts.value = [
            ...new Set([
                ...selectedPosts.value,
                ...currentIds
            ])
        ]

    } else {

        const currentIds =
            props.posts.data.map(post => post.id)

        selectedPosts.value =
            selectedPosts.value.filter(
                id => !currentIds.includes(id)
            )

    }

}


/*
|--------------------------------------------------------------------------
| Apply filters
|--------------------------------------------------------------------------
*/

const applyFilters = () => {

    router.get(
        '/posts',
        {
            search: search.value || undefined,

            sort_by: sortBy.value,

            sort_order: sortOrder.value,

            status: status.value
        },
        {
            preserveState: true,

            preserveScroll: true,

            replace: true,

            onSuccess: () => {

                selectedPosts.value = []

                checkAll.value = false

            }
        }
    )

}


/*
|--------------------------------------------------------------------------
| Search watcher
|--------------------------------------------------------------------------
*/

let searchTimer = null

watch(search, () => {

    clearTimeout(searchTimer)

    searchTimer = setTimeout(() => {

        applyFilters()

    }, 400)

})


/*
|--------------------------------------------------------------------------
| Sort
|--------------------------------------------------------------------------
*/

const changeSort = (column) => {

    if (sortBy.value === column) {

        sortOrder.value =
            sortOrder.value === 'asc'
                ? 'desc'
                : 'asc'

    } else {

        sortBy.value = column

        sortOrder.value = 'asc'

    }

    applyFilters()

}


/*
|--------------------------------------------------------------------------
| Status filter
|--------------------------------------------------------------------------
*/

const changeStatus = () => {

    applyFilters()

}


/*
|--------------------------------------------------------------------------
| Delete single post
|--------------------------------------------------------------------------
*/

const deletePost = async (id) => {

    const result = await confirmDelete()

    if (!result.isConfirmed) {
        return
    }

    loadingAlert('Moving post to trash...')

    router.delete(
        `/posts/${id}`,
        {

            preserveScroll: true,

            onError: () => {

                closeAlert()

                toastAlert(
                    'Unable to delete the post.',
                    'error'
                )

            },

            onFinish: () => {

                closeAlert()

            }

        }
    )

}


/*
|--------------------------------------------------------------------------
| Restore post
|--------------------------------------------------------------------------
*/

const restorePost = async (id) => {

    const result = await confirmRestore()

    if (!result.isConfirmed) {
        return
    }

    loadingAlert('Restoring post...')

    router.patch(
        `/posts/${id}/restore`,
        {},
        {

            preserveScroll: true,

            onError: () => {

                closeAlert()

                toastAlert(
                    'Unable to restore the post.',
                    'error'
                )

            },

            onFinish: () => {

                closeAlert()

            }

        }
    )

}


/*
|--------------------------------------------------------------------------
| Bulk delete
|--------------------------------------------------------------------------
*/

const bulkDelete = async () => {

    if (!selectedPosts.value.length) {

        toastAlert(
            'Please select at least one post.',
            'warning'
        )

        return

    }

    const result = await confirmBulkDelete(
        selectedPosts.value.length
    )

    if (!result.isConfirmed) {
        return
    }

    loadingAlert('Deleting selected posts...')

    router.delete(
        '/posts-bulk-delete',
        {

            data: {
                ids: selectedPosts.value
            },

            preserveScroll: true,

            onSuccess: () => {

                selectedPosts.value = []

                checkAll.value = false

            },

            onError: () => {

                closeAlert()

                toastAlert(
                    'Unable to delete selected posts.',
                    'error'
                )

            },

            onFinish: () => {

                closeAlert()

            }

        }
    )

}


/*
|--------------------------------------------------------------------------
| CSV Export
|--------------------------------------------------------------------------
*/

const exportCsv = () => {

    const params = new URLSearchParams()

    if (search.value) {

        params.append(
            'search',
            search.value
        )

    }

    params.append(
        'sort_by',
        sortBy.value
    )

    params.append(
        'sort_order',
        sortOrder.value
    )

    params.append(
        'status',
        status.value
    )

    window.location.href =
        `/posts-export?${params.toString()}`

}


/*
|--------------------------------------------------------------------------
| Clear search
|--------------------------------------------------------------------------
*/

const clearSearch = () => {

    search.value = ''

}


/*
|--------------------------------------------------------------------------
| Sort icon
|--------------------------------------------------------------------------
*/

const sortIcon = (column) => {

    if (sortBy.value !== column) {
        return '↕'
    }

    return sortOrder.value === 'asc'
        ? '↑'
        : '↓'

}

</script>


<template>

    <div class="min-h-screen bg-gray-100 py-10">

        <div class="mx-auto max-w-7xl px-4">


            <!-- ========================================================= -->
            <!-- Header -->
            <!-- ========================================================= -->

            <div
                class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between"
            >

                <div>

                    <h1
                        class="text-3xl font-bold text-gray-800"
                    >
                        Posts
                    </h1>

                    <p
                        class="mt-1 text-sm text-gray-500"
                    >
                        Manage your posts using Laravel,
                        Vue and SweetAlert2.
                    </p>

                </div>


                <Link
                    href="/posts/create"
                    class="rounded bg-blue-600 px-5 py-2 text-center font-medium text-white transition hover:bg-blue-700"
                >
                    + Add Post
                </Link>

            </div>


            <!-- ========================================================= -->
            <!-- Statistics -->
            <!-- ========================================================= -->

            <div
                class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3"
            >

                <!-- Total -->

                <div
                    class="rounded-lg bg-white p-5 shadow"
                >

                    <p
                        class="text-sm font-medium text-gray-500"
                    >
                        Total Posts
                    </p>

                    <p
                        class="mt-2 text-3xl font-bold text-gray-800"
                    >
                        {{ counts.total }}
                    </p>

                </div>


                <!-- Active -->

                <div
                    class="rounded-lg bg-white p-5 shadow"
                >

                    <p
                        class="text-sm font-medium text-gray-500"
                    >
                        Active Posts
                    </p>

                    <p
                        class="mt-2 text-3xl font-bold text-green-600"
                    >
                        {{ counts.active }}
                    </p>

                </div>


                <!-- Trash -->

                <div
                    class="rounded-lg bg-white p-5 shadow"
                >

                    <p
                        class="text-sm font-medium text-gray-500"
                    >
                        Trash
                    </p>

                    <p
                        class="mt-2 text-3xl font-bold text-red-600"
                    >
                        {{ counts.trash }}
                    </p>

                </div>

            </div>


            <!-- ========================================================= -->
            <!-- Search + Filters -->
            <!-- ========================================================= -->

            <div
                class="mb-6 rounded-lg bg-white p-5 shadow"
            >

                <div
                    class="flex flex-col gap-4 lg:flex-row lg:items-end"
                >


                    <!-- Search -->

                    <div class="flex-1">

                        <label
                            class="mb-1 block text-sm font-medium text-gray-700"
                        >
                            Search Posts
                        </label>

                        <div class="relative">

                            <input
                                v-model="search"
                                type="text"
                                placeholder="Search title or description..."
                                class="w-full rounded-lg border border-gray-300 px-4 py-2 pr-10 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                            />

                            <button
                                v-if="search"
                                type="button"
                                @click="clearSearch"
                                class="absolute right-3 top-2 text-gray-400 hover:text-gray-700"
                            >
                                ×
                            </button>

                        </div>

                    </div>


                    <!-- Status -->

                    <div class="w-full lg:w-48">

                        <label
                            class="mb-1 block text-sm font-medium text-gray-700"
                        >
                            Status
                        </label>

                        <select
                            v-model="status"
                            @change="changeStatus"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                        >

                            <option value="active">
                                Active
                            </option>

                            <option value="all">
                                All Posts
                            </option>

                            <option value="trash">
                                Trash
                            </option>

                        </select>

                    </div>


                    <!-- Export -->

                    <button
                        type="button"
                        @click="exportCsv"
                        class="rounded-lg bg-green-600 px-5 py-2 font-medium text-white transition hover:bg-green-700"
                    >
                        ↓ Export CSV
                    </button>

                </div>


                <!-- Bulk actions -->

                <div
                    v-if="selectedPosts.length > 0"
                    class="mt-4 flex items-center justify-between rounded-lg bg-red-50 p-4"
                >

                    <span
                        class="font-medium text-red-700"
                    >
                        {{ selectedPosts.length }}
                        post(s) selected
                    </span>

                    <button
                        type="button"
                        @click="bulkDelete"
                        class="rounded bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700"
                    >
                        Delete Selected
                    </button>

                </div>

            </div>


            <!-- ========================================================= -->
            <!-- Table -->
            <!-- ========================================================= -->

            <div
                class="overflow-hidden rounded-lg bg-white shadow"
            >

                <div class="overflow-x-auto">

                    <table class="w-full">


                        <!-- Table Header -->

                        <thead>

                            <tr
                                class="border-b bg-gray-50 text-left"
                            >

                                <!-- Checkbox -->

                                <th class="w-12 p-4">

                                    <input
                                        type="checkbox"
                                        v-model="checkAll"
                                        @change="toggleAll"
                                        :disabled="posts.data.length === 0"
                                        class="h-4 w-4 rounded border-gray-300 text-blue-600"
                                    />

                                </th>


                                <!-- ID -->

                                <th
                                    class="p-4 font-semibold text-gray-700"
                                >

                                    <button
                                        type="button"
                                        @click="changeSort('id')"
                                        class="flex items-center gap-1 hover:text-blue-600"
                                    >
                                        #
                                        <span>
                                            {{ sortIcon('id') }}
                                        </span>
                                    </button>

                                </th>


                                <!-- Title -->

                                <th
                                    class="p-4 font-semibold text-gray-700"
                                >

                                    <button
                                        type="button"
                                        @click="changeSort('title')"
                                        class="flex items-center gap-1 hover:text-blue-600"
                                    >
                                        Title

                                        <span>
                                            {{ sortIcon('title') }}
                                        </span>

                                    </button>

                                </th>


                                <!-- Description -->

                                <th
                                    class="p-4 font-semibold text-gray-700"
                                >
                                    Description
                                </th>


                                <!-- Date -->

                                <th
                                    class="p-4 font-semibold text-gray-700"
                                >

                                    <button
                                        type="button"
                                        @click="changeSort('created_at')"
                                        class="flex items-center gap-1 hover:text-blue-600"
                                    >
                                        Created

                                        <span>
                                            {{ sortIcon('created_at') }}
                                        </span>

                                    </button>

                                </th>


                                <!-- Status -->

                                <th
                                    class="p-4 text-center font-semibold text-gray-700"
                                >
                                    Status
                                </th>


                                <!-- Action -->

                                <th
                                    class="p-4 text-center font-semibold text-gray-700"
                                >
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <!-- Table Body -->

                        <tbody>

                            <tr
                                v-for="post in posts.data"
                                :key="post.id"
                                class="border-b transition hover:bg-gray-50"
                            >

                                <!-- Checkbox -->

                                <td class="p-4">

                                    <input
                                        type="checkbox"
                                        :checked="selectedPosts.includes(post.id)"
                                        @change="togglePost(post.id)"
                                        class="h-4 w-4 rounded border-gray-300 text-blue-600"
                                    />

                                </td>


                                <!-- ID -->

                                <td
                                    class="p-4 text-gray-500"
                                >
                                    {{ post.id }}
                                </td>


                                <!-- Title -->

                                <td
                                    class="p-4 font-medium text-gray-800"
                                >
                                    {{ post.title }}
                                </td>


                                <!-- Description -->

                                <td
                                    class="max-w-md p-4 text-gray-600"
                                >
                                    <div
                                        class="line-clamp-2"
                                    >
                                        {{ post.description }}
                                    </div>
                                </td>


                                <!-- Created -->

                                <td
                                    class="whitespace-nowrap p-4 text-sm text-gray-500"
                                >
                                    {{
                                        new Date(
                                            post.created_at
                                        ).toLocaleDateString()
                                    }}
                                </td>


                                <!-- Status -->

                                <td
                                    class="p-4 text-center"
                                >

                                    <span
                                        v-if="!post.deleted_at"
                                        class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700"
                                    >
                                        Active
                                    </span>

                                    <span
                                        v-else
                                        class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700"
                                    >
                                        Deleted
                                    </span>

                                </td>


                                <!-- Actions -->

                                <td class="p-4">

                                    <div
                                        class="flex justify-center gap-2"
                                    >


                                        <!-- Active -->

                                        <template
                                            v-if="!post.deleted_at"
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

                                        </template>


                                        <!-- Deleted -->

                                        <template
                                            v-else
                                        >

                                            <button
                                                type="button"
                                                @click="restorePost(post.id)"
                                                class="rounded bg-green-100 px-3 py-1 text-sm font-medium text-green-700 transition hover:bg-green-200"
                                            >
                                                Restore
                                            </button>

                                        </template>

                                    </div>

                                </td>

                            </tr>


                            <!-- Empty -->

                            <tr
                                v-if="posts.data.length === 0"
                            >

                                <td
                                    colspan="7"
                                    class="p-10 text-center"
                                >

                                    <div
                                        class="text-gray-500"
                                    >

                                        <div
                                            class="mb-2 text-4xl"
                                        >
                                            📝
                                        </div>

                                        <p
                                            class="font-medium"
                                        >
                                            No posts found
                                        </p>

                                        <p
                                            class="mt-1 text-sm"
                                        >
                                            Try another search or
                                            create a new post.
                                        </p>

                                        <Link
                                            v-if="status !== 'trash'"
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


                <!-- ===================================================== -->
                <!-- Pagination -->
                <!-- ===================================================== -->

                <div
                    v-if="posts.links && posts.links.length > 3"
                    class="flex flex-col items-center justify-between gap-4 border-t p-5 sm:flex-row"
                >

                    <p
                        v-if="posts.meta"
                        class="text-sm text-gray-500"
                    >
                        Showing
                        <span class="font-medium">
                            {{ posts.meta.from || 0 }}
                        </span>
                        to
                        <span class="font-medium">
                            {{ posts.meta.to || 0 }}
                        </span>
                        of
                        <span class="font-medium">
                            {{ posts.meta.total || 0 }}
                        </span>
                        posts
                    </p>


                    <div
                        class="flex flex-wrap gap-1"
                    >

                        <Link
                            v-for="link in posts.links"
                            :key="link.label"
                            :href="link.url || '#'"
                            v-html="link.label"
                            preserve-state
                            preserve-scroll
                            class="rounded border px-3 py-2 text-sm transition"
                            :class="{
                                'bg-blue-600 text-white border-blue-600':
                                    link.active,

                                'bg-white text-gray-700 hover:bg-gray-100':
                                    !link.active && link.url,

                                'cursor-not-allowed opacity-40':
                                    !link.url
                            }"
                        />

                    </div>

                </div>

            </div>

        </div>

    </div>

</template>