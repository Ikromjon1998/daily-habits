<div class="px-5 py-6 page-enter" wire:poll.30s>
    {{-- Notification Tap Banner (Livewire) --}}
    @if($tapBanner)
        <div class="mb-4 px-4 py-3 bg-orange-500/20 border border-orange-500/30 rounded-xl flex items-center justify-between">
            <div class="flex items-center gap-2 min-w-0">
                <span class="text-orange-400 flex-shrink-0">&#x1F514;</span>
                <p class="text-sm text-orange-300 truncate">{{ $tapBanner }}</p>
            </div>
            <button wire:click="dismissBanner" class="text-orange-400/60 active:text-orange-300 flex-shrink-0 ml-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif

    {{-- Debug Panel --}}
    <div class="mb-4 px-4 py-3 bg-gray-900 border border-gray-800/50 rounded-xl text-xs font-mono"
         x-data="{
             logs: [],
             add(msg) {
                 const t = new Date().toLocaleTimeString('en-US', {hour12:false});
                 this.logs.unshift(t + ' ' + msg);
                 if (this.logs.length > 20) this.logs.pop();
             }
         }"
         x-init="
             add('page loaded, readyState=' + document.readyState);
             add('Livewire=' + (typeof window.Livewire !== 'undefined' ? 'yes' : 'no'));

             document.addEventListener('livewire:init', () => add('livewire:init fired'));
             document.addEventListener('livewire:navigated', () => add('livewire:navigated fired'));

             if (window.Livewire && window.Livewire.on) {
                 window.Livewire.on('native:Ikromjon\\LocalNotifications\\Events\\NotificationTapped', (data) => {
                     add('JS GOT NotificationTapped: ' + JSON.stringify(data));
                 });
             }

             document.addEventListener('livewire:init', () => {
                 if (window.Livewire && window.Livewire.on) {
                     window.Livewire.on('native:Ikromjon\\LocalNotifications\\Events\\NotificationTapped', (data) => {
                         add('JS GOT NotificationTapped (after init): ' + JSON.stringify(data));
                     });
                 }
             });
         ">
        <p class="text-gray-500 font-semibold mb-1">Debug Log</p>
        <template x-for="(log, i) in logs" :key="i">
            <p class="text-gray-400 leading-relaxed" x-text="log"></p>
        </template>
        <p x-show="logs.length === 0" class="text-gray-600">Waiting for events...</p>
    </div>

    {{-- Header --}}
    <div class="mb-6 section-enter">
        <p class="text-sm text-gray-400 font-medium">{{ now()->format('l, M j') }}</p>
        <p class="text-3xl font-bold mt-1"
           x-data="{ time: '', greeting: '' }"
           x-init="
               const update = () => {
                   const now = new Date();
                   time = now.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
                   const h = now.getHours();
                   greeting = h < 12 ? 'Good morning' : h < 17 ? 'Good afternoon' : 'Good evening';
               };
               update();
               setInterval(update, 10000);
           ">
            <span class="text-lg text-gray-400 font-medium block" x-text="greeting">Good morning</span>
            <span x-text="time">{{ now()->format('g:i A') }}</span>
        </p>
    </div>

    @if($total > 0)
        {{-- Progress Summary --}}
        <div class="section-enter {{ $percentage === 100 ? 'celebration-shimmer' : '' }} bg-gradient-to-br from-violet-600/20 to-indigo-600/20 border border-violet-500/20 rounded-2xl p-5 mb-6"
             style="animation-delay: 0.05s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-violet-300/80 font-medium">Daily Progress</p>
                    <p class="text-3xl font-bold mt-1">
                        {{ $completed }}<span class="text-lg text-gray-400">/{{ $total }}</span>
                    </p>
                </div>

                {{-- Progress Ring --}}
                <div class="relative w-16 h-16">
                    <svg class="w-16 h-16 -rotate-90" viewBox="0 0 36 36">
                        <circle cx="18" cy="18" r="15.5" fill="none" stroke="currentColor"
                                stroke-width="3" class="text-gray-700/50" />
                        <circle cx="18" cy="18" r="15.5" fill="none" stroke="currentColor"
                                stroke-width="3" stroke-linecap="round"
                                stroke-dasharray="{{ $percentage }}, 100"
                                class="{{ $percentage === 100 ? 'text-emerald-400' : 'text-violet-400' }} progress-ring" />
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-xs font-bold {{ $percentage === 100 ? 'text-emerald-400' : 'text-white' }}">
                            {{ $percentage }}%
                        </span>
                    </div>
                </div>
            </div>

            @if($percentage === 100)
                <p class="text-sm text-emerald-400 font-medium mt-3 fade-in">All done for today!</p>
            @endif
        </div>

        {{-- Habit List --}}
        <div class="space-y-3">
            @foreach($habits as $habit)
                @php
                    $isCompleted = $habit->isCompletedToday();
                    $streak = $habit->currentStreak();
                    $milestone = $habit->streakMilestone();
                    $weeklyData = $habit->weeklyCompletions();
                    $dayLabels = ['M', 'T', 'W', 'T', 'F', 'S', 'S'];
                @endphp

                <div wire:key="habit-{{ $habit->id }}" class="slide-up" style="animation-delay: {{ $loop->index * 0.06 }}s">
                    {{-- Milestone Banner --}}
                    @if($milestone)
                        <div class="mx-2 mb-1 px-3 py-1.5 rounded-t-xl bg-gradient-to-r from-amber-500/20 to-orange-500/20 border border-b-0 border-amber-500/30 flex items-center gap-2">
                            <span class="text-sm">{{ $milestone >= 100 ? '👑' : ($milestone >= 30 ? '⭐' : '🎉') }}</span>
                            <span class="text-xs font-semibold text-amber-400">{{ $milestone }}-day streak!</span>
                        </div>
                    @endif

                    <div class="bg-gray-900 border rounded-2xl p-4 transition-all duration-300
                                {{ $isCompleted ? 'border-emerald-500/30 bg-emerald-950/20' : 'border-gray-800/50' }}
                                {{ $milestone ? 'rounded-t-lg' : '' }}">

                        <div class="flex items-center gap-4">
                            {{-- Toggle area --}}
                            <button wire:click="toggleHabit({{ $habit->id }})"
                                    class="habit-card flex-shrink-0 w-10 h-10 rounded-full border-2 flex items-center justify-center transition-all duration-300
                                           {{ $isCompleted ? 'border-emerald-500 bg-emerald-500/20 glow-complete' : 'border-gray-600 active:border-violet-400' }}">
                                @if($isCompleted)
                                    <svg class="w-5 h-5 text-emerald-400 check-animate" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                    </svg>
                                @endif

                                <div wire:loading wire:target="toggleHabit({{ $habit->id }})">
                                    <svg class="w-5 h-5 text-gray-500 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25" />
                                        <path fill="currentColor" class="opacity-75" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                    </svg>
                                </div>
                            </button>

                            {{-- Emoji --}}
                            <span class="text-2xl flex-shrink-0 transition-transform duration-300 {{ $isCompleted ? 'scale-90 opacity-60' : '' }}">{{ $habit->emoji }}</span>

                            {{-- Info --}}
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-[15px] truncate transition-colors duration-300 {{ $isCompleted ? 'text-gray-500 line-through' : 'text-white' }}">
                                    {{ $habit->name }}
                                </p>
                                @if($habit->description)
                                    <p class="text-xs text-gray-500 truncate mt-0.5">{{ $habit->description }}</p>
                                @endif
                                <div class="flex items-center gap-3 mt-1.5">
                                    <span class="text-[11px] text-gray-500 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                        </svg>
                                        {{ \Carbon\Carbon::parse($habit->reminder_time)->format('g:i A') }}
                                    </span>

                                    @if($streak > 0)
                                        <span class="text-[11px] font-semibold flex items-center gap-0.5
                                            @if($streak >= 30) text-red-400
                                            @elseif($streak >= 7) text-orange-400
                                            @elseif($streak >= 3) text-amber-400
                                            @else text-gray-400
                                            @endif">
                                            🔥 {{ $streak }}d
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Edit button --}}
                            <a href="/habits/{{ $habit->id }}/edit" wire:navigate
                               class="card-press flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center text-gray-500 active:bg-gray-800 active:text-gray-300 transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" />
                                </svg>
                            </a>
                        </div>

                        {{-- Weekly Calendar Dots --}}
                        <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-800/30 px-1">
                            @foreach($weeklyData as $date => $done)
                                @php
                                    $isToday = $date === now()->toDateString();
                                    $isFuture = $date > now()->toDateString();
                                @endphp
                                <div class="flex flex-col items-center gap-1.5">
                                    <span class="text-[9px] font-medium {{ $isToday ? 'text-violet-400' : 'text-gray-600' }}">
                                        {{ $dayLabels[$loop->index] }}
                                    </span>
                                    <div class="w-5 h-5 rounded-full flex items-center justify-center transition-all duration-300
                                        @if($done)
                                            @if($streak >= 30) bg-red-500/30 ring-1 ring-red-500/50
                                            @elseif($streak >= 7) bg-orange-500/30 ring-1 ring-orange-500/50
                                            @elseif($streak >= 3) bg-amber-500/30 ring-1 ring-amber-500/50
                                            @else bg-emerald-500/30 ring-1 ring-emerald-500/50
                                            @endif
                                        @elseif($isToday) ring-1 ring-violet-500/40
                                        @elseif($isFuture) bg-gray-800/20
                                        @else bg-gray-800/40
                                        @endif">
                                        @if($done)
                                            <div class="w-2 h-2 rounded-full
                                                @if($streak >= 30) bg-red-400
                                                @elseif($streak >= 7) bg-orange-400
                                                @elseif($streak >= 3) bg-amber-400
                                                @else bg-emerald-400
                                                @endif"></div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- FAB --}}
        <a href="/habits/create" wire:navigate
           class="fab-enter fixed right-5 bottom-24 w-14 h-14 bg-violet-600 active:bg-violet-700 rounded-full flex items-center justify-center shadow-lg shadow-violet-600/30 transition-transform active:scale-90"
           style="margin-bottom: var(--inset-bottom, 0px);">
            <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
        </a>

    @else
        {{-- Empty State --}}
        <div class="flex flex-col items-center justify-center py-16 text-center page-enter">
            <div class="w-20 h-20 rounded-full bg-gray-800/50 flex items-center justify-center mb-4">
                <svg class="w-10 h-10 text-gray-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-300 mb-1">No habits yet</h3>
            <p class="text-sm text-gray-500 max-w-[240px]">Start building better routines by adding your first habit.</p>
            <a href="/habits/create" wire:navigate
               class="mt-6 px-6 py-3 bg-violet-600 active:bg-violet-700 text-white text-sm font-semibold rounded-xl transition-transform active:scale-95">
                Add Your First Habit
            </a>
        </div>
    @endif
</div>
