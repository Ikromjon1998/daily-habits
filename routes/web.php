<?php

use App\Livewire\HabitForm;
use App\Livewire\Settings;
use App\Livewire\Today;
use Illuminate\Support\Facades\Route;

Route::get('/', Today::class);
Route::get('/settings', Settings::class);
Route::get('/habits/create', HabitForm::class);
Route::get('/habits/{id}/edit', HabitForm::class);
