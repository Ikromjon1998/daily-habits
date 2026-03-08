<?php

use App\Livewire\Today;
use App\Livewire\Settings;
use Illuminate\Support\Facades\Route;

Route::get('/', Today::class);
Route::get('/settings', Settings::class);
