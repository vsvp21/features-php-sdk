<?php

declare(strict_types=1);

namespace Feature\Interfaces;

use Feature\Feature;
use GuzzleHttp\Exception\GuzzleException;

interface ClientInterface
{
    public function getFeature(string $targetingKey, string $featureName): Feature;

    /**
     * @param string $targetingKey
     * @return array<string, Feature>
     * @throws GuzzleException
     */
    public function getAllFeatures(string $targetingKey): array;

    /**
     * @param string[] $targetingKeys
     * @return array<string, array<string, Feature>>
     */
    public function getFeaturesForTargetingKeys(array $targetingKeys): array;

    /**
     * @param string $featureName
     * @return string[]
     */
    public function getTargetsByFeatureName(string $featureName): array;
}
