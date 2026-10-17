<?php

use App\Livewire\NpiSearchForm;
use App\Livewire\NpiSearchResults;
use App\Livewire\ProviderDetailsModal;
use App\Services\ApiService;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

beforeEach(function () {
    config([
        'cache.default' => 'array',
        'npi_registry.url' => 'https://npiregistry.cms.hhs.gov/api/',
        'npi_registry.version' => '2.1',
        'npi_registry.timeout' => 10,
    ]);

    Cache::flush();
});

test('the search form submits trimmed criteria using registry parameter names', function () {
    Livewire::test(NpiSearchForm::class)
        ->set('firstName', ' Jane ')
        ->set('lastName', ' Doe ')
        ->set('npiNumber', '1234567890')
        ->set('taxonomyDescription', 'Family Medicine')
        ->set('city', ' Austin ')
        ->set('state', 'TX')
        ->set('postalCode', '78701')
        ->call('search')
        ->assertDispatched('search-submitted', criteria: [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'number' => '1234567890',
            'taxonomy_description' => 'Family Medicine',
            'city' => 'Austin',
            'state' => 'TX',
            'postal_code' => '78701',
        ]);
});

test('the search form rejects empty criteria and clears previous results', function () {
    Livewire::test(NpiSearchForm::class)
        ->call('search')
        ->assertHasErrors('search')
        ->assertDispatched('clear-results')
        ->assertNotDispatched('search-submitted');
});

test('the search form does not allow state as the only criterion', function () {
    Livewire::test(NpiSearchForm::class)
        ->set('state', 'TX')
        ->call('search')
        ->assertHasErrors('state')
        ->assertDispatched('clear-results')
        ->assertNotDispatched('search-submitted');
});

test('the search form validates the NPI number before submitting', function () {
    Livewire::test(NpiSearchForm::class)
        ->set('npiNumber', '123')
        ->call('search')
        ->assertHasErrors('npiNumber')
        ->assertNotDispatched('search-submitted');
});

test('search results show provider details and request the next result for pagination', function () {
    Http::fake([
        '*' => Http::response([
            'result_count' => 1,
            'results' => [[
                'number' => '1234567890',
                'basic' => [
                    'first_name' => 'Jane',
                    'last_name' => 'Doe',
                ],
                'taxonomies' => [
                    ['desc' => 'Other Specialty', 'primary' => false],
                    ['desc' => 'Family Medicine', 'primary' => true],
                ],
                'addresses' => [
                    [
                        'address_purpose' => 'MAILING',
                        'address_1' => 'PO Box 1',
                        'city' => 'Austin',
                        'state' => 'TX',
                        'postal_code' => '78702',
                    ],
                    [
                        'address_purpose' => 'LOCATION',
                        'address_1' => '123 Main Street',
                        'city' => 'Austin',
                        'state' => 'TX',
                        'postal_code' => '78701',
                    ],
                ],
                'other_identifiers' => [],
            ]],
        ]),
    ]);

    Livewire::test(NpiSearchResults::class)
        ->dispatch('search-submitted', criteria: ['city' => 'Austin'])
        ->assertSee('1234567890')
        ->assertSee('Jane Doe')
        ->assertSee('Family Medicine')
        ->assertSee('123 Main Street')
        ->assertSee('78701');

    Http::assertSent(function (Request $request) {
        $url = parse_url($request->url());
        parse_str($url['query'] ?? '', $query);

        return ($url['scheme'] ?? null) === 'https'
            && ($url['host'] ?? null) === 'npiregistry.cms.hhs.gov'
            && ($url['path'] ?? null) === '/api/'
            && $query === [
                'city' => 'Austin',
                'limit' => '51',
                'skip' => '0',
                'version' => '2.1',
            ];
    });
});

test('search results expose an error when the registry request fails', function () {
    Http::fake([
        '*' => Http::response([], 503),
    ]);

    Livewire::test(NpiSearchResults::class)
        ->dispatch('search-submitted', criteria: ['city' => 'Austin'])
        ->assertHasErrors('search');
});

test('provider details open from a selected result and can be closed', function () {
    $provider = [
        'number' => '1234567890',
        'name' => 'Jane Doe',
        'practice_location' => ['address_1' => '123 Main Street'],
        'mailing_address' => ['address_1' => 'PO Box 1'],
        'taxonomies' => [['desc' => 'Family Medicine', 'primary' => true]],
        'other_identifiers' => [],
    ];

    Livewire::test(ProviderDetailsModal::class)
        ->assertSet('isOpen', false)
        ->dispatch('show-provider', provider: $provider)
        ->assertSet('provider', $provider)
        ->assertSet('isOpen', true)
        ->assertSee('Jane Doe')
        ->assertSee('123 Main Street')
        ->assertSee('PO Box 1')
        ->assertSee('Family Medicine')
        ->call('closeModal')
        ->assertSet('isOpen', false);
});

test('successful registry responses are cached for repeated requests', function () {
    Http::fake([
        '*' => Http::response([
            'result_count' => 1,
            'results' => [['number' => '1234567890']],
        ]),
    ]);

    $service = app(ApiService::class);
    $criteria = ['number' => '1234567890'];

    expect($service->get($criteria))->toMatchArray([
        'success' => true,
        'count' => 1,
        'data' => [['number' => '1234567890']],
        'error' => null,
    ]);

    expect($service->get($criteria))->toMatchArray([
        'success' => true,
        'count' => 1,
        'data' => [['number' => '1234567890']],
        'error' => null,
    ]);

    Http::assertSentCount(1);
});
