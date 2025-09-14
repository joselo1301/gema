<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Información del Usuario
        </x-slot>

        <div class="space-y-3">
            @php
                $userInfo = $this->getUserInfo();
            @endphp
            
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                    <x-heroicon-o-user-circle class="w-10 h-10 text-primary-600 dark:text-primary-400" />
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $userInfo['name'] }}
                    </h3>
                    @if($userInfo['puesto'])
                        <p class="text-sm text-primary-600 dark:text-primary-400 font-medium">
                            {{ $userInfo['puesto'] }}
                        </p>
                    @endif
                    @if($userInfo['empresa'])
                        <p class="text-xs text-gray-600 dark:text-gray-400">
                            {{ $userInfo['empresa'] }}
                        </p>
                    @endif
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-3 border-t border-gray-200 dark:border-gray-700">
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Email
                    </p>
                    <p class="text-sm text-gray-900 dark:text-white">
                        {{ $userInfo['email'] }}
                    </p>
                </div>
                
                @if($userInfo['roles'])
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Roles
                        </p>
                        <p class="text-sm text-gray-900 dark:text-white">
                            {{ $userInfo['roles'] }}
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
