<?php

declare(strict_types=1);

namespace Feature;

final class Feature
{
    public function __construct(
        public readonly string $name,
        public readonly string $targetingKey,
        public readonly mixed $value,
    ) {
    }
}
