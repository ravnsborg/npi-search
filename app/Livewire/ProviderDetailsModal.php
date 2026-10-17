<?php

namespace App\Livewire;

use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class ProviderDetailsModal extends Component
{
    /** @var array<string, mixed> */
    public array $provider = [];

    public bool $isOpen = false;

    /** @param array<string, mixed> $provider */
    #[On('show-provider')]
    public function showProvider(array $provider): void
    {
        $this->provider = $provider;
        $this->isOpen = true;
    }

    public function closeModal(): void
    {
        $this->isOpen = false;
    }

    public function render(): View
    {
        return view('components.provider-details-modal');
    }
}
