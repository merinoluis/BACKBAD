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

        if (!$user) {
            return response()->json([
                'message' => 'Credenciales incorrectas',
            ], 401);
        }

        if ($user->bloqueado) {
            return response()->json([
                'message' => 'Usuario bloqueado por exceder intentos fallidos.',
            ], 403);
        }

        if (!Hash::check($request->password, $user->password)) {
            $user->intentos_fallidos += 1;

            if ($user->intentos_fallidos >= 3) {
                $user->bloqueado = true;
                $user->save();

                return response()->json([
                    'message' => 'Usuario bloqueado por exceder intentos fallidos.',
                ], 403);
            }

            $user->save();

            return response()->json([
                'message' => 'Credenciales incorrectas',
                'intentos_restantes' => 3 - $user->intentos_fallidos,
            ], 401);
        }


        $user->intentos_fallidos = 0;
        $user->bloqueado = false;
        $user->save();

        $user->tokens()->delete();
        $token = $user->createToken('authToken')->accessToken;

        return response()->json([
            'message' => 'Inicio de sesión exitoso',
            'user' => [
                'id' => $user->id,
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
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|string|max:100',
                'lastname' => 'required|string|max:100',
                'nombreUsuario' => 'required|string|max:50|unique:usuarios,nombreUsuario',
                'email' => 'nullable|email|max:100|unique:usuarios,email',
                'password' => 'required|string|min:6',
                'nombreRol' => 'required|exists:roles,nombreRol',
            ],
            [
                'name.required' => 'El nombre es obligatorio.',
                'lastname.required' => 'El apellido es obligatorio.',
                'nombreUsuario.required' => 'El nombre de usuario es obligatorio.',
                'nombreUsuario.unique' => 'El nombre de usuario ya está en uso.',
                'email.email' => 'Debe ingresar un correo válido.',
                'email.unique' => 'Este correo ya está registrado.',
                'password.required' => 'La contraseña es obligatoria.',
                'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
                'nombreRol.required' => 'El rol es obligatorio.',
                'nombreRol.exists' => 'El rol seleccionado no es válido.',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'lastname' => $request->lastname,
            'nombreUsuario' => $request->nombreUsuario,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'nombreRol' => $request->nombreRol,
            'idEstado' => 1,
        ]);

        $token = $user->createToken('authToken')->accessToken;

        return response()->json([
            'message' => 'Usuario registrado correctamente',
            'user' => $user,
            'access_token' => $token,
        ]);
    }

    public function solicitarRestablecimiento(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombreUsuario' => 'required|exists:usuarios,nombreUsuario',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Usuario no encontrado.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::where('nombreUsuario', $request->nombreUsuario)->first();

        if (!$user->bloqueado) {
            return response()->json([
                'message' => 'El usuario no está bloqueado.',
            ], 400);
        }

        if ($user->solicitud_restablecimiento) {
            return response()->json([
                'message' => 'Ya se ha solicitado el restablecimiento. Por favor espera respuesta.',
            ], 400);
        }

        $user->solicitud_restablecimiento = 1;
        $user->save();

        // puede ser correo al admin

        return response()->json([
            'message' => 'Solicitud enviada. Un administrador revisará tu cuenta.',
        ]);
    }

    public function desbloquearUsuario(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombreUsuario' => 'required|exists:usuarios,nombreUsuario',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Usuario no encontrado.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::where('nombreUsuario', $request->nombreUsuario)->first();

        if (!$user->bloqueado) {
            return response()->json([
                'message' => 'El usuario ya está desbloqueado.',
            ], 400);
        }

        $user->bloqueado = 0;
        $user->intentos_fallidos = 0;
        $user->solicitud_restablecimiento = 0;
        $user->save();

        return response()->json([
            'message' => 'Usuario desbloqueado correctamente.',
        ]);
    }
}
