<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PostController extends Controller
{
    // Show list
    public function index()
    {
        return Inertia::render('Posts/Index', [
            'posts' => Post::latest()->get()
        ]);
    }

    // Show create form
    public function create()
    {
        return Inertia::render('Posts/Create');
    }

    // Store post
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
        ]);

        Post::create($request->all());

        return redirect()->route('posts.index')
            ->with('success', 'Post created successfully');
    }

    // Show edit form
    public function edit(Post $post)
    {
        return Inertia::render('Posts/Edit', [
            'post' => $post
        ]);
    }

    // Update post
    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
        ]);

        $post->update($request->all());

        return redirect()->route('posts.index')
            ->with('success', 'Post updated successfully');
    }

    // Delete post
    public function destroy(Post $post)
    {
        $post->delete();

        return back()->with('success', 'Post deleted successfully');
    }
}
