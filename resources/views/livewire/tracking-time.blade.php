<x-slot name="header">
    <h1 class="text-gray-900">{{__('Tracking Time')}}</h1>
</x-slot>


<div class="py-4 max-w-7xl mx-auto sm:px-6 lg:px-8 ">
    {{-- <div class="flex justify-end mb-2">
        <div class="">
            <x-datetime-picker
                label="{{ __('From Date') }}"
                placeholder="From Date"
                wire:model.defer="normalPicker"
            />
        </div>
    
        <div class="ml-2">
            <x-datetime-picker
                label="{{ __('To Date') }}"
                placeholder="To Date"
                wire:model.defer="normalPicker"
            />
        </div>
    </div> --}}

    {{-- Projects with tasks --}}

    <div class="flex gap-x-2 mb-4 justify-end">
        <div class="py-2 px-4 bg-white shaddow-sm rounded">
            <div class="text-xs up text-gray-500 uppercase">{{ __('Today') }}</div>
            <div>
                @if ($today > 60)
                    {{date('H'.' \h\o\u\r\s '. 'i'.' \m\i\n\s', mktime(0,$today))}}
                @else
                    {{date('i'.' \m\i\n\s', mktime(0,$today))}}
                @endif 
            </div>
        </div>
        <div class="py-2 px-4 bg-white shaddow-sm rounded">
            <div class="text-xs up text-gray-500 uppercase">{{ __('This Week') }}</div>
            <div>
                @if ($thisWeek > 60)
                    {{date('H'.' \h\o\u\r\s '. 'i'.' \m\i\n\s', mktime(0,$thisWeek))}}
                @else
                    {{date('i'.' \m\i\n\s', mktime(0,$thisWeek))}}
                @endif 
            </div>
        </div>
        <div class="py-2 px-4 bg-white shaddow-sm rounded">
            <div class="text-xs up text-gray-500 uppercase">{{ __('This Month') }}</div>
            <div>
                @if ($thisMonth > 60)
                    {{date('H'.' \h\o\u\r\s '. 'i'.' \m\i\n\s', mktime(0,$thisMonth))}}
                @else
                    {{date('i'.' \m\i\n\s', mktime(0,$thisMonth))}}
                @endif 
            </div>
        </div>
        <div class="py-2 px-4 bg-white shaddow-sm rounded bg-blue-100">
            <div class="text-xs up text-gray-500 uppercase">{{ __('Last Month') }}</div>
            <div>
                @if ($lastMonth > 60)
                    {{date('H'.' \h\o\u\r\s '. 'i'.' \m\i\n\s', mktime(0,$lastMonth))}}
                @else
                    {{date('i'.' \m\i\n\s', mktime(0,$lastMonth))}}
                @endif 
            </div>
        </div>
    </div>

    <div class="mb-2">
        <x-button wire:click="create()" icon="plus" primary label="New Task" />
    </div>
    @if($modal)
        @include('livewire.tasks.create')
    @endif

    @if($openMilestoneModal)
        @include('livewire.tasks.create-milestone')
    @endif
    
    @foreach ($tasks as $task)
        <div class="mt-8">
            <div class="flex">
                <div class="flex w-1/2">
                    <div class="{{ $task->status_id == 2 ? 'bg-green-500' : 'bg-violet-500' }} uppercase px-2 py-1 text-white text-sm">{{$task->status->name}}</div>
                    <div class="ml-2 py-1 flex">
                        <div class="text-gray-600 text-xs items-center p-1">{{ $task->milestones->count() }} {{ __('milestones') }}</div>
                        {{-- <div><x-button xs wire:click="addMilestone({{$task->id}})" flat icon="plus" label="Add milestone" /></div> --}}
                        
                    </div>
                </div>
                <div class="flex w-1/2">
                    <div class="upercase text-xs text-gray-600 w-1/4 text-center items-center uppercase align-bottom">{{ __('Project') }}</div>
                    <div class="upercase text-xs text-gray-600 w-1/4 text-center items-center uppercase align-bottom">{{ __('Created') }}</div>
                    <div class="upercase text-xs text-gray-600 w-1/4 text-center items-center uppercase align-bottom">{{ __('Timing') }}</div>
                    <div class="upercase text-xs text-gray-600 w-1/4 text-center items-center"></div>
                </div>
            </div>
            <div class="flex bg-white px-4 py-2 shadow">
                <div class="text-gray-800 flex-none w-1/2">
                    <a href="{{ url('trackingtime/'.$task->id) }}"> {{$task->name}}</a>
                </div>
                <div class="flex w-1/2">
                    <div class="upercase text-gray-600 w-1/4 text-center items-text-bottom"> {{$task->project->name}}</div>
                    <div class="upercase text-gray-600 w-1/4 text-center items-text-bottom">{{$task->created_at->diffForHumans()}}</div>
                    <div class="upercase text-gray-600 w-1/4 text-center items-text-bottom">
                        @if ($task->hours > 60)
                            {{date('H'.' \h\o\u\r\s '. 'i'.' \m\i\n\s', mktime(0,$task->hours))}}
                        @else
                            {{date('i'.' \m\i\n\s', mktime(0,$task->hours))}}
                        @endif 
                    </div>
                    <div class="upercase text-gray-600 w-1/4 align-center items-center"> 
                        @if ($task->status_id == 1)
                            <x-button wire:click="completeStatus({{$task->id}})" icon="check" positive/>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endforeach

  
</div>


