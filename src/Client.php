<?php

declare(strict_types=1);

namespace Feature;

use Feature\Exceptions\FeatureNotFoundException;
use Feature\Exceptions\WrongTargetingKeyException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\RetryMiddleware;

class Client implements Interfaces\ClientInterface
{
    private \GuzzleHttp\Client $client;

    public function __construct(string $baseUri, float $timeout = 3.0, int $maxTries = 3)
    {
        $handlerStack = HandlerStack::create();
        /** @psalm-suppress MixedArgumentTypeCoercion */
        $handlerStack->push(Middleware::retry(new RetryDecider($maxTries), fn () => RetryMiddleware::exponentialDelay($maxTries)));

        $this->client = new \GuzzleHttp\Client([
            'handler' => $handlerStack,
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

    /**
     * @param string $targetingKey
     * @return array<string, Feature>
     * @throws GuzzleException
     */
    public function getAllFeatures(string $targetingKey): array
    {
        try {
            $response = $this->client->get("/feature-flags/targets/{$targetingKey}/features");

            /** @var array<string, array<string, mixed>> $data */
            $data = json_decode($response->getBody()->getContents(), true);

            /** @var array<string, Feature> $features */
            $features = [];
            /** @var array $feature */
            foreach ($data['data'] as $feature) {
                $features[(string) $feature['name']] = new Feature((string) $feature['name'], $targetingKey, $feature['value']);
            }

            return $features;
        } catch (RequestException $exception) {
            $this->handleException($exception);
        }

        return [];
    }

    /**
     * @param array $targetingKeys
     * @return array<string, array<string, Feature>>
     * @throws GuzzleException
     */
    public function getFeaturesForTargetingKeys(array $targetingKeys): array
    {
        try {
            $response = $this->client->get('/feature-flags/targets/features', [
                'query' => ['targeting_keys' => $targetingKeys],
            ]);

            /** @var array<string, array<string, mixed>> $data */
            $data = json_decode($response->getBody()->getContents(), true);

            /** @var array<string, array<string, Feature>> $features */
            $features = [];
            /** @var mixed $targetingKey */
            foreach ($targetingKeys as $targetingKey) {
                if (! array_key_exists((string) $targetingKey, $data['data'])) {
                    $features[(string) $targetingKey] = [];
                    continue;
                }

                /** @var array $feature */
                foreach ($data['data'][(string) $targetingKey] as $feature) {
                    $features[(string) $targetingKey][(string) $feature['name']] = new Feature((string) $feature['name'], (string) $targetingKey, $feature['value']);
                }
            }

            return $features;
        } catch (RequestException $exception) {
            $this->handleException($exception);
        }

        return [];
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
