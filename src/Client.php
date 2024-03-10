<?php

declare(strict_types=1);

namespace Feature;

use Feature\Exceptions\FeatureNotFoundException;
use Feature\Exceptions\WrongTargetingKeyException;
use GuzzleHttp\Exception\RequestException;

class Client implements Interfaces\ClientInterface
{
    private \GuzzleHttp\Client $client;

    public function __construct(string $baseUri, float $timeout = 3.0)
    {
        $this->client = new \GuzzleHttp\Client([
            'base_uri' => $baseUri,
            'timeout'  => $timeout,
        ]);
    }

    public function getFeature(string $targetingKey, string $featureName): Feature
    {
        try {
            $response = $this->client->get("/feature-flags/targets/{$targetingKey}/features/{$featureName}");

            /** @var array<string, array<string, mixed>> $data */
            $data = json_decode($response->getBody()->getContents(), true);

            return new Feature($featureName, $targetingKey, $data['data']['value'] ?? false);
        } catch (RequestException $exception) {
            $this->handleException($exception);
        }

        return new Feature('', '', false);
    }

    private function handleException(RequestException $exception): void
    {
        $response = $exception->getResponse();
        if (! $response) {
            throw $exception;
        }

        if ($response->getStatusCode() === 404) {
            throw new FeatureNotFoundException($exception->getMessage());
        }

        if ($response->getStatusCode() === 400) {
            throw new WrongTargetingKeyException($exception->getMessage());
        }
    }
}
