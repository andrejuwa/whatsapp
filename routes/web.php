<?php

use Illuminate\Support\Facades\Route;

Route::livewire('/', 'pages::chat');
Route::livewire('/contato/{waId}', 'pages::chat');
Route::livewire('/arquivar/{whatsappId}', 'pages::arquivar');
