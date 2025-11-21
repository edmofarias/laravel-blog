<?php

namespace App\Services\Contracts;

use App\Models\Comment;
use App\Models\Post;

interface ICommentService
{
    public function create(array $data, int $postId): bool;

    public function list(int $postId): ?Post;

    public function update(int $id, array $data): ?Comment;

    public function delete(int $id): bool;
}