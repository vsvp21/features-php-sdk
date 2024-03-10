<?php

declare(strict_types=1);

namespace Feature\Interfaces;

use Feature\Feature;

interface ClientInterface
{
    public function getFeature(string $targetingKey, string $featureName): Feature;
}
