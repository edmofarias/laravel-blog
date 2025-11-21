<?php

namespace Tests\Unit\Services;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Services\CommentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\UnauthorizedException;
use Tests\TestCase;

class CommentServiceTest extends TestCase
{
    use RefreshDatabase;

    private CommentService $commentService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->commentService = new CommentService();
    }

    /** @test */
    public function it_lists_comments_for_a_post()
    {
        // Arrange
        $post = Post::factory()->create();
        $comments = Comment::factory()->count(3)->create([
            'post_id' => $post->id
        ]);

        // Act
        $result = $this->commentService->list($post->id);

        // Assert
        $this->assertNotNull($result);
        $this->assertInstanceOf(Post::class, $result);
        $this->assertEquals($post->id, $result->id);
        $this->assertCount(3, $result->comments);
    }

    /** @test */
    public function it_returns_null_when_listing_comments_for_non_existent_post()
    {
        // Act
        $result = $this->commentService->list(999);

        // Assert
        $this->assertNull($result);
    }

    /** @test */
    public function it_creates_a_comment_successfully()
    {
        // Arrange
        $user = User::factory()->create();
        $post = Post::factory()->create();
        Auth::shouldReceive('id')->andReturn($user->id);

        $data = ['content' => 'This is a test comment'];

        // Act
        $result = $this->commentService->create($data, $post->id);

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseHas('comments', [
            'content' => 'This is a test comment',
            'post_id' => $post->id,
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function it_throws_validation_exception_when_creating_comment_without_content()
    {
        // Arrange
        $user = User::factory()->create();
        $post = Post::factory()->create();
        Auth::shouldReceive('id')->andReturn($user->id);

        $data = [];

        // Assert
        $this->expectException(ValidationException::class);

        // Act
        $this->commentService->create($data, $post->id);
    }

    /** @test */
    public function it_throws_validation_exception_when_creating_comment_with_empty_content()
    {
        // Arrange
        $user = User::factory()->create();
        $post = Post::factory()->create();
        Auth::shouldReceive('id')->andReturn($user->id);

        $data = ['content' => ''];

        // Assert
        $this->expectException(ValidationException::class);

        // Act
        $this->commentService->create($data, $post->id);
    }

    /** @test */
    public function it_updates_a_comment_successfully()
    {
        // Arrange
        $user = User::factory()->create();
        $comment = Comment::factory()->create(['user_id' => $user->id]);
        Auth::shouldReceive('id')->andReturn($user->id);

        $data = ['content' => 'Updated comment content'];

        // Act
        $result = $this->commentService->update($comment->id, $data);

        // Assert
        $this->assertNotNull($result);
        $this->assertInstanceOf(Comment::class, $result);
        $this->assertEquals('Updated comment content', $result->content);
        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'content' => 'Updated comment content',
        ]);
    }

    /** @test */
    public function it_returns_null_when_updating_non_existent_comment()
    {
        // Arrange
        $user = User::factory()->create();
        Auth::shouldReceive('id')->andReturn($user->id);

        $data = ['content' => 'Updated content'];

        // Act
        $result = $this->commentService->update(999, $data);

        // Assert
        $this->assertNull($result);
    }

    /** @test */
    public function it_throws_unauthorized_exception_when_updating_another_users_comment()
    {
        // Arrange
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $comment = Comment::factory()->create(['user_id' => $owner->id]);
        Auth::shouldReceive('id')->andReturn($otherUser->id);

        $data = ['content' => 'Updated content'];

        // Assert
        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessage('You are not authorized to update this comment.');

        // Act
        $this->commentService->update($comment->id, $data);
    }

    /** @test */
    public function it_throws_validation_exception_when_updating_comment_without_content()
    {
        // Arrange
        $user = User::factory()->create();
        $comment = Comment::factory()->create(['user_id' => $user->id]);
        Auth::shouldReceive('id')->andReturn($user->id);

        $data = [];

        // Assert
        $this->expectException(ValidationException::class);

        // Act
        $this->commentService->update($comment->id, $data);
    }

    /** @test */
    public function it_deletes_a_comment_successfully()
    {
        // Arrange
        $user = User::factory()->create();
        $comment = Comment::factory()->create(['user_id' => $user->id]);
        Auth::shouldReceive('id')->andReturn($user->id);

        // Act
        $result = $this->commentService->delete($comment->id);

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseMissing('comments', [
            'id' => $comment->id,
        ]);
    }

    /** @test */
    public function it_returns_false_when_deleting_non_existent_comment()
    {
        // Arrange
        $user = User::factory()->create();
        Auth::shouldReceive('id')->andReturn($user->id);

        // Act
        $result = $this->commentService->delete(999);

        // Assert
        $this->assertFalse($result);
    }

    /** @test */
    public function it_throws_unauthorized_exception_when_deleting_another_users_comment()
    {
        // Arrange
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $comment = Comment::factory()->create(['user_id' => $owner->id]);
        Auth::shouldReceive('id')->andReturn($otherUser->id);

        // Assert
        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessage('You are not authorized to delete this comment.');

        // Act
        $this->commentService->delete($comment->id);
    }
}