@section('title', __('Privacy Policy'))
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
            <span>{{ __('Privacy Policy') }}</span>
        </div>
        <div class="flex justify-between">
            <div>
                {{-- Title --}}
                <h1 class="text-3xl font-bold text-gray-700">{{ __('Privacy Policy') }}</h1>
            </div>
        </div>

        <div class="space-y-4 text-lg mt-6">
            <p>1. We do not share sell or use for our own marketing any data you enter on our web site or that you share with our team, with any person or company.</p>
            <p>2. Our booking data, which includes the data you have entered or given to us, is saved in either Amazon, Microsoft, Linode or Google cloud servers and we assume that the security of those servers protect the data to the best of class standards.</p>
            <p>3. We only use the data you give us to manage your booking requests which include payment methods you select, organizational data is shared with transport, tour and similar service providers.</p>
            <p>4. If you would like us to remove data you have given us send us an email and we will do so with 14 days and confirm same to you by return email. and we assume that the security of those servers protect the data to the best of class standards.</p>
            <p>5. For any questions about your personal data or your privacy protection contact us by any of the means on our web site.</p>
            <p>6. Our policy is contingent on the requirements or demands of the law or law enforcement.</p>
        </div>
    </div>
</div>
