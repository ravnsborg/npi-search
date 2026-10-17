<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Throwable;
use UnexpectedValueException;

class ApiService
{
    public const RESULTS_PER_REQUEST = 50;

    protected string $baseUrl;

    protected string $version;

    protected int $timeout;

    public function __construct()
    {
        $this->baseUrl = config('npi_registry.url');
        $this->version = config('npi_registry.version');
        $this->timeout = config('npi_registry.timeout');
    }

    /**
     * Send a GET request to the API.
     *
     * @param  array<string, bool|float|int|string|null>  $queryParams
     * @return array{success: true, data: list<array<string, mixed>>, count: int, error: null}|array{success: false, data: null, count: 0, error: string}
     */
    public function get(array $queryParams = [], int $limit = self::RESULTS_PER_REQUEST, int $skip = 0): array
    {
        $returnPayload = [
            'success' => false,
            'data' => null,
            'count' => 0,
            'error' => null,
        ];

        $queryParams = array_merge($queryParams, [
            'limit' => $limit,
            'skip' => $skip,
            'version' => $this->version,
        ]);

        try {
            $cacheKey = 'npi_search_' . md5(http_build_query($queryParams));

            /** @var array{result_count?: int, results?: list<array<string, mixed>>, Errors?: list<array{description: string}>} $apiResponse */
            $apiResponse = Cache::remember($cacheKey, 3600, function () use ($queryParams) {
                return $this->request()
                    ->get($this->baseUrl, $queryParams)
                    ->throw()
                    ->json();
            });

            if (isset($apiResponse['result_count'])) {
                return [
                    'success' => true,
                    'data' => $apiResponse['results'] ?? [],
                    'count' => (int) $apiResponse['result_count'],
                    'error' => null,
                ];
            }

            Cache::forget($cacheKey);

            if (isset($apiResponse['Errors'])) {
                $errorMessages = collect($apiResponse['Errors'])
                    ->pluck('description')
                    ->implode(', ');

                throw new UnexpectedValueException($errorMessages);
            }

            throw new UnexpectedValueException('Unexpected API response.');
        } catch (UnexpectedValueException $e) {
            report($e);
            $returnPayload['error'] = $e->getMessage();
        } catch (ConnectionException $e) {
            report($e);
            $returnPayload['error'] = 'Unable to connect to the API.';
        } catch (Throwable $e) {
            report($e);
            $returnPayload['error'] = 'An unexpected error occurred.';
        }

        return $returnPayload;
    }

    /**
     * Build initial Http Request
     */
    private function request(): PendingRequest
    {
        return Http::timeout($this->timeout);
    }
}
