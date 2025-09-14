<x-filament-panels::page>
    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        <div class="w-16 h-16 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ Auth::user()->name }}
                        </h2>
                        @if(Auth::user()->puesto)
                            <p class="text-lg text-primary-600 dark:text-primary-400 font-medium">
                                {{ Auth::user()->puesto }}
                            </p>
                        @endif
                        @if(Auth::user()->empresa)
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ Auth::user()->empresa }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{ $this->userInfolist }}
    </div>
</x-filament-panels::page>
