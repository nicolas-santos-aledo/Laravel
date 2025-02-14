<?php

use Illuminate\Support\Facades\Route;

route::view('/peliculas', 'peliculas');

Route::get('/', function () {
    return view('welcome');
});
