<!-- Left Sidebar: Search Parameters -->
<form wire:submit="search" class="lg:col-span-4 self-start bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <div class="space-y-5">
        <div class="space-y-4">
            <!-- First Name -->
            <div>
                <label for="first-name" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                <input id="first-name"
                    type="text"
                    maxlength="50"
                    wire:model="firstName"
                    class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition"
                    placeholder="e.g. John">
                @error('firstName') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Last Name -->
            <div>
                <label for="last-name" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                <input id="last-name"
                    type="text"
                    maxlength="50"
                    wire:model="lastName"
                    class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition"
                    placeholder="e.g. Smith">
                @error('lastName') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- NPI Number -->
            <div>
                <label for="npi-number" class="block text-sm font-medium text-gray-700 mb-1">NPI Number</label>
                <input id="npi-number"
                    type="text"
                    inputmode="numeric"
                    pattern="[0-9]*"
                    maxlength="10"
                    oninput="this.value = this.value.replace(/\D/g, '')"
                    wire:model="npiNumber"
                    class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition"
                    placeholder="10-digit NPI">
                @error('npiNumber') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Taxonomy Description -->
            <div>
                <label for="taxonomy-description" class="block text-sm font-medium text-gray-700 mb-1">Taxonomy Description</label>
                <select id="taxonomy-description"
                    wire:model="taxonomyDescription"
                    class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition">
                    <option value="">Any taxonomy</option>
                    <option value="Family Medicine">Family Medicine</option>
                    <option value="Internal Medicine">Internal Medicine</option>
                    <option value="Pediatrics">Pediatrics</option>
                    <option value="Cardiology">Cardiology</option>
                </select>
                @error('taxonomyDescription') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- City -->
            <div>
                <label for="city" class="block text-sm font-medium text-gray-700 mb-1">City</label>
                <input id="city"
                    type="text"
                    maxlength="50"
                    wire:model="city"
                    class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition"
                    placeholder="e.g. Los Angeles">
                @error('city') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- State -->
            <div>
                <label for="state" class="block text-sm font-medium text-gray-700 mb-1">State</label>
                <input id="state"
                    type="text"
                    wire:model="state"
                    maxlength="2"
                    class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition"
                    placeholder="e.g. TX">
                @error('state') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Zip Code -->
            <div>
                <label for="postal-code" class="block text-sm font-medium text-gray-700 mb-1">Zip Code</label>
                <input id="postal-code"
                    type="text"
                    wire:model="postalCode"
                    maxlength="10"
                    class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition"
                    placeholder="e.g. 90001">
                @error('postalCode') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    <!-- Submit Button -->
    <div class="mt-6 pt-4 border-t border-gray-100">
        @error('search') <p class="mb-3 text-sm text-red-600" role="alert">{{ $message }}</p> @enderror
        <button type="submit"
            wire:loading.attr="disabled"
            class="w-full bg-blue-500 hover:bg-blue-600 text-white font-medium py-2.5 px-4 rounded-lg shadow-md hover:shadow-lg active:shadow-sm active:translate-y-px transition duration-150 disabled:opacity-50">
            Search Providers
        </button>
    </div>
</form>