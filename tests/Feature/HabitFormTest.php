<?php

namespace Tests\Feature;

use App\Livewire\HabitForm;
use App\Models\Habit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class HabitFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_a_habit(): void
    {
        Livewire::test(HabitForm::class)
            ->set('name', 'Morning Run')
            ->set('emoji', '🏃')
            ->set('reminder_time', '06:00')
            ->set('frequency', 'daily')
            ->call('save')
            ->assertRedirect('/');

        $this->assertDatabaseHas('habits', [
            'name' => 'Morning Run',
            'emoji' => '🏃',
            'reminder_time' => '06:00',
            'frequency' => 'daily',
        ]);
    }

    public function test_can_create_habit_with_description(): void
    {
        Livewire::test(HabitForm::class)
            ->set('name', 'Read')
            ->set('description', '30 pages minimum')
            ->set('reminder_time', '21:00')
            ->call('save');

        $this->assertDatabaseHas('habits', [
            'name' => 'Read',
            'description' => '30 pages minimum',
        ]);
    }

    public function test_name_is_required(): void
    {
        Livewire::test(HabitForm::class)
            ->set('name', '')
            ->call('save')
            ->assertHasErrors(['name' => 'required']);
    }

    public function test_name_max_length_is_100(): void
    {
        Livewire::test(HabitForm::class)
            ->set('name', str_repeat('a', 101))
            ->call('save')
            ->assertHasErrors(['name' => 'max']);
    }

    public function test_frequency_must_be_valid(): void
    {
        Livewire::test(HabitForm::class)
            ->set('name', 'Test')
            ->set('frequency', 'invalid')
            ->call('save')
            ->assertHasErrors(['frequency' => 'in']);
    }

    public function test_can_edit_existing_habit(): void
    {
        $habit = Habit::factory()->create(['name' => 'Old Name']);

        Livewire::test(HabitForm::class, ['id' => $habit->id])
            ->assertSet('name', 'Old Name')
            ->set('name', 'New Name')
            ->call('save')
            ->assertRedirect('/');

        $this->assertDatabaseHas('habits', ['id' => $habit->id, 'name' => 'New Name']);
    }

    public function test_edit_form_populates_fields(): void
    {
        $habit = Habit::factory()->create([
            'name' => 'Yoga',
            'emoji' => '🧘',
            'reminder_time' => '08:30',
            'frequency' => 'weekdays',
            'description' => 'Morning yoga session',
        ]);

        Livewire::test(HabitForm::class, ['id' => $habit->id])
            ->assertSet('name', 'Yoga')
            ->assertSet('emoji', '🧘')
            ->assertSet('reminder_time', '08:30')
            ->assertSet('frequency', 'weekdays')
            ->assertSet('description', 'Morning yoga session');
    }

    public function test_can_delete_habit(): void
    {
        $habit = Habit::factory()->create();

        Livewire::test(HabitForm::class, ['id' => $habit->id])
            ->call('delete')
            ->assertRedirect('/');

        $this->assertDatabaseMissing('habits', ['id' => $habit->id]);
    }

    public function test_select_emoji_updates_emoji(): void
    {
        Livewire::test(HabitForm::class)
            ->call('selectEmoji', '🎯')
            ->assertSet('emoji', '🎯')
            ->assertSet('showEmojiPicker', false);
    }
}
