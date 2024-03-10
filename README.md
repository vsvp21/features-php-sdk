# Feature Flags PHP SDK for Choco Internal Usage


Installation:
```bash
composer require vsvp21/features-php-sdk
```

Publish config:
```bash
php artisan vendor:publish --provider="Feature\ServiceProvider"
```

Add service provider to app.php config:
```php
// config/app.php

// other providers
\Feature\ServiceProvider::class,
// other providers
```

Usage:

```php
// model class
use Feature\HasFeaturesTrait;

// your targeting key field identifier
protected function targetingKeyField(): string
{
    return 'your_targeting_key';
}

// your list of features
protected function featureFlags(): array
{
    return [
        'feature1',
        'feature2',
    ];
}
```

Usage without model:
```php
use Feature\Interfaces\ClientInterface::class;

$featureEnabled = app(ClientInterface::class)->getFeature('your_feature')->value;

if ($featureEnabled) {
    // some code
}
```
