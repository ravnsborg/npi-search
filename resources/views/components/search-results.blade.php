<main class="lg:col-span-8 bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col overflow-hidden lg:h-0 lg:min-h-full">

    @if ($results !== null && ($results->hasMorePages() || $results->currentPage() > 1))
    <div class="shrink-0 px-4 py-3 border-t border-gray-200">
        {{ $results->links() }}
    </div>
    @endif
    <div class="flex-1 min-h-0 overflow-auto lg:max-h-none">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="text-gray-700 uppercase text-xs tracking-wider">
                    <th class="sticky top-0 z-10 bg-gray-50 py-3 px-4 font-semibold shadow-[inset_0_-1px_0_#e5e7eb]">NPI</th>
                    <th class="sticky top-0 z-10 bg-gray-50 py-3 px-4 font-semibold shadow-[inset_0_-1px_0_#e5e7eb]">Name</th>
                    <th class="sticky top-0 z-10 bg-gray-50 py-3 px-4 font-semibold shadow-[inset_0_-1px_0_#e5e7eb]">Primary Taxonomy</th>
                    <th class="sticky top-0 z-10 bg-gray-50 py-3 px-4 font-semibold shadow-[inset_0_-1px_0_#e5e7eb]">Address</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 text-sm text-gray-700">
                @if ($results === null)
                <tr>
                    <td colspan="4" class="py-8 px-4 text-center text-gray-500">Submit a search to view providers.</td>
                </tr>
                @elseif ($results->isEmpty())
                <tr>
                    <td colspan="4" class="py-8 px-4 text-center text-gray-500">No providers found.</td>
                </tr>
                @else
                @foreach ($results as $provider)
                <tr wire:key="npi-{{ $provider['number'] }}"
                    wire:click="$dispatch('show-provider', { provider: @js($provider) })"
                    class="hover:bg-blue-50/60 cursor-pointer transition">
                    <td class="py-3 px-3 font-mono text-gray-900">{{ $provider['number'] }}</td>
                    <td class="py-3 px-3 text-gray-900">{{ $provider['name'] }}</td>
                    <td class="py-3 px-4">{{ $provider['taxonomy'] }}</td>
                    <td class="py-3 px-4 text-xs">
                        {{ $provider['address'] }}<br>
                        {{ $provider['city'] }}, {{ $provider['state'] }} {{ $provider['zip_code'] }}
                    </td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
    </div>
    @if ($results !== null && ($results->hasMorePages() || $results->currentPage() > 1))
    <div class="shrink-0 px-4 py-3 border-t border-gray-200">
        {{ $results->links() }}
    </div>
    @endif

</main>