<?php

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {

    $totalPosts = \App\Models\Post::withTrashed()->count();

    $activePosts = \App\Models\Post::count();

    $deletedPosts = \App\Models\Post::onlyTrashed()->count();

    $recentPosts = \App\Models\Post::orderByDesc('created_at')->limit(5)->get(['id', 'title', 'created_at']);

    return Inertia::render('Dashboard', [
        'stats' => [
            'total'  => $totalPosts,
            'active' => $activePosts,
            'trash'  => $deletedPosts,
            'recent' => $recentPosts,
        ],
    ]);

})->middleware('auth')->name('dashboard');


/*
|--------------------------------------------------------------------------
| Post Routes
|--------------------------------------------------------------------------
*/

Route::get('/posts', [
    PostController::class,
    'index'
])->name('posts.index');


Route::get('/posts/create', [
    PostController::class,
    'create'
])->name('posts.create');


Route::post('/posts', [
    PostController::class,
    'store'
])->name('posts.store');


Route::get('/posts/{post}/edit', [
    PostController::class,
    'edit'
])->name('posts.edit');


Route::put('/posts/{post}', [
    PostController::class,
    'update'
])->name('posts.update');


/*
|--------------------------------------------------------------------------
| Soft Delete
|--------------------------------------------------------------------------
*/

Route::delete('/posts/{post}', [
    PostController::class,
    'destroy'
])->name('posts.destroy');


/*
|--------------------------------------------------------------------------
| Restore
|--------------------------------------------------------------------------
*/

Route::patch('/posts/{post}/restore', [
    PostController::class,
    'restore'
])->name('posts.restore');


/*
|--------------------------------------------------------------------------
| Bulk Delete
|--------------------------------------------------------------------------
*/

Route::delete('/posts-bulk-delete', [
    PostController::class,
    'bulkDestroy'
])->name('posts.bulk-destroy');


/*
|--------------------------------------------------------------------------
| CSV Export
|--------------------------------------------------------------------------
*/

Route::get('/posts-export', [
    PostController::class,
    'export'
])->name('posts.export');


require __DIR__ . '/auth.php';
