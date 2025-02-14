<?php

use Illumninate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PeliculasController;
use App\Http\Controllers\Api\GenerosController;

Route::apiResource('peliculas', PeliculasController::class);
Route::apiResource('generos', GenerosController::class);
Route::get('/user', function (Request $request) {
	return $request->user();
});

