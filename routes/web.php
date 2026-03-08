<?php

use App\Livewire\Settings;
use App\Livewire\Today;
use Illuminate\Support\Facades\Route;

Route::get('/', Today::class);
Route::get('/settings', Settings::class);
