@section('title', __('Contact Us'))
@section('description', 'Best Private Transfers in the DR!')
@section('keywords', 'Dominican Shuttles, Private Transfers, Airport Pickup, Tourist, Transfers, Airport, Dominican, Tourism, Beach, Hotel, Private, Shuttle', 'Safety')
@section('og-image', asset('images/image-cover.png'))
@section('og-image-url', asset('images/image-cover.png'))
<div>
    <div class="py-4 max-w-7xl mx-auto px-2 sm:px-6 lg:px-8 ">
        <div class="flex gap-2 text-xs text-gray-500 mb-2">
            <span>{{ __('Dominican Shuttles') }} </span>
            <span class="pt-0.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </span>
            <span>{{ __('About Us') }}</span>
        </div>

        <div class="py-10 px-6">
            <div class="bg-white shadow p-4">
            
                @livewire('contact-form')
            </div>
        </div>
    </div>
</div>
