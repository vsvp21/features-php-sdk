<?php

declare(strict_types=1);

namespace Feature;

use Feature\Interfaces\ClientInterface;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider as SupportServiceProvider;

final class ServiceProvider extends SupportServiceProvider
{
    public function register()
    {
        $this->app->bind(ClientInterface::class, function () {
            return new Client(
                (string) Config::get('feature.url'),
                (float) Config::get('feature.timeout'),
                (int) Config::get('feature.max_tries'),
            );
        });
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/feature.php' => $this->app->configPath('feature.php'),
        ]);
    }
}
