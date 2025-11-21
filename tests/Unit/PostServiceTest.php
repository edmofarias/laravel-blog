<?php

use App\Models\Comment;
use App\Models\Post;
use PHPUnit\Framework\TestCase;
use Mockery;
use App\Services\PostService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\UnauthorizedException;

class PostServiceTest extends TestCase
{

    public function test_find_returns_post_with_comments()
    {
        $id = 42;
        $post = new Post([
            'title' => 'Sample Post',
            'content' => 'This is a sample post.',
        ]);

        //$mock = $this->createMock(Post::class);

        $postFacade = Mockery::mock('alias:App\Models\Post');
        $postFacade->shouldReceive('with')->once()->with(['comments'])->andReturnSelf();
        $postFacade->shouldReceive('find')->once()->with($id)->andReturn($post);

        $service = new PostService();
        $result = $service->find($id);

        $this->assertSame($post, $result);
    }
}