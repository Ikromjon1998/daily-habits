<?php

namespace Tests\Feature;

use App\Livewire\HabitForm;
use App\Livewire\Settings;
use App\Livewire\Today;
use App\Models\Habit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageTest extends TestCase
{
    use RefreshDatabase;

    public function test_today_page_loads_successfully(): void
    {
        $this->get('/')->assertStatus(200);
    }

    public function test_settings_page_loads_successfully(): void
    {
        $this->get('/settings')->assertStatus(200);
    }

    public function test_create_habit_page_loads_successfully(): void
    {
        $this->get('/habits/create')->assertStatus(200);
    }

    public function test_edit_habit_page_loads_successfully(): void
    {
        $habit = Habit::factory()->create();

        $this->get("/habits/{$habit->id}/edit")->assertStatus(200);
    }

    public function test_today_page_renders_today_component(): void
    {
        $this->get('/')->assertSeeLivewire(Today::class);
    }

    public function test_settings_page_renders_settings_component(): void
    {
        $this->get('/settings')->assertSeeLivewire(Settings::class);
    }

    public function test_create_page_renders_habit_form_component(): void
    {
        $this->get('/habits/create')->assertSeeLivewire(HabitForm::class);
    }
}
