<?php

namespace App\Livewire;

use App\Services\ApiService;
use Illuminate\Pagination\Paginator;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class NpiSearchResults extends Component
{
    use WithPagination;

    /** @var array<string, string> */
    public array $criteria = [];

    /** @param array<string, string> $criteria */
    #[On('search-submitted')]
    public function applySearch(array $criteria): void
    {
        $this->criteria = $criteria;

        $this->resetPage();
    }

    #[On('clear-results')]
    public function clearResults(): void
    {
        $this->criteria = [];
        $this->resetPage();
    }

    public function render(): View
    {
        if (empty($this->criteria)) {
            return view('components.search-results', ['results' => null]);
        }

        $page = $this->getPage();
        $limit = ApiService::RESULTS_PER_REQUEST;
        $skip = ($page - 1) * $limit;

        // Call API Service. Incremented limit to return 1 more rec than page allows to validate a next page would exist.
        $apiResponse = (new ApiService)->get($this->criteria, $limit + 1, $skip);

        if (! $apiResponse['success']) {
            $this->addError('search', 'The NPI Registry request failed. Please try again.');

            return view('components.search-results', ['results' => null]);
        }

        $results = new Paginator(
            $this->formatResults($apiResponse['data']),
            $limit,
            $page,
            ['path' => url()->current()],
        );

        return view('components.search-results', compact('results'));
    }

    /**
     * @param  list<array<string, mixed>>  $results
     * @return list<array<string, mixed>>
     */
    private function formatResults(array $results): array
    {
        return array_map(function (array $provider): array {
            $basic = is_array($provider['basic'] ?? null) ? $provider['basic'] : [];
            $taxonomies = is_array($provider['taxonomies'] ?? null) ? $provider['taxonomies'] : [];
            $addresses = is_array($provider['addresses'] ?? null) ? $provider['addresses'] : [];
            $primaryTaxonomy = collect($taxonomies)->firstWhere('primary', true) ?? ($taxonomies[0] ?? []);
            $practiceLocation = collect($addresses)->firstWhere('address_purpose', 'LOCATION') ?? [];
            $location = $practiceLocation ?: ($addresses[0] ?? []);
            $mailingAddress = collect($addresses)->firstWhere('address_purpose', 'MAILING') ?? [];
            $personName = collect([
                $basic['name_prefix'] ?? null,
                $basic['first_name'] ?? null,
                $basic['last_name'] ?? null,
                $basic['name_suffix'] ?? null,
            ])->filter()->implode(' ');

            return [
                'number' => (string) ($provider['number'] ?? ''),
                'name' => (string) ($basic['organization_name'] ?? $personName),
                'taxonomy' => (string) ($primaryTaxonomy['desc'] ?? ''),
                'address' => (string) ($location['address_1'] ?? ''),
                'city' => (string) ($location['city'] ?? ''),
                'state' => (string) ($location['state'] ?? ''),
                'zip_code' => (string) ($location['postal_code'] ?? ''),
                'practice_location' => $practiceLocation,
                'mailing_address' => $mailingAddress,
                'taxonomies' => $taxonomies,
                'other_identifiers' => is_array($provider['other_identifiers'] ?? null)
                    ? $provider['other_identifiers']
                    : [],
            ];
        }, $results);
    }
}
