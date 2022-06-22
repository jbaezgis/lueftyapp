<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Home') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto p-4">
           <h1>{{ $booking->fullname }}</h1>

            
        </div>
    </div>
</x-app-layout>