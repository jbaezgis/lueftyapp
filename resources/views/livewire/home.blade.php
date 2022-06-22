<div>
    @if($modal)
        @include('livewire.filters-modal')
    @endif
    <div class="bg-white h-full w-full overflow-y-auto pb-6">
        <div class="bg-blue-600 px-5 py-4 text-white">
            <x-jet-application-logo/>
        </div>
        <div class="px-5 pt-6 pb-20">
            <div class="flex justify-between">
                <div class="mb-3">
                    <h1 class="text-2xl font-bold uppercase">Current Offers</h1>
                    <p class="text-sm text-gray-500 font-bold">All Offers</p>
                </div>
                <div>
                    <div class="text-xs text-center text-gray-500">Filters</div>
                    <div class="flex justify-center text-gray-600">
                        <a href="#" wire:click="openModal()">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Items --}}
            <div class="border border-gray-100 shadow mb-4">
                <div class="p-4">
                    <div class="flex">
                        <div class="text-blue-600 p-2">
                            
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                              </svg>
                        </div>
                        <div class="p-2">
                            <div class="text-xs text-gray-400">From</div>
                            PUJ (Punta Cana International Airport)
                        </div>
                    </div>
                    {{-- <div class="text-gray-400 pl-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 17l-4 4m0 0l-4-4m4 4V3" />
                        </svg>
                    </div> --}}

                    <div class="flex">
                        <div class="text-blue-600 p-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="p-2">
                            <div class="text-xs text-gray-400">To</div>
                            Boca Chica
                        </div>
                    </div>

                    <div class="flex justify-center gap-6 mt-2">
                        <div class="text-center">
                            <div class="text-xs text-gray-500">Passengers</div>
                            <div>4</div>
                        </div>
                        <div class="text-center">
                            <div class="text-xs text-gray-500">Service Date</div>
                            <div>12/04/2022, 12:00</div>
                        </div>
                    </div>

                </div>
                {{-- footer --}}
                <div class="bg-gray-100">
                    <div class="text-gray-700 text-lg p-2 text-center font-semibold">
                        $ 70.95
                    </div>
                </div>
            </div>

            <div class="border border-gray-100 shadow ">
                <div class="p-4">
                    <div class="flex">
                        <div class="text-blue-600 p-2">
                            
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                              </svg>
                        </div>
                        <div class="p-2">
                            <div class="text-xs text-gray-400">From</div>
                            San domingo
                        </div>
                    </div>
                    {{-- <div class="text-gray-400 pl-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 17l-4 4m0 0l-4-4m4 4V3" />
                        </svg>
                    </div> --}}

                    <div class="flex">
                        <div class="text-blue-600 p-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="p-2">
                            <div class="text-xs text-gray-400">To</div>
                            Bavaro
                        </div>
                    </div>

                    <div class="flex justify-center gap-6 mt-2">
                        <div class="text-center">
                            <div class="text-xs text-gray-500">Passengers</div>
                            <div>2</div>
                        </div>
                        <div class="text-center">
                            <div class="text-xs text-gray-500">Service Date</div>
                            <div>12/04/2022, 12:00</div>
                        </div>
                    </div>

                </div>
                {{-- footer --}}
                <div class="bg-gray-100">
                    <div class="text-gray-700 text-lg p-2 text-center font-semibold">
                        $ 186.50
                    </div>
                </div>
            </div>
        </div>

    </div>
    
    
</div>
