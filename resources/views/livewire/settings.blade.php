<div class="px-5 py-6">
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Settings</h1>
    </div>

    {{-- App Info --}}
    <div class="bg-gray-900 border border-gray-800/50 rounded-2xl overflow-hidden mb-4">
        <div class="px-5 py-4 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-violet-500 to-indigo-600 flex items-center justify-center text-xl font-bold">
                DH
            </div>
            <div>
                <h2 class="font-semibold">Daily Habits</h2>
                <p class="text-sm text-gray-400">v1.0.0</p>
            </div>
        </div>
    </div>

    {{-- Notifications Section --}}
    <div class="mb-4">
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-2 mb-2">Notifications</h3>
        <div class="bg-gray-900 border border-gray-800/50 rounded-2xl overflow-hidden">
            <button wire:click="requestPermission" class="w-full px-5 py-4 flex items-center justify-between active:bg-gray-800/50 transition-colors">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-violet-600/20 flex items-center justify-center">
                        <svg class="w-4 h-4 text-violet-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                        </svg>
                    </div>
                    <div class="text-left">
                        <p class="text-sm font-medium">Notification Permission</p>
                        <p class="text-xs text-gray-500">
                            @if($permissionStatus === 'granted')
                                Notifications are enabled
                            @elseif($permissionStatus === 'denied')
                                Tap to request again
                            @else
                                Tap to enable notifications
                            @endif
                        </p>
                    </div>
                </div>
                @if($permissionStatus === 'granted')
                    <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-400">
                        Granted
                    </span>
                @elseif($permissionStatus === 'denied')
                    <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-red-500/20 text-red-400">
                        Denied
                    </span>
                @else
                    <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-gray-800 text-gray-400">
                        Unknown
                    </span>
                @endif
            </button>
            
            @if($permissionStatus === 'granted')
            <button wire:click="sendImmediateNotification" class="w-full px-5 py-3 flex items-center justify-between active:bg-gray-800/50 transition-colors border-t border-gray-800/30">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-green-600/20 flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </div>
                    <div class="text-left">
                        <p class="text-sm font-medium">Test Now (2 sec)</p>
                        <p class="text-xs text-gray-500">Immediate test</p>
                    </div>
                </div>
            </button>
            <button wire:click="rescheduleAllHabitsTest" class="w-full px-5 py-3 flex items-center justify-between active:bg-gray-800/50 transition-colors border-t border-gray-800/30">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-orange-600/20 flex items-center justify-center">
                        <svg class="w-4 h-4 text-orange-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </div>
                    <div class="text-left">
                        <p class="text-sm font-medium">Test Habits (15 sec)</p>
                        <p class="text-xs text-gray-500">Schedule all habits in 15 sec</p>
                    </div>
                </div>
            </button>
            @endif
        </div>
    </div>

    {{-- Stats Section --}}
    <div class="mb-4">
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-2 mb-2">Statistics</h3>
        <div class="bg-gray-900 border border-gray-800/50 rounded-2xl overflow-hidden divide-y divide-gray-800/50">
            <div class="px-5 py-4 flex items-center justify-between">
                <span class="text-sm text-gray-300">Active Habits</span>
                <span class="text-sm font-semibold">{{ $totalHabits }}</span>
            </div>
            <div class="px-5 py-4 flex items-center justify-between">
                <span class="text-sm text-gray-300">Completed Today</span>
                <span class="text-sm font-semibold">{{ $completionsToday }}</span>
            </div>
            <div class="px-5 py-4 flex items-center justify-between">
                <span class="text-sm text-gray-300">Longest Streak</span>
                <span class="text-sm font-semibold">{{ $longestStreak }} {{ $longestStreak === 1 ? 'day' : 'days' }}</span>
            </div>
        </div>
    </div>

    {{-- About --}}
    <div>
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-2 mb-2">About</h3>
        <div class="bg-gray-900 border border-gray-800/50 rounded-2xl overflow-hidden divide-y divide-gray-800/50">
            <div class="px-5 py-4 flex items-center justify-between">
                <span class="text-sm text-gray-300">Built with</span>
                <span class="text-sm text-gray-400">NativePHP Mobile v3</span>
            </div>
            <div class="px-5 py-4 flex items-center justify-between">
                <span class="text-sm text-gray-300">Notifications by</span>
                <span class="text-sm text-gray-400">local-notifications v1.1.0</span>
            </div>
        </div>
    </div>
</div>
