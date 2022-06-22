<div id="defaultModal" tabindex="-1" class="fixed h-screen m-auto z-50 w-full">
    <div class="relative p-4 h-full">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <!-- Modal header -->
            <div class="flex justify-between items-start p-4 rounded-t border-b dark:border-gray-600">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Filters
                </h3>
                <button wire:click="closeModal()" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-toggle="defaultModal">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>  
                </button>
            </div>
            <!-- Modal body -->
            <div class="p-6">
               <div class="">
                    <div class="text-gray-600 text-lg font-bold">Date range</div>
                    <div class="text-gray-400 text-sm">Service date</div>
               </div>
               <div class="mb-4">
                    {{-- <label for="fromDate" class="block text-gray-700 text-sm font-bold mb-2">{{__('Between')}} </label> --}}
                    <input type="date" class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="fromDate">
                </div>

                <div class="mb-4">
                    {{-- <label for="toDate" class="block text-gray-700 text-sm font-bold mb-2">{{__('And')}} </label> --}}
                    <input type="date" class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="toDate">
                </div>

                {{-- <div class="border-b border-gray-300 py-4"></div> --}}

                <div class="">
                    <div class="text-gray-600 text-lg font-bold">By location</div>
                    {{-- <div class="text-gray-400 text-sm">Service date</div> --}}
               </div>
               <div class="mb-4">
                    <label for="fromLocation" class="block text-gray-700 text-sm font-bold mb-2">{{__('From Location')}} </label>
                    <input type="text" class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="fromLocation">
                </div>

                <div class="mb-4">
                    <label for="toLocation" class="block text-gray-700 text-sm font-bold mb-2">{{__('To Location')}} </label>
                    <input type="text" class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="toLocation">
                </div>
            </div>
            <!-- Modal footer -->
            <div class="flex justify-end items-center p-2 space-x-2 rounded-b border-t border-gray-200 dark:border-gray-600">
                <button wire:click="closeModal()" type="button" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded border border-gray-200 text-sm px-5 py-2 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">Close</button>
                <button type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded text-sm px-5 py-2 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Search</button>
            </div>
        </div>
    </div>
</div>