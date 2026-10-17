<?php

namespace App\Livewire;

use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class ProviderDetailsModal extends Component
{
    public array $provider = [];

    public bool $isOpen = false;

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
