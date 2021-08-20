<?php

use core\Router\Route;

Route::Get("/{token}", app\Controllers\BasicController::class, 'index');

Route::Put("/addUrl", app\Controllers\BasicController::class, 'addUrl');

