<?php

namespace App\Services;

use App\Services\Contracts\IPostService;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\UnauthorizedException;

class PostService implements IPostService
{
	public function create(array $data): Post
	{
        $validated = Validator::make($data, [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ])->validate();

        $post = new Post($validated);

        $post->user_id = Auth::id();
        $post->save();

		return $post;
	}

	public function find(int $id): ?Post
	{
		return Post::with(['comments'])->find($id);
	}

    public function findByUser(int $userId): Collection
	{        
        return Post::where('user_id', $userId)->with('comments')->get();
	}

	public function list(): Collection
	{
        $posts = Post::All();
        $posts->load('comments');

		return $posts;
	}

    public function update(int $id, array $data): Post
    {
        $post = Post::find($id);

        if ($post->user_id !== Auth::id()) {
            throw new UnauthorizedException('You are not authorized to update this post.');
        }

        $validated = Validator::make($data, [
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
        ])->validate();

        $post->update($validated);

        return $post;
    }

	public function delete(int $id): bool
	{
        $post = Post::find($id);

        if ($post->user_id !== Auth::id()) {
            throw new UnauthorizedException('You are not authorized to update this post.');
        }

		return (bool) $post->delete();
	}
}