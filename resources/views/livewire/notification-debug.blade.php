<div class="px-5 py-6 page-enter">
    {{-- Header --}}
    <div class="mb-4 section-enter flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">Notification Debug</h1>
            <p class="text-xs text-gray-500 mt-1">Plugin v1.3.2 — Event Tap Fix</p>
        </div>
        <a href="/settings" wire:navigate class="text-xs text-violet-400 font-medium px-3 py-1.5 rounded-lg bg-violet-500/10">
            Back
        </a>
    </div>

    {{-- Permission Status --}}
    <div class="mb-4 section-enter" style="animation-delay: 0.05s">
        <div class="bg-gray-900 border border-gray-800/50 rounded-2xl px-5 py-3 flex items-center justify-between">
            <span class="text-sm text-gray-300">Permission</span>
            @if($permissionStatus === 'granted')
                <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-400">Granted</span>
            @else
                <button wire:click="checkPermission" class="text-xs font-medium px-2.5 py-1 rounded-full bg-red-500/20 text-red-400">
                    {{ $permissionStatus }} — Tap to check
                </button>
            @endif
        </div>
    </div>

    {{-- Test Scenarios --}}
    <div class="mb-4 section-enter" style="animation-delay: 0.1s">
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-2 mb-2">Test Scenarios</h3>
        <div class="bg-gray-900 border border-gray-800/50 rounded-2xl overflow-hidden divide-y divide-gray-800/30">

            {{-- Scenario 1: Warm start --}}
            <button wire:click="scheduleWarmTest" class="card-press w-full px-5 py-4 active:bg-gray-800/50 transition-colors text-left">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-green-600/20 flex items-center justify-center text-lg">1</div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-green-400">Warm Start (10s)</p>
                        <p class="text-xs text-gray-500 mt-0.5">Keep app open. Tap notification when it arrives.</p>
                    </div>
                </div>
                <p class="text-xs text-gray-600 mt-2 ml-13">
                    Expected: NotificationReceived + NotificationTapped appear in log below
                </p>
            </button>

            {{-- Scenario 2: Cold start --}}
            <button wire:click="scheduleColdTest" class="card-press w-full px-5 py-4 active:bg-gray-800/50 transition-colors text-left">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-orange-600/20 flex items-center justify-center text-lg">2</div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-orange-400">Cold Start (30s)</p>
                        <p class="text-xs text-gray-500 mt-0.5">Press, then KILL the app. Tap notification to relaunch.</p>
                    </div>
                </div>
                <p class="text-xs text-gray-600 mt-2 ml-13">
                    Expected: App opens here, NotificationTapped appears in log (this was the bug)
                </p>
            </button>

            {{-- Scenario 3: Action buttons cold start --}}
            <button wire:click="scheduleActionTest" class="card-press w-full px-5 py-4 active:bg-gray-800/50 transition-colors text-left">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-red-600/20 flex items-center justify-center text-lg">3</div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-red-400">Action Buttons + Cold (30s)</p>
                        <p class="text-xs text-gray-500 mt-0.5">Kill app, then press Confirm or Dismiss on the notification.</p>
                    </div>
                </div>
                <p class="text-xs text-gray-600 mt-2 ml-13">
                    Expected: NotificationActionPressed with actionId in log
                </p>
            </button>
        </div>
    </div>

    {{-- Event Log --}}
    <div class="section-enter" style="animation-delay: 0.15s">
        <div class="flex items-center justify-between px-2 mb-2">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Event Log</h3>
            @if(count($eventLog) > 0)
                <button wire:click="clearLog" class="text-xs text-gray-600 hover:text-gray-400">Clear</button>
            @endif
        </div>

        <div class="bg-gray-900 border border-gray-800/50 rounded-2xl overflow-hidden">
            @forelse($eventLog as $entry)
                <div class="px-4 py-3 {{ !$loop->last ? 'border-b border-gray-800/30' : '' }}">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-[10px] font-mono text-gray-600">{{ $entry['time'] }}</span>
                        <span class="text-xs font-semibold
                            @if(str_contains($entry['event'], 'Tapped')) text-orange-400
                            @elseif(str_contains($entry['event'], 'ActionPressed')) text-red-400
                            @elseif(str_contains($entry['event'], 'Received')) text-blue-400
                            @elseif(str_contains($entry['event'], 'Scheduled')) text-green-400
                            @elseif(str_contains($entry['event'], 'Permission')) text-violet-400
                            @else text-gray-400
                            @endif
                        ">
                            {{ $entry['event'] }}
                        </span>
                    </div>
                    @if($entry['data'])
                        <p class="text-[11px] font-mono text-gray-600 break-all leading-relaxed">{{ $entry['data'] }}</p>
                    @endif
                </div>
            @empty
                <div class="px-5 py-8 text-center">
                    <p class="text-sm text-gray-600">No events yet</p>
                    <p class="text-xs text-gray-700 mt-1">Schedule a notification above, then tap it</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
