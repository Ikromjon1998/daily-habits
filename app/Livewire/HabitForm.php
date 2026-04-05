<?php

namespace App\Livewire;

use App\Models\Habit;
use App\Services\HabitNotificationService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;

class HabitForm extends Component
{
    public ?Habit $habit = null;

    #[Validate('required|string|max:100')]
    public string $name = '';

    #[Validate('nullable|string|max:255')]
    public ?string $description = null;

    public string $emoji = '✅';

    #[Validate('required|string')]
    public string $reminder_time = '09:00';

    #[Validate('required|in:daily,weekdays,weekends')]
    public string $frequency = 'daily';

    #[Validate('nullable|string')]
    public ?string $sound_name = null;

    public bool $showEmojiPicker = false;

    public bool $showDeleteConfirm = false;

    /** @var array<string, string> */
    public array $availableSounds = [];

    /** @var array<int, string> */
    public array $emojis = [
        '🏃', '📚', '💧', '🧘', '💻', '✍️', '🎯', '💪',
        '🧠', '🎨', '🎵', '📝', '🌱', '☀️', '🛌', '🍎',
        '🥗', '🚶', '🧹', '💊', '🦷', '📱', '🐶', '✅',
    ];

    public function mount(?int $id = null): void
    {
        $this->availableSounds = $this->discoverSounds();

        if ($id) {
            $this->habit = Habit::findOrFail($id);
            $this->name = $this->habit->name;
            $this->description = $this->habit->description;
            $this->emoji = $this->habit->emoji;
            $this->reminder_time = $this->habit->reminder_time;
            $this->frequency = $this->habit->frequency;
            $this->sound_name = $this->habit->sound_name;
        }
    }

    public function selectEmoji(string $emoji): void
    {
        $this->emoji = $emoji;
        $this->showEmojiPicker = false;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'emoji' => $this->emoji,
            'reminder_time' => $this->reminder_time,
            'frequency' => $this->frequency,
            'sound_name' => $this->sound_name,
        ];

        $notificationService = app(HabitNotificationService::class);

        if ($this->habit) {
            $this->habit->update($data);
            $notificationService->schedule($this->habit);
        } else {
            $habit = Habit::create($data);
            $notificationService->schedule($habit);
        }

        $this->redirect('/', navigate: true);
    }

    public function delete(): void
    {
        if ($this->habit) {
            app(HabitNotificationService::class)->cancel($this->habit);
            $this->habit->delete();
        }

        $this->redirect('/', navigate: true);
    }

    /**
     * Scan resources/sounds/ for available sound files.
     *
     * @return array<string, string>
     */
    private function discoverSounds(): array
    {
        $dir = resource_path('sounds');

        if (! is_dir($dir)) {
            return [];
        }

        $sounds = [];
        $extensions = ['wav', 'mp3', 'ogg', 'caf', 'aiff', 'aif'];

        foreach (scandir($dir) as $file) {
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (in_array($ext, $extensions, true)) {
                $label = ucfirst(pathinfo($file, PATHINFO_FILENAME));
                $sounds[$file] = $label;
            }
        }

        ksort($sounds);

        return $sounds;
    }

    public function render(): View
    {
        return view('livewire.habit-form')
            ->layout('layouts.app');
    }
}
