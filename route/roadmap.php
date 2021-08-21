<?php

use core\Router\Route;

// Author methods

Route::Get("/author/list", app\Controllers\AuthorController::class, 'index');

Route::Get("/author/list/{page}", app\Controllers\AuthorController::class, 'index');

Route::Get("/author/list/{page}/{perPage}", app\Controllers\AuthorController::class, 'index');

Route::Post("/author/add", app\Controllers\AuthorController::class, 'add');

Route::Post("/author/update", app\Controllers\AuthorController::class, 'update');

Route::Post("/author/delete", app\Controllers\AuthorController::class, 'delete');

// Magazine methods

Route::Get("/magazine/list/{page}/{perPage}/{author_id}", app\Controllers\MagazineController::class, 'index');

Route::Post("/magazine/add", app\Controllers\MagazineController::class, 'add');

Route::Post("/magazine/update", app\Controllers\MagazineController::class, 'update');

Route::Post("/magazine/delete", app\Controllers\MagazineController::class, 'delete');

