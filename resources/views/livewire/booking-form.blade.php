@section('title', __('Booking form') . ' - #'. $booking->id )
@section('description', 'Best Private Transfers in the DR!')
@section('keywords', 'Dominican Shuttles, Private Transfers, Airport Pickup, Tourist, Transfers, Airport, Dominican, Tourism, Beach, Hotel, Private, Shuttle', 'Safety')
@section('og-image', asset('images/image-cover.png'))
@section('og-image-url', asset('images/image-cover.png'))
<div>
    <div class="max-w-3xl mx-auto p-4">
        <div class="bg-white rounded">
            <div class="pt-4 flex justify-center">
                <div class="">
                    <div class="flex">
                        <div class="text-blue-600 p-2">
                            <x-icon name="location-marker" class="w-5 h-5" />
                        </div>
                        <div class="p-2">
                            {{ $booking->alias_location_from }}
                        </div>
                    </div>
                    <div class="text-gray-400 pl-2">
                        <x-icon name="arrow-narrow-down" style="solid" class="w-5 h-5" />
                    </div>

                    <div class="flex">
                        <div class="text-blue-600 p-2">
                            <x-icon name="location-marker" style="solid" class="w-5 h-5" />
                        </div>
                        <div class="p-2">
                            {{ $booking->alias_location_to }}
                        </div>
                    </div>
                </div>

                
            </div>
            <div class="flex gap-6 py-4 justify-center">
                <div class="text-center">
                    <div class="text-sm text-gray-500">
                        @if ($booking->servicePrice->priceOption->id == 3)
                            {{-- <img class="mx-auto w-52" src="{{ asset('images/vehicles/Minivan.png') }}" alt=""> --}}
                            <div class="">
                                {{ __('Minivan') }}
                            </div>
                        @elseif ($booking->servicePrice->priceOption->id == 5)
                            {{-- <img class="mx-auto w-52" src="{{ asset('images/vehicles/crafter.png') }}" alt=""> --}}
                            <div class="">
                                {{ __('Minibus') }}
                            </div>
                        @endif
                    </div>
                    <div class="">
                        {{ $booking->servicePrice->priceOption->name }}
                    </div>
                </div>

                <div class="text-center">
                    <div class="text-sm text-gray-500">{{ __('Driving Time') }}</div>
                    {{-- {{formatDrivingTime($item->driving_time)}} --}}
                    <div class="">
                        @if ($booking->service->driving_time_minutes < 60)
                            {{date('i'.' \m\i\n\s', mktime(0,$booking->service->driving_time_minutes))}}
                        @elseif ($booking->service->driving_time_minutes < 120)
                            {{date('H'.' \h\o\u\r '. 'i'.' \m\i\n\s', mktime(0,$booking->service->driving_time_minutes))}}
                        @else
                            {{date('H'.' \h\o\u\r\s '. 'i'.' \m\i\n\s', mktime(0,$booking->service->driving_time_minutes))}}
                        @endif 
                    </div>
                </div>

            </div>
            
            <div class="flex justify-center gap-6 py-4">
                <div class="text-center">
                    <div class="text-sm text-gray-500">{{ __('Type') }}</div>
                    {{-- {{formatDrivingTime($item->driving_time)}} --}}
                    <div class="">
                        @if ($booking->type == 'oneway')
                            {{ __('One Way') }}
                        @else
                            {{ __('Round Trip') }}
                        @endif
                    </div>
                </div>
    
                <div class="text-center">
                    <div class="text-sm text-gray-500">{{ __('Order Total') }}</div>
                    {{-- {{formatDrivingTime($item->driving_time)}} --}}
                    <div class="">
                        ${{ number_format($booking->order_total, 2, '.', ',') }}
                    </div>
                </div>

            </div>
        </div>{{-- bg-white --}}

        {{-- Form --}}
        

        

        {{-- Form --}}
        {!! Form::model($booking, ['method' => 'PATCH', 'url' => ['/booking', $booking->id], 'class' => '']) !!}
    
            <div class="text-lg font-bold text-gray-600 my-6 border-b border-gray-200">
                    {{ __('Contact Details') }}
                </div>
                <div class="mb-2">
                    {{-- <x-input wire:model="fullname" label="Full name" name="fullname" value="{{ old('fullname', $booking->fullname) }}"/> --}}
                    <label class="mb-1 block text-sm font-medium {{ $errors->has('fullname') ? 'text-negative-600' : 'text-secondary-700' }} dark:text-gray-400" for="arrival_time">{{ __('Full name') }}</label>
                    <input class="{{ $errors->has('fullname') ? 'border-red-600' : 'border-gray-300' }} focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full" 
                        type="text" 
                        name="fullname" id="fullname"
                        wire:model="fullname"
                        value="{{ old($booking->fullname) }}"
                    >
                </div>
                
                <div class="mb-2 ">
                    <label class="mb-1 block text-sm font-medium {{ $errors->has('email') ? 'text-negative-600' : 'text-secondary-700' }} dark:text-gray-400" for="arrival_time">{{ __('Email') }}</label>
                    <input class="{{ $errors->has('email') ? 'border-red-600' : 'border-gray-300' }} focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full" 
                        type="email" 
                        name="email" id="email"
                        wire:model="email"
                        value="{{ old('email', $booking->email) }}"
                    >
                    @error('email')
                        <span class="text-sm text-red-400">{{ $message }}</span>
                    @enderror
                </div>

                <div class="grid grid-cols-5 gap-2">
                    <div class="col-span-3">
                        <x-inputs.phone wire:model="phone" label="Phone"  name="phone" value="{{ $booking->phone }}"/>
                    </div>
    
                    <div class="col-span-2">
                        {{-- <x-select
                            label="Language"
                            name="language"
                        >
                            <x-select.option label="English" value="en" {{ $booking->language == 'en' ? 'selected' : '' }}/>
                            <x-select.option label="Español" value="es" {{ $booking->language == 'es' ? 'selected' : '' }}/>
                        </x-select> --}}

                        <label class="mb-1 block text-sm font-medium text-secondary-700 dark:text-gray-400">{{ __('Preferred language')}}</label>
                        <select class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full" name="language" id="language">
                            <option value="en"  {{ old('language') == 'en' ? 'selected' : '' }}>English</option>
                            <option value="es" {{ old('language') == 'es' ? 'selected' : '' }}>Español</option>
                        </select> 
                    </div>
                </div>


                {{-- Conditions One Way --}}
                @if ($booking->service->fromlocation->is_airport and $booking->service->tolocation->is_airport)
                    <div class="text-lg font-bold text-gray-600 my-6 border-b border-gray-200">
                        {{$booking->type == 'roundtrip' ? __('1st Trip Arrival Information') : 'Arrival Information'}}
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="mb-2">
                            <div class="block text-sm font-medium text-secondary-700 dark:text-gray-400 mb-1" for="arrival_time">
                                {{$booking->type == 'roundtrip' ? __('1st Trip arrival date') : 'Arrival date'}}
                            </div>
                            <div class="border border-gray-300 bg-white p-2 rounded-md shadow-sm w-full">
                                {{ date('j F Y', strtotime($booking->arrival_date)) }}
                            </div>
                        </div>
        
                        <div class="mb-2">
                        <label class="mb-1 block text-sm font-medium text-secondary-700 dark:text-gray-400" for="arrival_time">{{$booking->type == 'roundtrip' ? __('1st Trip arrival time') : 'Arrival time'}}</label>
                            <input class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full" 
                                type="time" 
                                name="arrival_time" id="arrival_time"
                                value="{{ old('arrival_time', $booking->arrival_time) }}"
                            >
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div class="mb-2">
                            <x-input label="{{$booking->type == 'roundtrip' ? __('1st Trip arrival airline') : 'Arrival airline'}}"  name="arrival_airline"  value="{{ old('arrival_airline', $booking->arrival_airline) }}"/>
                        </div>
                        <div class="mb-2">
                            <x-input label="Flight flight"  name="flight_number" value="{{ old('flight_number', $booking->flight_number) }}"/>
                        </div>
                    </div>

                    <div class="mb-2">
                        {{-- <label class="mb-1 block text-sm font-medium text-secondary-700 dark:text-gray-400" for="arrival_time">{{ __('More') }}</label> --}}
                        <x-textarea label="More information" name="more_information"/>
                        {{-- <textarea id="txtid" name="more_information" rows="4" cols="50" maxlength="200">
                            A nice day is a nice day.
                            Lao Tseu
                        </textarea> --}}
    
                        <div class="text-sm text-gray-600">
                            {{ __('Please enter the name of your hotel or the address of your drop off location, as well as any additional information we should know.') }}
                        </div>
                    </div>

                    
				@elseif ($booking->service->fromlocation->is_airport)
                    <div class="text-lg font-bold text-gray-600 my-6 border-b border-gray-200">
                        {{ __('Arrival Information') }}
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="mb-2">
                            <div class="block text-sm font-medium text-secondary-700 dark:text-gray-400 mb-1" for="arrival_time">
                                {{ __('Arrival date') }}
                            </div>
                            <div class="border border-gray-300 bg-white p-2 rounded-md shadow-sm w-full">
                                {{ date('j F Y', strtotime($booking->arrival_date)) }}
                            </div>
                        </div>
        
                        <div class="mb-2">
                        <label class="mb-1 block text-sm font-medium text-secondary-700 dark:text-gray-400" for="arrival_time">{{ __('Arrival time') }}</label>
                            <input class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full" 
                                type="time" 
                                name="arrival_time" id="arrival_time"
                                value="{{ old('arrival_time', $booking->arrival_time) }}"
                                
                            >
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div class="mb-2">
                            <x-input label="{{ $booking->service->fromlocation->is_airport ? __('Arrival Airline') : __('Departure Airline') }}"  name="arrival_airline"/>
                        </div>
                        <div class="mb-2">
                            <x-input label="Flight flight"  name="flight_number"/>
                        </div>
                    </div>

                    <div class="mb-2">
                        {{-- <label class="mb-1 block text-sm font-medium text-secondary-700 dark:text-gray-400" for="arrival_time">{{ __('More') }}</label> --}}
                        <x-textarea label="More information" name="more_information"/>
                        {{-- <textarea id="txtid" name="more_information" rows="4" cols="50" maxlength="200">
                            A nice day is a nice day.
                            Lao Tseu
                        </textarea> --}}

                        <div class="text-sm text-gray-600">
                            {{ __('Please enter the name of your hotel or the address of your drop off location, as well as any additional information we should know.') }}
                        </div>
                    </div>
		
				{{-- End if formlocation is airport --}}
		
				{{-- Start if tolocation is airport --}}
				@elseif ($booking->service->tolocation->is_airport)
                    <div class="text-lg font-bold text-gray-600 my-6 border-b border-gray-200">
                        {{ __('Departure Information') }}
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="mb-2">
                            <div class="block text-sm font-medium text-secondary-700 dark:text-gray-400 mb-1" for="arrival_time">
                                {{ __('Departure Date') }}
                            </div>
                            <div class="border border-gray-300 bg-white p-2 rounded-md shadow-sm w-full">
                                {{ date('j F Y', strtotime($booking->arrival_date)) }}
                            </div>
                        </div>
        
                        <div class="mb-2">
                        <label class="mb-1 block text-sm font-medium text-secondary-700 dark:text-gray-400" for="arrival_time">{{ __('Departure time') }}</label>
                            <input class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full" 
                                type="time" 
                                name="arrival_time" id="arrival_time"
                            >
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div class="mb-2">
                            <x-input label="Departure airline"  name="arrival_airline"/>
                        </div>
                        <div class="mb-2">
                            <x-input label="Flight number"  name="flight_number"/>
                        </div>
                    </div>

                    <div class="mb-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-secondary-700 dark:text-gray-400" for="want_to_arrive">{{ __('I would like to be at the airport') }}</label>             
                            <select class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full" name="want_to_arrive" id="want_to_arrive">
                                @foreach($willArriveData as $k => $v)
                                    <option value="{{$k}}" {{old('want_to_arrive') == '' && $k=='120' ? 'selected' : ''}} {{old('want_to_arrive') == $k ? 'selected' : ''}}>{{$v}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <div class="text-sm font-medium text-secondary-700 dark:text-gray-400">{{ __('before the flight departure time') }}</div> 
                        </div>

                    </div>


                    <div class="mb-2">
                        {{-- <label class="mb-1 block text-sm font-medium text-secondary-700 dark:text-gray-400" for="arrival_time">{{ __('More') }}</label> --}}
                        <x-textarea label="More information" name="more_information"/>
                        {{-- <textarea id="txtid" name="more_information" rows="4" cols="50" maxlength="200">
                            A nice day is a nice day.
                            Lao Tseu
                        </textarea> --}}

                        <div class="text-sm text-gray-600">
                            {{ __('Please enter the name of your hotel or the address of your drop off location, as well as any additional information we should know.') }}
                        </div>
                    </div>

				{{-- End if tolocation is airport --}}
				@else
                    <div class="text-lg font-bold text-gray-600 my-6 border-b border-gray-200">
                        {{$booking->type == 'roundtrip' ? __('1st') : ''}} {{ __('Trip out pick-up information') }}
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="mb-2">
                            <div class="block text-sm font-medium text-secondary-700 dark:text-gray-400 mb-1" for="arrival_time">
                                {{$booking->type == 'roundtrip' ? __('1st') : ''}} {{ __('Trip out pick-up date') }}
                            </div>
                            <div class="border border-gray-300 bg-white p-2 rounded-md shadow-sm w-full">
                                {{ date('j F Y', strtotime($booking->arrival_date)) }}
                            </div>
                        </div>
        
                        <div class="mb-2">
                            <label class="mb-1 block text-sm font-medium text-secondary-700 dark:text-gray-400" for="arrival_time">{{$booking->type == 'roundtrip' ? __('1st') : ''}} {{ __('Trip out pick-up time') }}</label>
                            <input class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full" 
                                type="time" 
                                name="arrival_time" id="arrival_time"
                            >
                        </div>
                    </div>  

                    <div class="mb-2">
                        {{-- <label class="mb-1 block text-sm font-medium text-secondary-700 dark:text-gray-400" for="arrival_time">{{ __('More') }}</label> --}}
                        <x-textarea label="More information" name="more_information"/>
                        {{-- <textarea id="txtid" name="more_information" rows="4" cols="50" maxlength="200">
                            A nice day is a nice day.
                            Lao Tseu
                        </textarea> --}}

                        <div class="text-sm text-gray-600">
                            {{ __('Please enter the name of your pick-up hotel or address AND the name of your drop-off hotel or location, as well as any additional information we should know.') }}
                        </div>
                    </div>
				@endif

                {{-- Round Trip conditions --}}
                @if ($booking->type == 'roundtrip')
                    @if ($booking->service->fromlocation->is_airport and $booking->service->tolocation->is_airport)
                        <div class="text-lg font-bold text-gray-600 my-6 border-b border-gray-200">
                            {{ __('2nd Trip Pick-up Information') }}
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div class="mb-2">
                                
                                <label class="mb-1 block text-sm font-medium text-secondary-700 dark:text-gray-400" for="return_date">{{ __('2nd Trip arrival date') }}</label>
                                <input class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full" 
                                    type="date" 
                                    name="return_date" id="return_date"
                                    value="{{ old('return_date', date('Y-m-d')) }}"
                                >
                            </div>
            
                            <div class="mb-2">
                            <label class="mb-1 block text-sm font-medium text-secondary-700 dark:text-gray-400" for="return_time_2">{{__('2nd Trip arrival time')}}</label>
                                <input class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full" 
                                    type="time" 
                                    name="return_time_2" id="return_time_2"
                                    value="{{ old('return_time_2', $booking->return_time_2) }}"
                                >
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div class="mb-2">
                                <x-input label="{{ __('2nd Trip arrival airline') }}"  name="return_airline" value="{{ old('return_airline', $booking->return_airline) }}"/>
                            </div>
                            <div class="mb-2">
                                <x-input label="Flight flight"  name="return_flight_number" value="{{ old('return_flight_number', $booking->return_flight_number) }}"/>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="mb-1 block text-sm font-medium text-secondary-700 dark:text-gray-400" for="arrival_time">{{ __('Additional information') }}</label>
                            <textarea class="border border-gray-300 bg-white p-2 rounded-md shadow-sm w-full" name="return_more_information" rows="4" cols="50" ></textarea>
        
                            {{-- <div class="text-sm text-gray-600">
                                {{ __('Please enter the name of your hotel or the address of your drop off location, as well as any additional information we should know.') }}
                            </div> --}}
                        </div>

							
					@elseif ($booking->service->fromlocation->is_airport)
                            <div class="text-lg font-bold text-gray-600 my-6 border-b border-gray-200">
                                {{ __('Departure Information') }}
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div class="mb-2">
                                    
                                    <label class="mb-1 block text-sm font-medium text-secondary-700 dark:text-gray-400" for="return_date">{{ __('Departure date') }}</label>
                                    <input class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full" 
                                        type="date" 
                                        name="return_date" id="return_date"
                                        value="{{ old('return_date', date('Y-m-d')) }}"
                                    >
                                </div>
                
                                <div class="mb-2">
                                <label class="mb-1 block text-sm font-medium text-secondary-700 dark:text-gray-400" for="return_time_2">{{__('Departure time')}}</label>
                                    <input class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full" 
                                        type="time" 
                                        name="return_time_2" id="return_time_2"
                                        value="{{ old('return_time_2', $booking->return_time_2) }}"
                                    >
                                </div>
                            </div>  

                            <div class="grid grid-cols-2 gap-2">
                                <div class="mb-2">
                                    <x-input label="{{ __('Departure airline') }}"  name="return_airline" value="{{ old('return_airline', $booking->return_airline) }}"/>
                                </div>
                                <div class="mb-2">
                                    <x-input label="Flight number"  name="return_flight_number" value="{{ old('return_flight_number', $booking->return_flight_number) }}"/>
                                </div>
                            </div>

                            <div class="mb-2">
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-secondary-700 dark:text-gray-400" for="return_want_to_arrive_2">{{ __('I would like to be at the airport') }}</label>             
                                    <select class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full" name="return_want_to_arrive_2" id="return_want_to_arrive_2">
                                        @foreach($willArriveData as $k => $v)
                                            <option value="{{$k}}" {{old('return_want_to_arrive_2') == '' && $k=='120' ? 'selected' : ''}} {{old('return_want_to_arrive_2') == $k ? 'selected' : ''}}>{{$v}}</option>
                                        @endforeach
                                    </select>
                                </div>
        
                                <div>
                                    <div class="text-sm font-medium text-secondary-700 dark:text-gray-400">{{ __('before the flight departure time') }}</div> 
                                </div>
        
                            </div>

                            <div class="mb-2">
                                <label class="mb-1 block text-sm font-medium text-secondary-700 dark:text-gray-400" for="arrival_time">{{ __('Additional information') }}</label>
                                <textarea class="border border-gray-300 bg-white p-2 rounded-md shadow-sm w-full" name="return_more_information" rows="4" cols="50" ></textarea>
            
                                <div class="text-sm text-gray-600">
                                    {{ __('Enter Hotel or Address and other additional Information if different from arrival drop-off information.') }}
                                </div>
                            </div>

					@elseif ($booking->service->tolocation->is_airport)
                        <div class="text-lg font-bold text-gray-600 my-6 border-b border-gray-200">
                            {{ __('Arrival Information') }}
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div class="mb-2">
                                
                                <label class="mb-1 block text-sm font-medium text-secondary-700 dark:text-gray-400" for="return_date">{{ __('Arrival date') }}</label>
                                <input class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full" 
                                    type="date" 
                                    name="return_date" id="return_date"
                                    value="{{ old('return_date', date('Y-m-d')) }}"
                                >
                            </div>
            
                            <div class="mb-2">
                            <label class="mb-1 block text-sm font-medium text-secondary-700 dark:text-gray-400" for="return_time_2">{{__('Arrival time')}}</label>
                                <input class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full" 
                                    type="time" 
                                    name="return_time_2" id="return_time_2"
                                    value="{{ old('return_time_2', $booking->return_time_2) }}"
                                >
                            </div>
                        </div>  

                        <div class="grid grid-cols-2 gap-2">
                            <div class="mb-2">
                                <x-input label="{{ __('Arrival Airline') }}"  name="return_airline" value="{{ old('return_airline', $booking->return_airline) }}"/>
                            </div>
                            <div class="mb-2">
                                <x-input label="Flight Number"  name="return_flight_number" value="{{ old('return_flight_number', $booking->return_flight_number) }}"/>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="mb-1 block text-sm font-medium text-secondary-700 dark:text-gray-400" for="arrival_time">{{ __('Additional information') }}</label>
                            <textarea class="border border-gray-300 bg-white p-2 rounded-md shadow-sm w-full" name="return_more_information" rows="4" cols="50" ></textarea>
        
                            <div class="text-sm text-gray-600">
                                {{ __('Enter Hotel or Address and other additional information if different from first trip pick-up information.') }}
                            </div>
                        </div>

					{{-- End if tolocation is airport --}}
					@else
                        <div class="text-lg font-bold text-gray-600 my-6 border-b border-gray-200">
                            {{ __('2nd Trip Pick-up Information') }}
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div class="mb-2">
                                
                                <label class="mb-1 block text-sm font-medium text-secondary-700 dark:text-gray-400" for="return_date">{{ __('2nd Trip back pick-up date') }}</label>
                                <input class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full" 
                                    type="date" 
                                    name="return_date" id="return_date"
                                    value="{{ old('return_date', date('Y-m-d')) }}"
                                >
                            </div>
            
                            <div class="mb-2">
                            <label class="mb-1 block text-sm font-medium text-secondary-700 dark:text-gray-400" for="return_time_2">{{__('2nd Trip back pick-up time')}}</label>
                                <input class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full" 
                                    type="time" 
                                    name="return_time_2" id="return_time_2"
                                    value="{{ old('return_time_2', $booking->return_time_2) }}"
                                >
                            </div>
                        </div> 

                        <div class="mb-2">
                            <label class="mb-1 block text-sm font-medium text-secondary-700 dark:text-gray-400" for="arrival_time">{{ __('Additional information') }}</label>
                            <textarea class="border border-gray-300 bg-white p-2 rounded-md shadow-sm w-full" name="return_more_information" rows="4" cols="50" ></textarea>
        
                            <div class="text-sm text-gray-600">
                                {{ __('Enter Hotel or Address and other additional Information if different from trip out drop-off information.') }}
                            </div>
                        </div>
					@endif
		
					
				@endif
				{{-- end roundtrip information --}}

            <div class="flex justify-center mt-4">
                <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                    <button 
                        type="submit" 
                        class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-blue-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-blue-800 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150 sm:text-sm sm:leading-5"
                        {{ (!empty($fullname) && !empty($email) && !empty($phone) ? '' : 'disabled') }}
                        >
                        {{__('Save and Continue')}}
                    </button>
                    {{-- <button wire:click.prevent="save()" type="button" class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-blue-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-blue-800 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                        {{__('Save and Continue')}}
                    </button> --}}
                </span>
            </div>
            
        {!! Form::close() !!}

    </div>{{-- main div --}}
</div>
