<?php

namespace App\Services\Contracts;

use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;

interface IPostService
{
    public function create(array $data): Post;

    public function find(int $id): ?Post;

    public function list(): Collection;

    public function update(int $id, array $data): Post;

    public function delete(int $id): bool;

    public function findByUser(int $id): Collection;
}