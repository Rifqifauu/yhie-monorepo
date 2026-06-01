<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // Redirect ke URL frontend (Nuxt)
    return redirect(env('FRONTEND_URL', 'http://localhost:3000'));
});
