<?php

namespace App\Providers;

use App\Support\SentryBeforeSend;
use Illuminate\Support\ServiceProvider;
use Sentry\SentrySdk;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $client = SentrySdk::getCurrentHub()->getClient();

        if ($client !== null) {
            $client->getOptions()->setBeforeSendCallback(new SentryBeforeSend());
        }
    }
}
