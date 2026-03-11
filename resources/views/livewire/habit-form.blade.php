<div class="px-5 py-6 page-enter">
    {{-- Header --}}
    <div class="flex items-center gap-3 mb-8 section-enter">
        <a href="/" wire:navigate class="card-press w-10 h-10 rounded-xl bg-gray-800 flex items-center justify-center active:bg-gray-700 transition-colors">
            <svg class="w-5 h-5 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
            </svg>
        </a>
        <h1 class="text-xl font-bold">{{ $habit ? 'Edit Habit' : 'New Habit' }}</h1>
    </div>

    <form wire:submit="save" class="space-y-6">

        {{-- Emoji Picker --}}
        <div class="section-enter" style="animation-delay: 0.05s">
            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 block">Icon</label>
            <button type="button" wire:click="$toggle('showEmojiPicker')"
                    class="card-press w-16 h-16 rounded-2xl bg-gray-900 border border-gray-800/50 flex items-center justify-center text-3xl active:bg-gray-800 transition-all">
                {{ $emoji }}
            </button>

            @if($showEmojiPicker)
                <div class="mt-3 p-3 bg-gray-900 border border-gray-800/50 rounded-2xl fade-in">
                    <div class="grid grid-cols-8 gap-2">
                        @foreach($emojis as $e)
                            <button type="button" wire:click="selectEmoji('{{ $e }}')"
                                    class="w-10 h-10 rounded-xl flex items-center justify-center text-xl active:bg-gray-700 transition-all
                                           {{ $emoji === $e ? 'bg-violet-600/30 ring-2 ring-violet-500 scale-110' : 'hover:bg-gray-800' }}">
                                {{ $e }}
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Name --}}
        <div class="section-enter" style="animation-delay: 0.1s">
            <label for="name" class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 block">Name</label>
            <input type="text" id="name" wire:model="name" placeholder="e.g. Morning Run"
                   class="w-full bg-gray-900 border border-gray-800/50 rounded-xl px-4 py-3 text-white placeholder-gray-600 focus:outline-none focus:border-violet-500/50 focus:ring-1 focus:ring-violet-500/50 transition-all duration-200">
            @error('name')
                <p class="mt-1.5 text-xs text-red-400 fade-in">{{ $message }}</p>
            @enderror
        </div>

        {{-- Description --}}
        <div class="section-enter" style="animation-delay: 0.15s">
            <label for="description" class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 block">
                Description <span class="text-gray-600 normal-case">(optional)</span>
            </label>
            <textarea id="description" wire:model="description" rows="2" placeholder="e.g. Run for 30 minutes"
                      class="w-full bg-gray-900 border border-gray-800/50 rounded-xl px-4 py-3 text-white placeholder-gray-600 focus:outline-none focus:border-violet-500/50 focus:ring-1 focus:ring-violet-500/50 transition-all duration-200 resize-none"></textarea>
        </div>

        {{-- Reminder Time --}}
        <div class="section-enter" style="animation-delay: 0.2s">
            <label for="reminder_time" class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 block">Reminder Time</label>
            <input type="time" id="reminder_time" wire:model="reminder_time"
                   class="w-full bg-gray-900 border border-gray-800/50 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-violet-500/50 focus:ring-1 focus:ring-violet-500/50 transition-all duration-200">
        </div>

        {{-- Frequency --}}
        <div class="section-enter" style="animation-delay: 0.25s">
            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 block">Frequency</label>
            <div class="grid grid-cols-3 gap-2">
                @foreach(['daily' => 'Daily', 'weekdays' => 'Weekdays', 'weekends' => 'Weekends'] as $value => $label)
                    <button type="button" wire:click="$set('frequency', '{{ $value }}')"
                            class="py-3 rounded-xl text-sm font-semibold text-center transition-all duration-200
                                   {{ $frequency === $value
                                       ? 'bg-violet-600 text-white shadow-lg shadow-violet-600/20'
                                       : 'bg-gray-900 border border-gray-800/50 text-gray-400 active:bg-gray-800' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Actions --}}
        <div class="pt-2 space-y-3 section-enter" style="animation-delay: 0.3s">
            <button type="submit"
                    wire:loading.attr="disabled"
                    class="card-press w-full py-3.5 bg-violet-600 active:bg-violet-700 disabled:opacity-50 text-white text-sm font-semibold rounded-xl transition-all duration-200 shadow-lg shadow-violet-600/20 flex items-center justify-center gap-2">
                <span wire:loading.remove wire:target="save">
                    {{ $habit ? 'Save Changes' : 'Create Habit' }}
                </span>
                <span wire:loading wire:target="save">Saving...</span>
            </button>

            @if($habit)
                @if($showDeleteConfirm)
                    <div class="flex gap-2 fade-in">
                        <button type="button" wire:click="$set('showDeleteConfirm', false)"
                                class="card-press flex-1 py-3 bg-gray-800 text-gray-300 text-sm font-semibold rounded-xl active:bg-gray-700 transition-colors">
                            Cancel
                        </button>
                        <button type="button" wire:click="delete"
                                class="card-press flex-1 py-3 bg-red-600 active:bg-red-700 text-white text-sm font-semibold rounded-xl transition-colors">
                            Confirm Delete
                        </button>
                    </div>
                @else
                    <button type="button" wire:click="$set('showDeleteConfirm', true)"
                            class="w-full py-3 text-red-400 text-sm font-semibold rounded-xl active:bg-gray-900 transition-colors">
                        Delete Habit
                    </button>
                @endif
            @endif
        </div>
    </form>
</div>
