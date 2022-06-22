<div class="bg-white absolute bottom-0 w-full border-t border-gray-200 flex">
    <a href="{{ url('/') }}" class="flex flex-grow items-center justify-center p-2 {{ request()->is('/') ? 'text-blue-500' : 'text-gray-500' }}  hover:text-blue-500">
        <div class="text-center">
            <div class="flex justify-center mb-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
            <span class="block text-xs leading-none">Current Offers</span>
        </div>
    </a>
    <a href="{{ url('accepted-offers') }}" class="flex flex-grow items-center justify-center p-2 {{ request()->is('accepted-offers') ? 'text-blue-500' : 'text-gray-500' }} hover:text-blue-500">
        <div class="text-center">
            <div class="flex justify-center mb-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
            <span class="block text-xs leading-none">Accepted Offers</span>
        </div>
    </a>
    <a href="{{ url('won-offers') }}" class="flex flex-grow items-center justify-center p-2 {{ request()->is('won-offers') ? 'text-blue-500' : 'text-gray-500' }} hover:text-blue-500">
        <div class="text-center">
            <div class="flex justify-center mb-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
            <span class="block text-xs leading-none">Won Offers</span>
        </div>
    </a>
    <a href="{{ url('account') }}" class="flex flex-grow items-center justify-center p-2 {{ request()->is('account') ? 'text-blue-500' : 'text-gray-500' }} hover:text-blue-500">
        <div class="text-center">
            <div class="flex justify-center mb-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <span class="block text-xs leading-none">Account</span>
        </div>
    </a>
</div>