<?php

use Illuminate\Support\Facades\Route;

Route::livewire('/', 'pages::home')->name('home');
Route::livewire('/subjects/{subject}', 'pages::subject')->name('subjects.show');
Route::livewire('/courses/{course}', 'pages::course')->name('courses.show');
Route::livewire('/lessons/{lesson}', 'pages::lesson')->name('lessons.show');
