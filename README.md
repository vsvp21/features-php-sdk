# Feature Flags PHP SDK for Choco Internal Usage


Installation:
```bash
composer require vsvp21/features-php-sdk
```

if you want to apply feature flags to existing Laravel model, you can override __get method in your model like this:
```php
protected array $features = [
    'is_check_pulling_enabled'
];

// this will return value from model if it's not a feature, otherwise it will return value from feature flag
public function __get($key)
{
    if (in_array($key, $this->features)) {
        return app(Feature\Interfaces\ClientInterface::class)->getFeature($this->filialId, $key)->value;
    }

    return $this->getAttribute($key)
}
```

Bind interface to implementation in your service provider:
```php
public function register()
{
    $this->app->bind(\Feature\Interfaces\ClientInterface::class, function ($app) {
        return new \Feature\Client('host:port', 3.0);
    });
}
```