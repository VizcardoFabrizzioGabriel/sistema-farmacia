<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Cliente;
use App\Models\Empleado;
use App\Models\Rol;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = User::with(['rol', 'cliente', 'empleado'])->paginate(15);
        $roles = Rol::all();
        return view('dashboards.admin.usuarios', compact('usuarios', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_rol' => 'required|exists:roles,id_rol',
            'dni' => 'required|string|max:15|unique:usuarios,dni',
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'email' => 'required|email|unique:usuarios,email',
            'password' => 'required|string|min:6',
            'fecha_ingreso' => 'nullable|date',
            'turno' => 'nullable|string|max:20',
        ]);

        $usuario = User::create([
            'id_rol' => $request->id_rol,
            'dni' => $request->dni,
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'email' => $request->email,
            'password_hash' => Hash::make($request->password),
            'estado' => true,
        ]);

        $rol = Rol::find($request->id_rol);

        if ($rol->nombre === 'Cliente') {
            Cliente::create([
                'id_usuario' => $usuario->id_usuario,
                'puntos_fidelidad' => 0,
            ]);
        } else {
            Empleado::create([
                'id_usuario' => $usuario->id_usuario,
                'fecha_ingreso' => $request->fecha_ingreso ?? now(),
                'turno' => $request->turno ?? 'Mañana',
                'numero_colegiatura' => $request->numero_colegiatura ?? null,
                'caja_asignada' => $request->caja_asignada ?? null,
                'cargo' => $request->cargo ?? null,
            ]);
        }

        return redirect()->back()->with('success', 'Usuario creado correctamente.');
    }

    public function update(Request $request, int $id)
    {
        $usuario = User::findOrFail($id);

        $request->validate([
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'email' => 'required|email|unique:usuarios,email,' . $id . ',id_usuario',
            'estado' => 'boolean',
        ]);

        $usuario->update($request->only(['nombres', 'apellidos', 'email', 'estado']));

        if ($request->password) {
            $usuario->password_hash = Hash::make($request->password);
            $usuario->save();
        }

        return redirect()->back()->with('success', 'Usuario actualizado.');
    }

    public function destroy(int $id)
    {
        $usuario = User::findOrFail($id);
        $usuario->estado = false;
        $usuario->save();

        return redirect()->back()->with('success', 'Usuario suspendido.');
    }
}