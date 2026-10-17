<?php

use App\Services\ApiService;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    config([
        'cache.default' => 'array',
        'npi_registry.url' => 'https://npiregistry.cms.hhs.gov/api/',
        'npi_registry.version' => '2.1',
        'npi_registry.timeout' => 10,
    ]);

    Cache::flush();
});

test('it returns registry results and sends the configured query parameters', function () {
    Http::fake([
        '*' => Http::response([
            'result_count' => '1',
            'results' => [['number' => '1234567890']],
        ]),
    ]);

    $result = app(ApiService::class)->get(
        ['number' => '1234567890'],
        limit: 25,
        skip: 10,
    );

    expect($result)->toBe([
        'success' => true,
        'data' => [['number' => '1234567890']],
        'count' => 1,
        'error' => null,
    ]);

    Http::assertSent(function (Request $request) {
        $url = parse_url($request->url());
        parse_str($url['query'] ?? '', $query);

        return ($url['scheme'] ?? null) === 'https'
            && ($url['host'] ?? null) === 'npiregistry.cms.hhs.gov'
            && ($url['path'] ?? null) === '/api/'
            && $query === [
                'number' => '1234567890',
                'limit' => '25',
                'skip' => '10',
                'version' => '2.1',
            ];
    });
});

test('it returns registry validation errors', function () {
    Http::fake([
        '*' => Http::response([
            'Errors' => [
                ['description' => 'Invalid NPI number.'],
                ['description' => 'Invalid state.'],
            ],
        ]),
    ]);

    expect(app(ApiService::class)->get(['number' => '123']))->toBe([
        'success' => false,
        'data' => null,
        'count' => 0,
        'error' => 'Invalid NPI number., Invalid state.',
    ]);
});

test('it returns a connection error when the registry cannot be reached', function () {
    Http::fake([
        '*' => Http::failedConnection('Connection refused'),
    ]);

    expect(app(ApiService::class)->get(['city' => 'Austin']))->toBe([
        'success' => false,
        'data' => null,
        'count' => 0,
        'error' => 'Unable to connect to the API.',
    ]);
});

test('it returns a generic error for unsuccessful HTTP responses', function () {
    Http::fake([
        '*' => Http::response([], 503),
    ]);

    expect(app(ApiService::class)->get(['city' => 'Austin']))->toBe([
        'success' => false,
        'data' => null,
        'count' => 0,
        'error' => 'An unexpected error occurred.',
    ]);
});
