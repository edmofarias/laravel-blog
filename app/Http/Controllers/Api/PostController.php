<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\HttpResponse;
use App\Services\Contracts\IPostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\UnauthorizedException;

class PostController extends Controller
{
    use HttpResponse;

    protected IPostService $postService;

    public function __construct(IPostService $postService)
    {
        $this->postService = $postService;
    }

    public function list(): JsonResponse
    {
        try {
            $posts = $this->postService->list();

            return response()->json($posts);
        } catch (\Exception $e) {
            return $this->reponseError('Error fetching all posts: ' . $e->getMessage());
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $post = $this->postService->find($id);

            if ($post === null) {
                return response()->json(['message' => 'Post not found'], Response::HTTP_NOT_FOUND);
            }

            return response()->json($post);
        } catch (\Exception $e) {
            return $this->reponseError('Error fetching post: ' . $e->getMessage());
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $post = $this->postService->create($request->all());
            
            return response()->json($post, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->reponseError('Post creation failed: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $post = $this->postService->update($id, $request->all());
            
            return response()->json($post);
        } catch (UnauthorizedException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_FORBIDDEN);
        } catch (\Exception $e) {
            return $this->reponseError('Post update failed: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        try {
            $this->postService->delete($id);

            return response()->json(['message' => 'Post deleted successfully']);
        } catch (UnauthorizedException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_FORBIDDEN);
        } catch (\Exception $e) {
            return $this->reponseError('Post deletion failed: ' . $e->getMessage());
        }
    }

    public function userPosts(Request $request, $userId): JsonResponse
    {
        try {
            $posts = $this->postService->findByUser($userId);

            return response()->json($posts);
        } catch (\Exception $e) {
            return $this->reponseError('Error fetching posts for user ' . $userId . ': ' . $e->getMessage());
        }
    }
}
