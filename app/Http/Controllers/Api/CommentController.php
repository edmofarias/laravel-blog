<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\HttpResponse;
use App\Services\Contracts\ICommentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\UnauthorizedException;
use Symfony\Component\HttpFoundation\Response;

class CommentController extends Controller
{
    use HttpResponse;

    protected ICommentService $commentService;
    
    public function __construct(ICommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    public function list(int $postId): JsonResponse
    {
        try {
            $post = $this->commentService->list($postId);
            
            if ($post === null) {
                return response()->json(['message' => 'Post not found'], Response::HTTP_NOT_FOUND);
            }

            return response()->json($post->comments);
        } catch (\Exception $e) {
            return $this->reponseError('Error fetching all posts: ' . $e->getMessage());
        }
    }

    public function store(Request $request, $postId)
    {
        try {
            $created = $this->commentService->create($request->all(), $postId);
            
            if (!$created) {
                return response()->json(['message' => 'Post not found'], Response::HTTP_NOT_FOUND);
            }

            return response()->json(['message' => 'Comment created'], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->reponseError('Comment creation failed: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $comment = $this->commentService->update($id, $request->all());

            if (!$comment) {
                return response()->json(['message' => 'Comment not found'], Response::HTTP_NOT_FOUND);
            }

            return response()->json($comment);
        } catch (UnauthorizedException $e) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        } catch (\Exception $e) {
            return $this->reponseError('Comment update failed: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $deleted = $this->commentService->delete($id);

            if (!$deleted) {
                return response()->json(['message' => 'Comment not found'], Response::HTTP_NOT_FOUND);
            }

            return response()->json(['message' => 'Comment deleted successfully']);
        } catch (UnauthorizedException $e) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        } catch (\Exception $e) {
            return $this->reponseError('Comment deletion failed: ' . $e->getMessage());
        }
    }
}
