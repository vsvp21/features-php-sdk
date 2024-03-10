<?php

declare(strict_types=1);

namespace Feature;

use Feature\Interfaces\ClientInterface;

trait HasFeaturesTrait
{
    public function __get($key)
    {
        if (in_array($key, $this->featureFlags())) {
            return app(ClientInterface::class)->getFeature((string) $this->getAttribute($this->targetingKeyField()), $key)->value;
        }

        return $this->getAttribute($key);
    }

    protected function targetingKeyField(): string
    {
        return 'id';
    }

    protected function featureFlags(): array
    {
        return [];
    }
}
