<?php

declare(strict_types=1);

namespace Feature;

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Throwable;

final class RetryDecider
{
    public function __construct(
        private readonly int $tries = 3,
    ) {
    }

    public function __invoke(
        int $retries,
        Request $request,
        Response $response = null,
        Throwable $exception = null
    ): bool {
        if ($retries >= $this->tries) {
            return false;
        }

        if ($exception instanceof ConnectException) {
            return true;
        }

        if ($response && $response->getStatusCode() >= 500) {
            return true;
        }

        return false;
    }
}
