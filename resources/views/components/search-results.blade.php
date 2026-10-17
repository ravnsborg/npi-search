<main class="lg:col-span-8 bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col justify-between overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-700 uppercase text-xs tracking-wider border-b border-gray-200">
                    <th class="py-3 px-4 font-semibold">NPI</th>
                    <th class="py-3 px-4 font-semibold">Name</th>
                    <th class="py-3 px-4 font-semibold">Primary Taxonomy</th>
                    <th class="py-3 px-4 font-semibold">Address</th>
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
    <div class="px-4 py-3 border-t border-gray-200">
        {{ $results->links() }}
    </div>
    @endif
</main>