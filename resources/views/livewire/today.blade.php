<div class="px-5 py-6">
    {{-- Header --}}
    <div class="mb-6">
        <p class="text-sm text-gray-400 font-medium">{{ now()->format('l, M j') }}</p>
        <h1 class="text-2xl font-bold mt-1">Today's Habits</h1>
    </div>

    {{-- Progress Summary --}}
    <div class="bg-gradient-to-br from-violet-600/20 to-indigo-600/20 border border-violet-500/20 rounded-2xl p-5 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-violet-300/80 font-medium">Daily Progress</p>
                <p class="text-3xl font-bold mt-1">0<span class="text-lg text-gray-400">/0</span></p>
            </div>
            <div class="w-16 h-16 rounded-full border-4 border-gray-700 flex items-center justify-center">
                <span class="text-sm font-bold text-gray-400">0%</span>
            </div>
        </div>
    </div>

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
</div>
