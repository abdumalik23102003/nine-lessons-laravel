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

        // Register SmsRuClient
        $this->app->singleton(\App\Services\Sms\SmsRuClient::class, function () {
            return new \App\Services\Sms\SmsRuClient(
                apiId: config('sms.sms_ru.api_id', ''),
                from: config('sms.sms_ru.from', 'SMS'),
                testMode: config('sms.sms_ru.test', false),
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register SmsRu notification channel
        \Illuminate\Support\Facades\Notification::extend('sms', function ($app) {
            return new \App\Channels\SmsRuChannel(
                $app->make(\App\Services\Sms\SmsRuClient::class)
            );
        });

        // Register SMS notification channel (fallback)
        \Illuminate\Support\Facades\Notification::extend('sms-mock', function ($app) {
            return new \App\Channels\SmsChannel();
        });

        Gate::policy(Photo::class, PhotoPolicy::class);
        
        Advert::observe(AdvertObserver::class);
    }
}
