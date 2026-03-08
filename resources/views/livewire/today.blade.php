<div class="px-5 py-6">
    {{-- Header --}}
    <div class="mb-6">
        <p class="text-sm text-gray-400 font-medium">{{ now()->format('l, M j') }}</p>
        <h1 class="text-2xl font-bold mt-1">Today's Habits</h1>
    </div>

    @if($total > 0)
        {{-- Progress Summary --}}
        <div class="bg-gradient-to-br from-violet-600/20 to-indigo-600/20 border border-violet-500/20 rounded-2xl p-5 mb-6">
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
                <p class="text-sm text-emerald-400/80 font-medium mt-3">All done for today!</p>
            @endif
        </div>

        {{-- Habit List --}}
        <div class="space-y-3">
            @foreach($habits as $habit)
                @php
                    $isCompleted = $habit->isCompletedToday();
                    $streak = $habit->currentStreak();
                @endphp

                <div wire:key="habit-{{ $habit->id }}"
                     class="habit-card bg-gray-900 border rounded-2xl p-4 flex items-center gap-4 transition-all
                            {{ $isCompleted ? 'border-emerald-500/30 bg-emerald-950/20' : 'border-gray-800/50' }}">

                    {{-- Toggle area (tap to complete) --}}
                    <button wire:click="toggleHabit({{ $habit->id }})"
                            class="flex-shrink-0 w-10 h-10 rounded-full border-2 flex items-center justify-center transition-all
                                   {{ $isCompleted ? 'border-emerald-500 bg-emerald-500/20' : 'border-gray-600 active:border-violet-400' }}">
                        @if($isCompleted)
                            <svg class="w-5 h-5 text-emerald-400 check-animate" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                        @endif

                        {{-- Loading spinner --}}
                        <div wire:loading wire:target="toggleHabit({{ $habit->id }})">
                            <svg class="w-5 h-5 text-gray-500 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25" />
                                <path fill="currentColor" class="opacity-75" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                            </svg>
                        </div>
                    </button>

                    {{-- Emoji --}}
                    <span class="text-2xl flex-shrink-0">{{ $habit->emoji }}</span>

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-[15px] truncate {{ $isCompleted ? 'text-gray-400 line-through' : 'text-white' }}">
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
                                             {{ $streak >= 7 ? 'text-orange-400' : 'text-gray-400' }}">
                                    🔥 {{ $streak }}d
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Edit button --}}
                    <a href="/habits/{{ $habit->id }}/edit" wire:navigate
                       class="flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center text-gray-500 active:bg-gray-800 active:text-gray-300 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" />
                        </svg>
                    </a>
                </div>
            @endforeach
        </div>

        {{-- FAB --}}
        <a href="/habits/create" wire:navigate
           class="fixed right-5 bottom-24 w-14 h-14 bg-violet-600 hover:bg-violet-500 active:bg-violet-700 rounded-full flex items-center justify-center shadow-lg shadow-violet-600/30 transition-colors"
           style="margin-bottom: var(--inset-bottom, 0px);">
            <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
        </a>

    @else
        {{-- Empty State --}}
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <div class="w-20 h-20 rounded-full bg-gray-800/50 flex items-center justify-center mb-4">
                <svg class="w-10 h-10 text-gray-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-300 mb-1">No habits yet</h3>
            <p class="text-sm text-gray-500 max-w-[240px]">Start building better routines by adding your first habit.</p>
            <a href="/habits/create" wire:navigate
               class="mt-6 px-6 py-3 bg-violet-600 hover:bg-violet-500 active:bg-violet-700 text-white text-sm font-semibold rounded-xl transition-colors">
                Add Your First Habit
            </a>
        </div>
    @endif
</div>
