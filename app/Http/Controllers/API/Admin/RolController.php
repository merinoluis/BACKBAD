<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rol;

class RolController extends Controller
{
    public function index()
    {
        return response()->json(Rol::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombreRol' => 'required|string|unique:roles,nombreRol',
            'descripcion' => 'nullable|string|max:150',
        ]);

        $rol = Rol::create($request->only(['nombreRol', 'descripcion']));

        return response()->json([
            'message' => 'Rol creado correctamente',
            'data' => $rol
        ], 201);
    }
}
