<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Generos;

class GenerosController extends Controller
{
    /**
     * Mostrar todos los géneros.
     */
    public function index()
    {
        return Generos::all();
    }

    /**
     * Crear un nuevo género.
     */
    public function store(Request $request)
    {
	  $genero = Generos::create($request->all());
	return response()->json($genero, 201);
    }

    /**
     * Mostrar un género específico.
     */
    public function show(Generos $genero)
    {
        return $genero;
    }

    /**
     * Actualizar un género.
     */
    public function update(Request $request, Generos $genero)
    {
        $genero->update($request->all());

        return response()->json($genero);
    }

    /**
     * Eliminar un género.
     */
    public function destroy(Generos $genero)
    {
        $genero->delete();

    	return response()->json(null, 204);
    }
}
