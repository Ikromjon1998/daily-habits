<div class="px-5 py-6 page-enter">
    {{-- Header --}}
    <div class="mb-4 section-enter flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">Notification Debug</h1>
            <p class="text-xs text-gray-500 mt-1">Plugin v1.9.0 — Custom Notification Sounds</p>
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

        {{-- How to test guide --}}
        <div class="bg-gray-900/50 border border-gray-800/30 rounded-2xl px-5 py-4 mb-3">
            <p class="text-xs font-semibold text-gray-400 mb-2">How to test</p>
            <p class="text-[11px] text-gray-500 leading-relaxed">
                Start with #6 (instant), then #5 (15s), then #1 (10s warm).
                Then test action buttons: #3a (warm, 10s), #3c (input, 10s), then #3b (cold, 30s).
                Test #7 to verify Laravel Notification channel integration.
                Test #8a and #8b for custom notification sounds.
                Save #2 for last (cold start tap). After each test, check the Event Log below.
            </p>
        </div>

        <div class="bg-gray-900 border border-gray-800/50 rounded-2xl overflow-hidden divide-y divide-gray-800/30">

            {{-- Scenario 1: Warm start --}}
            <button wire:click="scheduleWarmTest" class="card-press w-full px-5 py-4 active:bg-gray-800/50 transition-colors text-left">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-green-600/20 flex items-center justify-center text-lg">1</div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-green-400">Warm Start (10s)</p>
                        <p class="text-xs text-gray-500 mt-0.5">Tests: schedule, receive, and tap while app is open.</p>
                    </div>
                </div>
                <div class="mt-2 ml-13 space-y-1">
                    <p class="text-[11px] text-gray-500">1. Tap this button</p>
                    <p class="text-[11px] text-gray-500">2. Keep the app open and wait 10 seconds</p>
                    <p class="text-[11px] text-gray-500">3. When the notification appears, tap it</p>
                    <p class="text-[11px] text-green-700">Pass: NotificationScheduled, NotificationReceived, and NotificationTapped all appear in log</p>
                </div>
            </button>

            {{-- Scenario 2: Cold start --}}
            <button wire:click="scheduleColdTest" class="card-press w-full px-5 py-4 active:bg-gray-800/50 transition-colors text-left">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-orange-600/20 flex items-center justify-center text-lg">2</div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-orange-400">Cold Start (30s)</p>
                        <p class="text-xs text-gray-500 mt-0.5">Tests: tap detection when app was killed (cold start).</p>
                    </div>
                </div>
                <div class="mt-2 ml-13 space-y-1">
                    <p class="text-[11px] text-gray-500">1. Tap this button</p>
                    <p class="text-[11px] text-gray-500">2. Kill the app (swipe away from recents)</p>
                    <p class="text-[11px] text-gray-500">3. Wait 30 seconds for the notification</p>
                    <p class="text-[11px] text-gray-500">4. Tap the notification to relaunch the app</p>
                    <p class="text-[11px] text-green-700">Pass: App reopens to this page, NotificationTapped appears in log</p>
                </div>
            </button>

            {{-- Scenario 3a: Action buttons warm start --}}
            <button wire:click="scheduleActionWarmTest" class="card-press w-full px-5 py-4 active:bg-gray-800/50 transition-colors text-left">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-red-600/20 flex items-center justify-center text-lg">3a</div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-red-400">Action Buttons — Warm (10s)</p>
                        <p class="text-xs text-gray-500 mt-0.5">Tests: 3 action buttons (regular + destructive + snooze 5min) while app is open.</p>
                    </div>
                </div>
                <div class="mt-2 ml-13 space-y-1">
                    <p class="text-[11px] text-gray-500">1. Tap this button, keep app open</p>
                    <p class="text-[11px] text-gray-500">2. Wait 10s for the notification banner</p>
                    <p class="text-[11px] text-gray-500">3. <span class="text-white">iOS:</span> Long-press the banner to reveal buttons</p>
                    <p class="text-[11px] text-gray-500">3. <span class="text-white">Android/Samsung:</span> Swipe banner up, pull down shade, then expand the notification (two-finger swipe down or tap the arrow)</p>
                    <p class="text-[11px] text-gray-500">4. You should see 3 buttons: <span class="text-white">Done</span>, <span class="text-red-400">Skip</span> (red on iOS), <span class="text-white">Snooze (5m)</span></p>
                    <p class="text-[11px] text-gray-500">5. Tap any button — <span class="text-amber-400">Snooze reschedules natively in 5 minutes</span></p>
                    <p class="text-[11px] text-green-700">Pass: ActionPressed shows actionId + snoozed:true/snoozeSeconds:300 for snooze button</p>
                </div>
            </button>

            {{-- Scenario 3b: Action buttons cold start --}}
            <button wire:click="scheduleActionTest" class="card-press w-full px-5 py-4 active:bg-gray-800/50 transition-colors text-left">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-red-600/20 flex items-center justify-center text-lg">3b</div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-red-400">Action Buttons — Cold (30s)</p>
                        <p class="text-xs text-gray-500 mt-0.5">Tests: same 3 buttons after app is killed. Snooze works natively even when app is dead.</p>
                    </div>
                </div>
                <div class="mt-2 ml-13 space-y-1">
                    <p class="text-[11px] text-gray-500">1. Tap this button</p>
                    <p class="text-[11px] text-gray-500">2. Kill the app (swipe away)</p>
                    <p class="text-[11px] text-gray-500">3. Wait 30s, then open notification shade and expand the notification</p>
                    <p class="text-[11px] text-gray-500">4. Pick any action: <span class="text-white">Done</span>, <span class="text-red-400">Skip</span>, or <span class="text-white">Snooze (5m)</span></p>
                    <p class="text-[11px] text-amber-500">Snooze: notification reappears in 5 min without opening the app</p>
                    <p class="text-[11px] text-gray-500">5. Open app manually — ActionPressed event should appear in debug bar/log</p>
                    <p class="text-[11px] text-green-700">Pass: ActionPressed appears in log with actionId + snoozed flag for snooze button</p>
                </div>
            </button>

            {{-- Scenario 3c: Text input action --}}
            <button wire:click="scheduleActionInputTest" class="card-press w-full px-5 py-4 active:bg-gray-800/50 transition-colors text-left">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-pink-600/20 flex items-center justify-center text-lg">3c</div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-pink-400">Text Input Action (10s)</p>
                        <p class="text-xs text-gray-500 mt-0.5">Tests: reply-style action button with text input field.</p>
                    </div>
                </div>
                <div class="mt-2 ml-13 space-y-1">
                    <p class="text-[11px] text-gray-500">1. Tap this button, keep app open</p>
                    <p class="text-[11px] text-gray-500">2. Wait 10s, open notification shade and expand the notification</p>
                    <p class="text-[11px] text-gray-500">3. Tap "Reply" — a text field should appear</p>
                    <p class="text-[11px] text-gray-500">4. Type something and send</p>
                    <p class="text-[11px] text-green-700">Pass: ActionPressed shows actionId "reply" and your typed text in "Input:" field</p>
                    <p class="text-[11px] text-gray-500">5. Or tap <span class="text-red-400">Dismiss</span> (red) to test destructive style</p>
                </div>
            </button>

            {{-- Scenario 4: Update content --}}
            <button wire:click="scheduleUpdateContentTest" class="card-press w-full px-5 py-4 active:bg-gray-800/50 transition-colors text-left">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-cyan-600/20 flex items-center justify-center text-lg">4</div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-cyan-400">Update Content (60s)</p>
                        <p class="text-xs text-gray-500 mt-0.5">Tests: updating title/body while preserving original timing.</p>
                    </div>
                </div>
                <div class="mt-2 ml-13 space-y-1">
                    <p class="text-[11px] text-gray-500">1. Tap this button (schedules with "ORIGINAL Title", then updates to "UPDATED Title")</p>
                    <p class="text-[11px] text-gray-500">2. Check log: you should see Scheduled + Updated entries immediately</p>
                    <p class="text-[11px] text-gray-500">3. Wait 60 seconds for the notification to arrive</p>
                    <p class="text-[11px] text-gray-500">4. Check the notification content</p>
                    <p class="text-[11px] text-green-700">Pass: Notification shows "UPDATED Title" (not "ORIGINAL"). Fires at 60s (timing preserved). NotificationUpdated event in log.</p>
                </div>
            </button>

            {{-- Scenario 5: Update timing --}}
            <button wire:click="scheduleUpdateTimingTest" class="card-press w-full px-5 py-4 active:bg-gray-800/50 transition-colors text-left">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-purple-600/20 flex items-center justify-center text-lg">5</div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-purple-400">Update Timing (120s -> 15s)</p>
                        <p class="text-xs text-gray-500 mt-0.5">Tests: rescheduling a notification to fire sooner.</p>
                    </div>
                </div>
                <div class="mt-2 ml-13 space-y-1">
                    <p class="text-[11px] text-gray-500">1. Tap this button (schedules at 120s, then updates delay to 15s)</p>
                    <p class="text-[11px] text-gray-500">2. Check log: you should see Scheduled + Updated entries</p>
                    <p class="text-[11px] text-gray-500">3. Wait ~15 seconds</p>
                    <p class="text-[11px] text-green-700">Pass: Notification fires in ~15s (not 120s). NotificationUpdated event in log.</p>
                    <p class="text-[11px] text-red-700">Fail: If notification takes 120s, the timing update didn't work.</p>
                </div>
            </button>

            {{-- Scenario 6: getPending + cancel --}}
            <button wire:click="testGetPending" class="card-press w-full px-5 py-4 active:bg-gray-800/50 transition-colors text-left">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-yellow-600/20 flex items-center justify-center text-lg">6</div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-yellow-400">Schedule + Cancel + GetPending</p>
                        <p class="text-xs text-gray-500 mt-0.5">Tests: schedule, getPending, cancel, and getPending again (instant result).</p>
                    </div>
                </div>
                <div class="mt-2 ml-13 space-y-1">
                    <p class="text-[11px] text-gray-500">1. Tap this button (all steps run automatically)</p>
                    <p class="text-[11px] text-gray-500">2. Check the log immediately for 4 entries</p>
                    <p class="text-[11px] text-green-700">Pass: First GetPending shows count: 2 with both notifications. Second GetPending shows count: 0 after cancel.</p>
                    <p class="text-[11px] text-red-700">Fail: If GetPending count doesn't match or cancel doesn't remove entries.</p>
                </div>
            </button>

            {{-- Scenario 7: Laravel Notification Channel --}}
            <button wire:click="scheduleChannelTest" class="card-press w-full px-5 py-4 active:bg-gray-800/50 transition-colors text-left">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600/20 flex items-center justify-center text-lg">7</div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-indigo-400">Laravel Notification Channel (10s)</p>
                        <p class="text-xs text-gray-500 mt-0.5">Tests: scheduling via Laravel's Notification system (via() + toLocalNotification()).</p>
                    </div>
                </div>
                <div class="mt-2 ml-13 space-y-1">
                    <p class="text-[11px] text-gray-500">1. Tap this button, keep app open</p>
                    <p class="text-[11px] text-gray-500">2. Wait 10s for the notification</p>
                    <p class="text-[11px] text-gray-500">3. Expand notification to see 2 buttons: <span class="text-white">OK</span> and <span class="text-red-400">Cancel</span></p>
                    <p class="text-[11px] text-gray-500">4. Tap the notification or an action button</p>
                    <p class="text-[11px] text-green-700">Pass: NotificationScheduled + NotificationReceived appear (same as direct scheduling). Action buttons work.</p>
                    <p class="text-[11px] text-indigo-400">This proves the full Laravel Notification channel integration works end-to-end.</p>
                </div>
            </button>

            {{-- Scenario 8a: Custom sound (direct API) --}}
            <button wire:click="scheduleCustomSoundTest" class="card-press w-full px-5 py-4 active:bg-gray-800/50 transition-colors text-left">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-600/20 flex items-center justify-center text-lg">8a</div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-amber-400">Custom Sound — Direct API (10s)</p>
                        <p class="text-xs text-gray-500 mt-0.5">Tests: custom sound via Facade with soundName parameter.</p>
                    </div>
                </div>
                <div class="mt-2 ml-13 space-y-1">
                    <p class="text-[11px] text-gray-500">1. Tap this button, keep app open</p>
                    <p class="text-[11px] text-gray-500">2. Wait 10s for the notification</p>
                    <p class="text-[11px] text-gray-500">3. Listen for the sound when it arrives</p>
                    <p class="text-[11px] text-green-700">Pass: Notification plays a custom alert sound (different from system default). NotificationReceived appears in log.</p>
                    <p class="text-[11px] text-red-700">Fail: Notification plays the default system sound or is silent.</p>
                    <p class="text-[11px] text-amber-400">Compare with scenario #1 (system default) to hear the difference.</p>
                </div>
            </button>

            {{-- Scenario 8b: Custom sound (Laravel Channel) --}}
            <button wire:click="scheduleCustomSoundChannelTest" class="card-press w-full px-5 py-4 active:bg-gray-800/50 transition-colors text-left">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-600/20 flex items-center justify-center text-lg">8b</div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-amber-400">Custom Sound — Laravel Channel (10s)</p>
                        <p class="text-xs text-gray-500 mt-0.5">Tests: custom sound via Laravel Notification channel using ->sound('alert.wav').</p>
                    </div>
                </div>
                <div class="mt-2 ml-13 space-y-1">
                    <p class="text-[11px] text-gray-500">1. Tap this button, keep app open</p>
                    <p class="text-[11px] text-gray-500">2. Wait 10s for the notification</p>
                    <p class="text-[11px] text-gray-500">3. Listen for the same custom sound as #8a</p>
                    <p class="text-[11px] text-gray-500">4. Expand to see action buttons (OK, Cancel, Snooze)</p>
                    <p class="text-[11px] text-green-700">Pass: Same custom sound as #8a. Proves ->sound('alert.wav') fluent builder works via channel.</p>
                    <p class="text-[11px] text-red-700">Fail: Default system sound or silent — soundName not passed through channel.</p>
                </div>
            </button>
        </div>
    </div>

    {{-- Event Log --}}
    <div class="section-enter" style="animation-delay: 0.15s">
        <div class="flex items-center justify-between px-2 mb-2">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Event Log</h3>
            @if(count($eventLog) > 0)
                <div class="flex items-center gap-3">
                    <button
                        x-data="{ copied: false }"
                        x-on:click="
                            const logs = @js($eventLog).map(e => `[${e.time}] ${e.event}: ${e.data}`).join('\n');
                            navigator.clipboard.writeText(logs).then(() => { copied = true; setTimeout(() => copied = false, 2000) });
                        "
                        class="text-xs text-cyan-500 hover:text-cyan-400"
                    >
                        <span x-show="!copied">Copy Log</span>
                        <span x-show="copied" x-cloak>Copied!</span>
                    </button>
                    <button wire:click="clearLog" class="text-xs text-gray-600 hover:text-gray-400">Clear</button>
                </div>
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
                            @elseif(str_contains($entry['event'], 'Updated')) text-cyan-400
                            @elseif(str_contains($entry['event'], 'Scheduled')) text-green-400
                            @elseif(str_contains($entry['event'], 'Permission')) text-violet-400
                            @elseif(str_contains($entry['event'], 'Cancelled') || str_contains($entry['event'], 'GetPending')) text-yellow-400
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
