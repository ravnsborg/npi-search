<?php

namespace App\Livewire;

use Illuminate\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;

class NpiSearchForm extends Component
{
    // Mapping form field names to API query parameter names.
    private const PARAMETER_MAP = [
        'firstName' => 'first_name',
        'lastName' => 'last_name',
        'npiNumber' => 'number',
        'taxonomyDescription' => 'taxonomy_description',
        'city' => 'city',
        'state' => 'state',
        'postalCode' => 'postal_code',
    ];

    #[Validate(['nullable', 'string', 'max:50'])]
    public string $firstName = '';

    #[Validate(['nullable', 'string', 'max:50'])]
    public string $lastName = '';

    #[Validate(['nullable', 'digits:10'], as: 'NPI number')]
    public string $npiNumber = '';

    #[Validate(['nullable', 'string'])]
    public string $taxonomyDescription = '';

    #[Validate(['nullable', 'string', 'max:50'])]
    public string $city = '';

    #[Validate(['nullable', 'string', 'min:2', 'max:2'])]
    public string $state = '';

    #[Validate(['nullable', 'string', 'max:10'])]
    public string $postalCode = '';

    public function search(): void
    {
        $validated = $this->validate();

        // Remove empty form fields from the validated data.
        $populatedFormFields = [];
        foreach ($validated as $key => $value) {
            $value = trim($value);
            if ($value) {
                $populatedFormFields[$key] = $value;
            }
        }

        // At least one search criterion must be provided.
        if (empty($populatedFormFields)) {
            $this->addError('search', 'Enter at least one search criterion.');
            $this->dispatch('clear-results');

            return;
        }

        // State cannot be the only search criterion.
        if (isset($populatedFormFields['state']) && count($populatedFormFields) === 1) {
            $this->addError('state', 'State cannot be the only search criterion.');
            $this->dispatch('clear-results');

            return;
        }

        $queryParameters = [];
        // Map the populated form field names to the corresponding API query parameter names.
        foreach (self::PARAMETER_MAP as $fieldName => $paramName) {
            if (isset($populatedFormFields[$fieldName])) {
                $queryParameters[$paramName] = $populatedFormFields[$fieldName];
            }
        }

        $this->dispatch('search-submitted', criteria: $queryParameters);
    }

    public function render(): View
    {
        return view('components.search-form');
    }
}
