<?php

use Livewire\Component;

new class extends Component {};
?>

<header class="sticky top-0 z-30 bg-white/90 backdrop-blur border-b border-gray-200 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <!-- Brand -->
        <div class="flex items-center gap-6">
            <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-600 text-white shadow-sm">
                <!-- Magnifying glass icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z" />
                </svg>
            </span>
            <span class="leading-tight">
                <span class="block text-lg font-bold text-gray-900">NPI Registry Search</span>
                <span class="hidden sm:block text-xs text-gray-500">Find healthcare providers by name, NPI, or location</span>
            </span>
        </div>
</header>