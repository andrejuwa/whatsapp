<?php

use Illuminate\Support\Facades\Route;

Route::livewire('/', 'pages::chat');
Route::livewire('/contato/{waId}', 'pages::chat');
