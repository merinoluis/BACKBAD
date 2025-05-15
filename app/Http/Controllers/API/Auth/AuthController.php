<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function signin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombreUsuario' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::where('nombreUsuario', $request->nombreUsuario)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Credenciales incorrectas',
            ], 401);
        }

        Auth::login($user);

        $user->tokens()->delete();

        $token = $user->createToken('authToken')->accessToken;

        return response()->json([
            'user' => [
                'id' => $user->id,
                'nombre' => $user->name,
                'apellido' => $user->lastname,
                'nombreUsuario' => $user->nombreUsuario,
                'rol' => $user->rol->nombreRol ?? null,
                'estado' => $user->estado->nombreEstado ?? null,
                'email' => $user->email,
            ],
            'access_token' => $token,
        ]);
    }

    public function signout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json([
            'message' => 'Sesión cerrada correctamente',
        ]);
    }

    public function me()
    {
        $user = Auth::user();

        return response()->json([
            'id' => $user->id,
            'nombre' => $user->name,
            'apellido' => $user->lastname,
            'nombreUsuario' => $user->nombreUsuario,
            'rol' => $user->rol->nombreRol ?? null,
            'estado' => $user->estado->nombreEstado ?? null,
            'email' => $user->email,
        ]);
    }

    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'           => 'required|string|max:100',
            'lastname'       => 'required|string|max:100',
            'nombreUsuario'  => 'required|string|max:50|unique:usuarios,nombreUsuario',
            'email'          => 'nullable|email|max:100|unique:usuarios,email',
            'password'       => 'required|string|min:6|confirmed',
            'nombreRol'      => 'required|exists:roles,nombreRol',
            'idEstado'       => 'required|exists:estado_usuario,idEstado',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::create([
            'name'           => $request->name,
            'lastname'       => $request->lastname,
            'nombreUsuario'  => $request->nombreUsuario,
            'email'          => $request->email,
            'password'       => Hash::make($request->password),
            'nombreRol'      => $request->nombreRol,
            'idEstado'       => $request->idEstado,
        ]);

        $token = $user->createToken('authToken')->accessToken;

        return response()->json([
            'message' => 'Usuario registrado con éxito',
            'user' => [
                'id' => $user->id,
                'nombre' => $user->name,
                'apellido' => $user->lastname,
                'nombreUsuario' => $user->nombreUsuario,
                'email' => $user->email,
                'rol' => $user->rol->nombreRol ?? null,
                'estado' => $user->estado->nombreEstado ?? null,
            ],
            'access_token' => $token,
        ]);
    }
}
