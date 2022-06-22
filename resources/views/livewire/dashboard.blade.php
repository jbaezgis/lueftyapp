<div>
    <div class="py-4 sm:px-6 lg:px-8 ">
        <div class="flex gap-2 text-xs text-gray-500 mb-2">
            <span>{{ __('System') }} </span>
            <span class="pt-0.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </span>
            <span>{{ __('Dashboard') }}</span>
        </div>
        {{-- Date Filters --}}
        {{-- <div class="flex gap-4 justify-end">
            <x-datetime-picker
                label="{{ __('From Date') }}"
                placeholder="{{ __('From Date') }}"
                wire:model="fromDate"
                without-time="true"
            />
            <x-datetime-picker
                label="{{ __('To Date') }}"
                placeholder="{{ __('To Date') }}"
                wire:model="toDate"
                without-time="true"
            />
        </div> --}}

        {{-- Counts --}}
        {{-- <div class="flex gap-4 justify-end py-4">
            <x-card>
                <p>{{ __('All Bookings') }}</p>
                <h2 class="text-xl font-bold">{{ $bookingsCount }}</h2>
            </x-card>
            <x-card>
                <p>{{ __('Peding') }}</p>
                <h2 class="text-xl font-bold">{{ $pendingCount }}</h2>
            </x-card>
            <x-card>
                <p>{{ __('Paid') }}</p>
                <h2 class="text-xl font-bold">{{ $paidCount }}</h2>
            </x-card>
        </div> --}}

    </div>
   
</div>
