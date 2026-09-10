<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PostsTest extends TestCase
{
    use RefreshDatabase;

    public function test_posts_index_returns_filtered_posts_and_counts(): void
    {
        $matchingPost = Post::create([
            'title' => 'Matching post',
            'description' => 'Searchable description',
        ]);

        $deletedPost = Post::create([
            'title' => 'Deleted post',
            'description' => 'Archived description',
        ]);
        $deletedPost->delete();

        $response = $this->get('/posts?search=Matching&status=active');

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Posts/Index')
            ->where('filters.search', 'Matching')
            ->where('filters.status', 'active')
            ->where('counts.total', 2)
            ->where('counts.active', 1)
            ->where('counts.trash', 1)
            ->where('posts.data.0.id', $matchingPost->id)
        );
    }

    public function test_post_can_be_deleted_restored_and_bulk_deleted(): void
    {
        $post = Post::create([
            'title' => 'Post one',
            'description' => 'Description one',
        ]);

        $secondPost = Post::create([
            'title' => 'Post two',
            'description' => 'Description two',
        ]);

        $this->delete("/posts/{$post->id}")
            ->assertSessionHas('success', 'Post moved to trash successfully.');

        $this->assertSoftDeleted('posts', ['id' => $post->id]);

        $this->patch("/posts/{$post->id}/restore")
            ->assertSessionHas('success', 'Post restored successfully.');

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'deleted_at' => null,
        ]);

        $this->delete('/posts-bulk-delete', [
            'ids' => [$post->id, $secondPost->id],
        ])->assertSessionHas('success', '2 post(s) moved to trash.');

        $this->assertSoftDeleted('posts', ['id' => $post->id]);
        $this->assertSoftDeleted('posts', ['id' => $secondPost->id]);
    }

    public function test_csv_export_contains_all_post_fields_and_status(): void
    {
        $post = Post::create([
            'title' => 'CSV post',
            'description' => 'CSV description',
        ]);

        $response = $this->get('/posts-export?status=active');

        $response->assertHeader('content-type', 'text/csv; charset=utf-8');
        $response->assertDownload();

        $content = $response->streamedContent();

        $this->assertStringContainsString(
            'ID,Title,Description,Status,"Created At","Deleted At"',
            $content
        );
        $this->assertStringContainsString(
            '"CSV post","CSV description",Active',
            $content
        );
    }
}