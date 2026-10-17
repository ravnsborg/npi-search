<?php

namespace App\Livewire;

use App\Services\ApiService;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class NpiSearchResults extends Component
{
    use WithPagination;

    public int $perPage = 50; // API max is 200

    public array $criteria = [];

    #[On('search-submitted')]
    public function applySearch(array $criteria)
    {
        $this->criteria = $criteria;

        $this->resetPage();
    }

    // #[On('search-cleared')]
    // public function clearSearch()
    // {
    //     $this->criteria = [];
    //     $this->resetPage();
    // }

    public function render()
    {
        // Don't hit the API until the user has searched
        if (empty($this->criteria)) {
            return view('components.search-results', ['results' => null]);
        }

        $page = $this->getPage();
        $limit = min($this->perPage, 1200);
        // $limit  = min($this->perPage, ApiService::RESULTS_PER_REQUEST);
        $params = $this->criteria + [
            'skip' => ($page - 1) * $limit,
            'limit' => $limit,
        ];

        $apiResponse = (new ApiService)->get($this->criteria);
        if (! $apiResponse['success']) {
            $this->addError('search', 'The NPI Registry request failed. Please try again.');

            return;
        }

        $formattedData = $this->formatResults($apiResponse['data']);

        $data = [
            'items' => $formattedData,
            'total' => count($formattedData),
            'criteria' => $this->criteria,
        ];

        $results = new LengthAwarePaginator(
            $data['items'],
            $data['total'],
            $limit,
            $page,
            ['path' => url()->current()],
        );

        return view('components.search-results', compact('results'));
    }

    // Standardize data to display
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
                $basic['middle_name'] ?? null,
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
