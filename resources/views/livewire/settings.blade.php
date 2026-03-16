<div class="px-5 py-6 page-enter" wire:poll.30s>
    {{-- Header --}}
    <div class="mb-6 section-enter">
        <h1 class="text-2xl font-bold">Settings</h1>
    </div>

    {{-- App Info --}}
    <div class="section-enter bg-gray-900 border border-gray-800/50 rounded-2xl overflow-hidden mb-4" style="animation-delay: 0.05s">
        <div class="px-5 py-4 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-violet-500 to-indigo-600 flex items-center justify-center text-xl font-bold shadow-lg shadow-violet-600/20">
                DH
            </div>
            <div>
                <h2 class="font-semibold">Daily Habits</h2>
                <p class="text-sm text-gray-400">v1.3.0</p>
            </div>
        </div>
    </div>

    {{-- Notifications Section --}}
    <div class="mb-4 section-enter" style="animation-delay: 0.1s">
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-2 mb-2">Notifications</h3>
        <div class="bg-gray-900 border border-gray-800/50 rounded-2xl overflow-hidden">
            <button wire:click="requestPermission" class="card-press w-full px-5 py-4 flex items-center justify-between active:bg-gray-800/50 transition-colors">
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
            <button wire:click="sendTestNotification" class="card-press w-full px-5 py-3 flex items-center justify-between active:bg-gray-800/50 transition-colors border-t border-gray-800/30">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-green-600/20 flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                        </svg>
                    </div>
                    <div class="text-left">
                        <p class="text-sm font-medium">Send Test Notification</p>
                        <p class="text-xs text-gray-500">Fires in 15 seconds with habit data</p>
                    </div>
                </div>
            </button>
            @endif
        </div>
    </div>

    {{-- Stats Section --}}
    <div class="mb-4 section-enter" style="animation-delay: 0.15s">
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-2 mb-2">Statistics</h3>
        <div class="bg-gray-900 border border-gray-800/50 rounded-2xl overflow-hidden divide-y divide-gray-800/50">
            <div class="px-5 py-4 flex items-center justify-between">
                <span class="text-sm text-gray-300">Active Habits</span>
                <span class="text-sm font-semibold">{{ $totalHabits }}</span>
            </div>
            <div class="px-5 py-4 flex items-center justify-between">
                <span class="text-sm text-gray-300">Completed Today</span>
                <span class="text-sm font-semibold {{ $completionsToday > 0 ? 'text-emerald-400' : '' }}">{{ $completionsToday }}</span>
            </div>
            <div class="px-5 py-4 flex items-center justify-between">
                <span class="text-sm text-gray-300">Longest Streak</span>
                <span class="text-sm font-semibold {{ $longestStreak >= 7 ? 'text-orange-400' : '' }}">{{ $longestStreak }} {{ $longestStreak === 1 ? 'day' : 'days' }}</span>
            </div>
        </div>
    </div>

    {{-- Debug --}}
    <div class="mb-4 section-enter" style="animation-delay: 0.2s">
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-2 mb-2">Developer</h3>
        <div class="bg-gray-900 border border-gray-800/50 rounded-2xl overflow-hidden">
            <a href="/notification-debug" wire:navigate class="card-press w-full px-5 py-4 flex items-center justify-between active:bg-gray-800/50 transition-colors">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-orange-600/20 flex items-center justify-center">
                        <svg class="w-4 h-4 text-orange-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 12.75c1.148 0 2.278.08 3.383.237 1.037.146 1.866.966 1.866 2.013 0 3.728-2.35 6.75-5.25 6.75S6.75 18.728 6.75 15c0-1.046.83-1.867 1.866-2.013A24.204 24.204 0 0 1 12 12.75Zm0 0c2.883 0 5.647.508 8.207 1.44a23.91 23.91 0 0 1-1.152-6.44l-.003-.082a76.669 76.669 0 0 0-.327-3.698A2.25 2.25 0 0 0 16.476 2h-8.95a2.25 2.25 0 0 0-2.25 1.97 76.59 76.59 0 0 0-.327 3.698L4.945 7.75a23.91 23.91 0 0 1-1.152 6.44A24.108 24.108 0 0 1 12 12.75Z" />
                        </svg>
                    </div>
                    <div class="text-left">
                        <p class="text-sm font-medium">Notification Debug</p>
                        <p class="text-xs text-gray-500">Test tap events with live event log</p>
                    </div>
                </div>
                <svg class="w-4 h-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>
            </a>
        </div>
    </div>

    {{-- About --}}
    <div class="section-enter" style="animation-delay: 0.25s">
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-2 mb-2">About</h3>
        <div class="bg-gray-900 border border-gray-800/50 rounded-2xl overflow-hidden divide-y divide-gray-800/50">
            <div class="px-5 py-4 flex items-center justify-between">
                <span class="text-sm text-gray-300">Built with</span>
                <span class="text-sm text-gray-400">NativePHP Mobile v3</span>
            </div>
            <div class="px-5 py-4 flex items-center justify-between">
                <span class="text-sm text-gray-300">Notifications by</span>
                <span class="text-sm text-gray-400">local-notifications v1.3.4</span>
            </div>
        </div>
    </div>
</div>
