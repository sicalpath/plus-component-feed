<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/', 'HomeContaoller@show')->name('feed:admin');
    Route::get('/statistics', 'HomeContaoller@statistics');
});
