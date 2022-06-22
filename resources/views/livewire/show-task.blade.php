 <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 ">
    <div class="px-4 sm:px-6 md:px-8 mb-12 sm:mb-16 md:mb-20 border-t pt-6">
        <h2 class="sm:text-lg sm:leading-snug font-semibold tracking-wide uppercase text-blue-600">{{ __('Task') }}</h2>
        <p class="text-xl sm:text-2xl lg:text-4xl leading-none font-extrabold text-gray-900 tracking-tight">
            {{ $task->name }}
        </p>
        <div>
            <div></div>
            <div></div>
        </div>
        <div class="flex py-4 gap-3">
            <div class="{{ $task->status_id == 2 ? 'bg-green-500' : 'bg-violet-500' }} uppercase px-2 py-1 text-white text-sm">{{$task->status->name}}</div>
            <div class="upercase text-gray-600 text-center items-text-bottom"> <span class="text-gray-700">{{ __('Project') }}:</span> <span class="font-bold ">{{$task->project->name}}</span></div>
            <div class="upercase text-gray-600 text-center items-text-bottom"><span class="text-gray-700">{{ __('Created') }}:</span> <span class="font-bold ">{{$task->created_at->diffForHumans()}}</span></div>
            <div class="upercase text-gray-600 text-center items-text-bottom">
                <span class="text-gray-700">{{ __('Timing') }}:</span> 
                <span class="font-bold ">
                    @if ($task->hours > 60)
                        {{date('H'.' \h\o\u\r\s \a\n\d '. 'i'.' \m\i\n\s', mktime(0,$task->hours))}}
                    @else
                        {{date('i'.' \m\i\n\s', mktime(0,$task->hours))}}
                    @endif   
                </span>
            </div>
        </div>

        <p class="mb-4 border-b-2 pb-4">{{ $task->details }}</p>
        <div class="py-4 "></div>

        <div class="mt-2">
            <x-button wire:click="createMilestone()" icon="plus" primary label="New Milestone" />
        </div>

        @if($openMilestoneModal)
            @include('livewire.tasks.create-milestone')
        @endif

        <div class="py-4">
            @foreach ($milestones as $item)
                <div class="flex mt-2 bg-white px-4 py-2 shadow border-l-2 {{ $item->completed == 1 ? 'border-green-500' : 'border-violet-500' }}">
                    <div class="text-gray-800 flex-none w-1/2">
                        {{$item->name}}
                    </div>
                    <div class="flex w-1/2">
                        <div class="upercase text-gray-600 w-1/4 text-center items-text-bottom">{{$item->created_at->diffForHumans()}}</div>
                        <div class="upercase text-gray-600 w-1/4 text-center items-text-bottom">
                            <span>
                                @if ($item->total > 60)
                                    {{date('H'.' \h\o\u\r\s \a\n\d '. 'i'.' \m\i\n\s', mktime(0,$item->total))}}
                                @else
                                    {{date('i'.' \m\i\n\s', mktime(0,$item->total))}}
                                @endif    
                            </span>
                        </div>
                        <div class="upercase text-white w-1/4 text-center items-text-bottom {{ $item->completed == 1 ? 'bg-green-500' : 'bg-violet-500' }} rounded">{{ $item->completed == 1 ? 'Completed' : 'In Progress' }}</div>
                        <div class="upercase text-gray-600 w-1/4 align-center items-center"> 
                            @if ($item->status_id == 1)
                                <x-button wire:click="completeStatus({{$item->id}})" icon="check" positive/>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
