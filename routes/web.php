<?php

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

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
