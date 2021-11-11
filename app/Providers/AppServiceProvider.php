<?php

namespace App\Providers;

use App\Repositories\Feed\FeedRepository;
use App\Repositories\Feed\FeedRepositoryInterface;
use App\RequestManagers\FeedRequestManager;
use App\Requests\AbstractHttpRequest;
use App\Requests\HttpRequestInterface;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(
            FeedRepositoryInterface::class,
            FeedRepository::class
        );
        $this->app->singleton(
            ClientInterface::class,
            Client::class
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
    }
}
