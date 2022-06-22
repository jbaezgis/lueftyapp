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
                <x-mobile-active-nav href="{{ url('/home') }}" :active="request()->is('home')">
                    {{ __('Ground Transfer') }}
                </x-mobile-active-nav>

                {{-- <x-mobile-active-nav href="{{ url('/home') }}" :active="request()->is('home')">
                    {{ __('Private Charter Flights') }}
                </x-mobile-active-nav>

                <x-mobile-active-nav href="{{ url('/home') }}" :active="request()->is('home')">
                    {{ __('Ferry &amp; Shuttle Combination') }}
                </x-mobile-active-nav> --}}

                
            </div>
        </div>
 
    </div>
</div>
