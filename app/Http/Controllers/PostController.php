<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PostController extends Controller
{
    /**
     * Display posts with:
     * - Search
     * - Sorting
     * - Pagination
     * - Active / Trash filter
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $sortBy = $request->input('sort_by', 'id');

        $sortOrder = $request->input('sort_order', 'asc');

        $status = $request->input('status', 'active');

        /*
        |--------------------------------------------------------------------------
        | Allowed sorting columns
        |--------------------------------------------------------------------------
        */

        $allowedSorts = [
            'id',
            'title',
            'created_at',
        ];

        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'id';
        }

        /*
        |--------------------------------------------------------------------------
        | Allowed sorting directions
        |--------------------------------------------------------------------------
        */

        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'asc';
        }

        /*
        |--------------------------------------------------------------------------
        | Query
        |--------------------------------------------------------------------------
        */

        $query = Post::query();

        /*
        |--------------------------------------------------------------------------
        | Active / Trash / All
        |--------------------------------------------------------------------------
        */

        if ($status === 'trash') {
            $query->onlyTrashed();
        } elseif ($status === 'all') {
            $query->withTrashed();
        }

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */

        $query->orderBy($sortBy, $sortOrder);

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        $posts = $query
            ->paginate(5)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Counts
        |--------------------------------------------------------------------------
        */

        $totalPosts = Post::withTrashed()->count();

        $activePosts = Post::count();

        $deletedPosts = Post::onlyTrashed()->count();

        return Inertia::render('Posts/Index', [
            'posts' => $posts,

            'filters' => [
                'search' => $search,
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder,
                'status' => $status,
            ],

            'counts' => [
                'total' => $totalPosts,
                'active' => $activePosts,
                'trash' => $deletedPosts,
            ],
        ]);
    }

    /**
     * Show create form.
     */
    public function create()
    {
        return Inertia::render('Posts/Create');
    }

    /**
     * Store post.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'required',
                'string',
            ],
        ]);

        Post::create($validated);

        return redirect()
            ->route('posts.index')
            ->with('success', 'Post created successfully.');
    }

    /**
     * Show edit form.
     */
    public function edit(Post $post)
    {
        return Inertia::render('Posts/Edit', [
            'post' => $post,
        ]);
    }

    /**
     * Update post.
     */
    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'required',
                'string',
            ],
        ]);

        $post->update($validated);

        return redirect()
            ->route('posts.index')
            ->with('success', 'Post updated successfully.');
    }

    /**
     * Soft delete post.
     */
    public function destroy(Post $post)
    {
        $post->delete();

        return back()->with(
            'success',
            'Post moved to trash successfully.'
        );
    }

    /**
     * Restore post from trash.
     */
    public function restore($id)
    {
        $post = Post::withTrashed()->findOrFail($id);

        $post->restore();

        return back()->with(
            'success',
            'Post restored successfully.'
        );
    }

    /**
     * Bulk delete posts.
     */
    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => [
                'required',
                'array',
                'min:1',
            ],

            'ids.*' => [
                'integer',
                'exists:posts,id',
            ],
        ]);

        Post::whereIn('id', $validated['ids'])->delete();

        return back()->with(
            'success',
            count($validated['ids']) . ' post(s) moved to trash.'
        );
    }

    /**
     * Export posts as CSV.
     */
    public function export(Request $request)
    {
        $search = $request->input('search');

        $sortBy = $request->input('sort_by', 'id');

        $sortOrder = $request->input('sort_order', 'asc');

        $status = $request->input('status', 'active');

        $allowedSorts = [
            'id',
            'title',
            'created_at',
        ];

        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'id';
        }

        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'asc';
        }

        $query = Post::query();

        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        if ($status === 'trash') {
            $query->onlyTrashed();
        } elseif ($status === 'all') {
            $query->withTrashed();
        }

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        $posts = $query
            ->orderBy($sortBy, $sortOrder)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | CSV Download
        |--------------------------------------------------------------------------
        */

        $filename = 'posts-' . now()->format('Y-m-d-H-i-s') . '.csv';

        return response()->streamDownload(function () use ($posts) {
            $handle = fopen('php://output', 'w');

            /*
            | CSV Header
            */

            fputcsv($handle, [
                'ID',
                'Title',
                'Description',
                'Status',
                'Created At',
                'Deleted At',
            ]);

            /*
            | CSV Data
            */

            foreach ($posts as $post) {
                fputcsv($handle, [
                    $post->id,
                    $post->title,
                    $post->description,
                    $post->deleted_at ? 'Deleted' : 'Active',
                    $post->created_at?->format('Y-m-d H:i:s'),
                    $post->deleted_at?->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
