<?php

namespace App\Providers;

use App\Services\CommentService;
use App\Services\Contracts\ICommentService;
use App\Services\Contracts\IPostService;
use App\Services\PostService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(IPostService::class, PostService::class);
        $this->app->singleton(ICommentService::class, CommentService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
