<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_api_posts_endpoint_returns_successful_response(): void
    {
        $response = $this->get('/api/posts');

        $response->assertStatus(200);
        $this->assertStringContainsString('application/json', (string) $response->headers->get('content-type'));
    }
}
