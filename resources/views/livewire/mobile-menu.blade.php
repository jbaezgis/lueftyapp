<div>
    <div class="bg-white shadow-sm">
        <div class="flex justify-between py-4 px-2">
            <div class="m-x-auto">
                <a href="{{ url('/') }}" class="">
                    <x-jet-application-logo />
                </a>
            </div>
            <div>
                <x-button label="EN" />
            </div>
        </div>
        {{-- <div class="py-2 overflow-x-auto scrollbar-hidden text-center">
            <div class="px-6 text-sm inline-flex">
                <a class="px-3 py-2 " href="/"><img class="block h-12 w-auto" src="{{asset('images/ds-support.svg')}}" alt=""></a>
                
            </div>
        </div> --}}
    
        <div class="py-2 overflow-x-auto scrollbar-hidden text-center">
            <div class="px-6 text-sm inline-flex">
                <a class="px-3 py-2 rounded-md  text-gray-800 whitespace-nowrap mr-3 hover:text-gray-900 hover:bg-blue-200 {{ request()->is('home') ? 'bg-blue-200' : 'bg-gray-50' }}" href="{{ url('home') }}">Ground Transfer</a>
                <a class="px-3 py-2 rounded-md bg-gray-50 text-gray-800 whitespace-nowrap mr-3 hover:text-gray-900 hover:bg-gray-200" href="#">Private Charter Flights</a>
                <a class="px-3 py-2 rounded-md bg-gray-50 text-gray-800 whitespace-nowrap mr-3 hover:text-gray-900 hover:bg-gray-200" href="#">Ferry &amp; Shuttle Combination</a>
            </div>
        </div>
 
    </div>
</div>
