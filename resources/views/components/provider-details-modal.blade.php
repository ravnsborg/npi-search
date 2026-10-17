<div id="provider-modal" wire:click.self="closeModal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 {{ $isOpen ? '' : 'hidden' }}">
    <div class="bg-white rounded-xl shadow-2xl border border-gray-200 max-w-2xl w-full max-h-[90vh] overflow-y-auto p-6 space-y-6 relative transform transition-all">

        <!-- Modal Header -->
        <div class="flex items-center justify-between border-b border-gray-100 pb-4">
            <div>
                <h3 id="modal-provider-name" class="text-xl font-bold text-gray-900">Provider: {{ $provider['name'] ?? '' }}</h3>
                <p class="text-xs text-gray-500 mt-0.5">NPI: <span id="modal-provider-npi" class="font-mono font-medium text-gray-900">{{ $provider['number'] ?? '' }}</span></p>
            </div>
            <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 p-2 rounded-lg hover:bg-gray-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Modal Content Body -->
        <div class="space-y-4 text-xs sm:text-sm">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-gray-50 p-3.5 rounded-lg border border-gray-200">
                    <span class="font-semibold text-gray-900 block mb-1">Practice Location:</span>
                    @php($practiceLocation = $provider['practice_location'] ?? [])
                    @php($practiceLocationLines = collect([
                    $practiceLocation['address_1'] ?? null,
                    $practiceLocation['address_2'] ?? null,
                    trim(collect([$practiceLocation['city'] ?? null, $practiceLocation['state'] ?? null])->filter()->implode(', ').' '.($practiceLocation['postal_code'] ?? '')),
                    ! empty($practiceLocation['telephone_number']) ? 'Phone: '.$practiceLocation['telephone_number'] : null,
                    ! empty($practiceLocation['fax_number']) ? 'Fax: '.$practiceLocation['fax_number'] : null,
                    ])->filter(fn ($line) => filled($line))->values())
                    <p class="text-gray-600 whitespace-pre-line leading-relaxed">
                        @foreach ($practiceLocationLines as $line)
                        {{ $line }}@unless ($loop->last)<br>@endunless
                        @endforeach
                    </p>
                </div>
                <div class="bg-gray-50 p-3.5 rounded-lg border border-gray-200">
                    <span class="font-semibold text-gray-900 block mb-1">Mailing Address:</span>
                    @php($mailingAddress = $provider['mailing_address'] ?? [])
                    @php($mailingAddressLines = collect([
                    $mailingAddress['address_1'] ?? null,
                    $mailingAddress['address_2'] ?? null,
                    trim(collect([$mailingAddress['city'] ?? null, $mailingAddress['state'] ?? null])->filter()->implode(', ').' '.($mailingAddress['postal_code'] ?? '')),
                    ])->filter(fn ($line) => filled($line))->values())
                    <p class="text-gray-600 whitespace-pre-line leading-relaxed">
                        @foreach ($mailingAddressLines as $line)
                        {{ $line }}@unless ($loop->last)<br>@endunless
                        @endforeach
                    </p>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <span class="font-semibold text-gray-900 block mb-1">Taxonomies:</span>
                    <ul class="list-disc list-inside space-y-0.5 text-gray-600 pl-1 text-sm">
                        @forelse ($provider['taxonomies'] ?? [] as $taxonomy)
                        <li>
                            {{ $taxonomy['desc'] ?? $taxonomy['code'] ?? 'Taxonomy' }}
                            @if (! empty($taxonomy['primary']))
                            (Primary)
                            @endif
                            @if (! empty($taxonomy['license']) || ! empty($taxonomy['state']))
                            - License: {{ $taxonomy['license'] ?? 'N/A' }}{{ ! empty($taxonomy['state']) ? ' ('.$taxonomy['state'].')' : '' }}
                            @endif
                        </li>
                        @empty
                        <li>None reported</li>
                        @endforelse
                    </ul>
                </div>
                <div>
                    <span class="font-semibold text-gray-900 block mb-1">Other Identifiers:</span>
                    <ul class="list-disc list-inside space-y-0.5 text-gray-600 pl-1 text-sm">
                        @forelse ($provider['other_identifiers'] ?? [] as $identifier)
                        <li>
                            {{ $identifier['desc'] ?? $identifier['code'] ?? 'Identifier' }}:
                            {{ $identifier['identifier'] ?? 'N/A' }}
                            @if (! empty($identifier['state']))
                            ({{ $identifier['state'] }})
                            @endif
                        </li>
                        @empty
                        <li>None reported</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <!-- Modal Footer Button -->
        <div class="pt-4 border-t border-gray-100 flex justify-end">
            <button wire:click="closeModal" class="bg-white border border-gray-300 hover:bg-gray-100 px-4 py-2 rounded-lg shadow-sm text-gray-700 font-medium transition">
                Back to Results
            </button>
        </div>
    </div>
</div>