<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Peliculas;

class PeliculasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Peliculas::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $pelicula = Peliculas::create($request->all());

        return response()->json($pelicula, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Peliculas $pelicula)
    {
        return $pelicula;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Peliculas $pelicula)
   {
        $pelicula->update($request->all());

        return response()->json($pelicula);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Peliculas $pelicula)
    {
        $pelicula->delete();

        return response()->json(null, 204);
    }
}
