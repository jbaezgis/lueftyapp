<div class="bg-gray-50">

    <div class="py-4 sm:px-6 lg:px-8 ">
        <div class="flex gap-2 text-xs text-gray-500 mb-2">
            <span>{{ __('Bookings') }} </span>
            <span class="pt-0.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </span>
            <span>{{ __('All Bookings') }}</span>
        </div>

        <div class="flex justify-between">
            <div>
                {{-- Title --}}
                <h1 class="text-3xl font-bold text-gray-700">{{__('Bookings')}}</h1>

                {{-- Counts --}}
                <div class="flex gap-4 py-2 text-gray-500">
                    <div class="">
                        <span>{{ __('All Bookings') }}:</span>
                        <span class="font-bold">{{ $bookingsCount }}</span>
                    </div>
        
                    <div class="">
                        <span>{{ __('Peding') }}:</span>
                        <span class="font-bold">{{ $pendingCount }}</span>
                    </div>
        
                    <div class="">
                        <span>{{ __('Paid') }}:</span>
                        <span class="font-bold">{{ $paidCount }}</span>
                    </div>
                </div>
            </div>

            {{-- Actions buttons --}}
            <div>
                <a class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-400 active:bg-blue-600 focus:outline-none focus:border-blue-600 focus:ring focus:ring-blue-300 disabled:opacity-25 transition" href="#">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ __('Create Booking') }}
                </a>
            </div>
        </div>
       
        <div class="border-b border-gray-100 mb-4"></div>

        {{-- Cols --}}
        <div class="grid grid-cols-7 gap-4">

            {{-- Filters --}}
            <div class="col-span-2 border p-4 bg-white">
                <div class="text-lg font-bold">
                    {{ __('Date range') }}
                </div>
                <div class="">
                    <div class="mb-2">
                        <x-datetime-picker
                            {{-- label="{{ __('From Date') }}" --}}
                            placeholder="{{ __('From Date') }}"
                            wire:model="fromDate"
                            without-time="true"
                            parse-format="YYYY-MM-DD"
                        />
                    </div>
                    <div>
                        <x-datetime-picker
                            {{-- label="{{ __('To Date') }}" --}}
                            placeholder="{{ __('To Date') }}"
                            wire:model="toDate"
                            without-time="true"
                            parse-format="YYYY-MM-DD"
                        />
                    </div>
                </div>

                {{-- Divider --}}
                <div class="border-b border-gray-100 mb-4"></div>

                <div class="text-lg font-bold">
                    {{ __('By Booking details') }}
                </div>
                <div class="">
                    <div class="mb-2">
                        <x-select
                            label="From Location"
                            placeholder="Select from location"
                            wire:model="fromLocation"
                        >
                            @foreach ($locationAlias as $item)
                                <x-select.option label="{{ $item->location_name }}" value="{{ $item->location_name }}" />
                            @endforeach
                           
                        </x-select>
                    </div>
    
                    <div class="mb-2">
                        <x-select
                            label="To Location"
                            placeholder="Select to location"
                            wire:model="toLocation"
                        >
                            @foreach ($locationAlias as $item)
                                <x-select.option label="{{ $item->location_name }}" value="{{ $item->location_name }}" />
                            @endforeach
                           
                        </x-select>
                    </div>

                    <div class="mb-2">
                        <x-input label="Order ID" wire:model="order" placeholder="Order ID" />
                    </div>
                    <div class="mb-2">
                        <x-input label="Name" wire:model="name" placeholder="Name" />
                    </div>
                    <div class="mb-2">
                        <x-input label="Email" wire:model="email" placeholder="Email" />
                    </div>
                </div>

                {{-- Divider --}}
                <div class="border-b border-gray-100 mb-4"></div>

                <div class="text-lg font-bold">
                    {{ __('Sorting panel') }}
                </div>
                <div class="pt-4 pb-2">
                    <div class="mb-2">
                        <x-select
                            label="Per page"
                            {{-- placeholder="Select one status" --}}
                            :options="['10', '15', '25', '50', '100']"
                            wire:model="perPage"
                        />
                    </div>
                    <div class="mb-2">
                        <label class="mb-1 block text-sm font-medium text-secondary-700 dark:text-gray-400">{{ __('Sort by')}}</label>
                        <select wire:model="sortField" class="text-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full" name="language" id="language">
                            <option value="id" >ID</option>
                            <option value="arrival_date">{{ __('Arrival Date') }}</option>
                            <option value="created_at">{{ __('Booking Date') }}</option>
                        </select> 
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-secondary-700 dark:text-gray-400">{{ __('Sort Direction')}}</label>
                        <select wire:model="sortDirection" class="text-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full" name="language" id="language">
                            <option value="asc">{{ __('Ascending') }}</option>
                            <option value="desc">{{ __('Descending') }}</option>
                        </select> 
                    </div>
                </div>

                <div class="flex justify-center pt-7">
                    <a wire:click="cleanFields" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-400 active:bg-yellow-600 focus:outline-none focus:border-yellow-600 focus:ring focus:ring-yellow-300 disabled:opacity-25 transition" href="#">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        {{ __('Reset Filters') }}
                    </a>
                </div>
            </div>

            {{-- Col right --}}
            <div class="col-span-5">
                <div class="flex justify-center text-gray-500">
                    <svg wire:loading xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 animate-spin mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span wire:loading class="text-sm mb-2">
                        {{ __('Loading') }}...
                    </span>
                </div>

                @foreach ($bookings as $booking)
                    <div class="bg-white border {{ $booking->status == 'paid' ? 'border-green-400' : 'border-gray-200'}}  rounded mb-4">
                        <div class="px-4 py-2">
                            <div class="flex gap-4 mb-2 border-b border-gray-100 py-2">
                                <div>
                                    <div class="text-xs text-gray-500">ID</div>
                                    <div>
                                        {{ $booking->id }}
                                    </div>
                                </div>
                                <div>
                                    <div class="text-xs text-gray-500">BOOKING TYPE</div>
                                    <div>
                                        {{ $booking->bookingtype }}
                                    </div>
                                </div>
                                <div>
                                    <div class="text-xs text-gray-500">TYPE</div>
                                    <div>
                                        {{ $booking->type }}
                                    </div>
                                </div>

                                <div>
                                    <div class="text-xs text-gray-500">{{ __('FROM') }}</div>
                                    <div class="font-bold">
                                        {{$booking->alias_location_from}}
                                    </div>
                                </div>
                                <div>
                                    <div class="text-xs text-gray-500">{{ __('TO') }}</div>
                                    <div class="font-bold">
                                        {{$booking->alias_location_to}}
                                    </div>
                                </div>
                                
                            </div>
                        

                            <div>
                                <h4 class="text-gray-500">{{ __('CLIENT DETAILS') }}</h4>
                            </div>

                            <div class="flex gap-4 py-2">
                                <div>
                                    <div class="text-xs text-gray-500">{{ __('NAME') }}</div>
                                    <div>
                                        <h3 class="">{{$booking->fullname}}</h3>
                                    </div>
                                </div>
                                <div>
                                    <div class="text-xs text-gray-500">{{ __('EMAIL') }}</div>
                                    <div>
                                        <h3 class="">{{$booking->email}}</h3>
                                    </div>
                                </div>
                                <div>
                                    <div class="text-xs text-gray-500">{{ __('PHONE') }}</div>
                                    <div>
                                        <h3 class="">809-321-1234</h3>
                                    </div>
                                </div>
                            </div>
                
                        </div>
                        
                        <div class="{{ $booking->status == 'paid' ? 'bg-green-400' : 'bg-gray-200'}} px-4 py-2"> 
                            <div class="flex gap-4">
                                <div>
                                    @if ($booking->status == 'paid')
                                        <a class="inline-flex items-center px-4 py-2 bg-green-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-600 active:bg-green-800 focus:outline-none focus:border-green-800 focus:ring focus:ring-green-300 disabled:opacity-25 transition" href="{{ url('booking/'.$booking->id) }}">Open</a>
                                    @else
                                        <a class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-400 active:bg-gray-600 focus:outline-none focus:border-gray-600 focus:ring focus:ring-gray-300 disabled:opacity-25 transition" href="{{ url('booking/'.$booking->id) }}">Open</a>
                                    @endif
                                </div>
                                
                                <div class="text-gray-600 align-middle py-1">
                                    <span>{{ __('Booking date') }}:</span> <span class="font-bold">{{ date('j F Y', strtotime($booking->created_at)) }}</span> ({{ $booking->created_at->diffForHumans() }})
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                
                {{ $bookings->links() }}
            </div>
        </div>

        
       
    </div>
