<?php

namespace App\Providers;

use App\Models\Advert;
use App\Models\Photo;
use App\Observers\AdvertObserver;
use App\Policies\PhotoPolicy;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(Client::class, function () {
            return ClientBuilder::create()
                ->setHosts(config('elasticsearch.hosts'))
                ->build();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Photo::class, PhotoPolicy::class);
        
        Advert::observe(AdvertObserver::class);
    }
}
