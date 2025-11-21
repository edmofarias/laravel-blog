<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Post;
use App\Services\Contracts\ICommentService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\UnauthorizedException;

class CommentService implements ICommentService
{
    public function list(int $postId): ?Post
    {
        $post = Post::with('comments')->find($postId);

        if (!$post) {
            return null;
        }

        return $post;
    }

    public function create(array $data, int $postId): bool
    {
        $post = Post::find($postId);

        $validated = Validator::make($data, [
            'content' => 'required|string',
        ])->validate();
        
        $created = $post->comments()->create([
            'content' => $validated['content'],
            'user_id' => Auth::id(),
        ]);

        return (bool) $created;
    }

    public function update(int $id, array $data): ?Comment
    {
        $comment = Comment::find($id);

        if ($comment === null) {
            return null;
        }

        if ($comment->user_id !== Auth::id()) {
            throw new UnauthorizedException('You are not authorized to update this comment.');
        }

        $validated = Validator::make($data, [
            'content' => 'required|string',
        ])->validate();

        $comment->update($validated);

        return $comment;
    }

    public function delete(int $id): bool
    {
        $comment = Comment::find($id);

        if ($comment === null) {
            return false;
        }

        if ($comment->user_id !== Auth::id()) {
            throw new UnauthorizedException('You are not authorized to delete this comment.');
        }

        return (bool) $comment->delete();
    }
}