<?php

namespace App\Http\Controllers;

use App\Models\Materia; // Importamos el modelo para interactuar con la BD
use Illuminate\Http\Request;

class MateriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // 1. Traemos todas las materias ordenadas por nivel de forma descendente
        $materias = Materia::orderBy('nivel', 'asc')->get();

        // 2. Retornamos una vista llamada 'materias.index' pasándole los datos
        return view('materias.index', compact('materias'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Retornamos la vista donde estará el formulario para ingresar la materia
        return view('materias.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validamos los datos del formulario para asegurarnos de que no vengan vacíos
        $request->validate([
            'codigo_materia' => 'required|string|unique:materias,codigo_materia',
            'nombre'         => 'required|string|max:255',
            'nivel'          => 'required|integer|min:1|max:10',
            'paralelo'       => 'required|string|max:10',
        ]);

        // 2. Como ya configuramos el $fillable, podemos usar la asignación masiva para guardar directo
        Materia::create($request->all());

        // 3. Redireccionamos a la lista de materias con un mensaje de éxito instantáneo
        return redirect()->route('materias.index')->with('success', '¡Materia creada con éxito para el SmartCampus!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Materia $materia)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Materia $materia)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Materia $materia)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Materia $materia)
    {
        //
    }
}
