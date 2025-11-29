<?php

use Illuminate\Support\Facades\Route;

Route::get('/test-campaigns-create', function () {
    return view('test', ['message' => 'Campaign create route is working!']);
});