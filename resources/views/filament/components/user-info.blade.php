@auth
<div class="user-info-topbar flex px-6 py-3 text-sm text-gray-700 dark:text-gray-200">
    <div class="flex space-x-3">        
        <div class="flex flex-col items-end w-full">
            <span class="font-semibold text-gray-900 dark:text-white">{{ auth()->user()->name }}</span>
            <div class="flex space-x-2 mt-0.5 text-xs text-primary-600 dark:text-primary-400">
                @if(auth()->user()->puesto)
                    <span >{{ auth()->user()->puesto }}</span>
                    @if(auth()->user()->empresa)
                        <span class="mx-1">-</span>
                        <span>{{ auth()->user()->empresa }}</span>
                    @endif
                @elseif(auth()->user()->empresa)
                    <span>{{ auth()->user()->empresa }}</span>
                @endif
            </div>
        </div>
    </div>
</div>
@endauth
