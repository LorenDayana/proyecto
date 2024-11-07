<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class RolesController extends Controller
{
    public function show(Request $request)
    {
    $users = User::all();
    return view('roles.index', ['users' => $users]);
    }

    // Método para actualizar el rol de un usuario
public function update(Request $request, $id)
{
    // Encuentra el usuario por su ID
    $user = User::findOrFail($id);

    // Actualiza el rol del usuario con el valor enviado en el formulario
    $user->role = $request->input('role');
    $user->save(); // Guarda los cambios en la base de datos

    // Redirecciona de vuelta a la lista de usuarios con un mensaje de éxito
    return redirect()->route('roles.index')->with('success', 'Rol actualizado correctamente.');
}

    
}
