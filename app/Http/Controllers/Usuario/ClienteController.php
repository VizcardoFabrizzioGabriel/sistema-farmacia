<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\User;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = Cliente::with('usuario')->paginate(15);
        return view('dashboards.admin.clientes', compact('clientes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'dni' => 'required|string|max:15|unique:usuarios,dni',
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'email' => 'required|email|unique:usuarios,email',
            'password' => 'required|string|min:6',
        ]);

        $usuario = User::create([
            'id_rol' => 5, // Cliente
            'dni' => $request->dni,
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'email' => $request->email,
            'password_hash' => bcrypt($request->password),
            'estado' => true,
        ]);

        Cliente::create([
            'id_usuario' => $usuario->id_usuario,
            'puntos_fidelidad' => 0,
        ]);

        return redirect()->back()->with('success', 'Cliente registrado correctamente.');
    }

    public function update(Request $request, int $id)
    {
        $cliente = Cliente::findOrFail($id);
        $usuario = $cliente->usuario;

        $request->validate([
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'email' => 'required|email|unique:usuarios,email,' . $usuario->id_usuario . ',id_usuario',
            'puntos_fidelidad' => 'required|integer|min:0',
        ]);

        $usuario->update($request->only(['nombres', 'apellidos', 'email']));
        $cliente->update(['puntos_fidelidad' => $request->puntos_fidelidad]);

        return redirect()->back()->with('success', 'Cliente actualizado.');
    }

    public function destroy(int $id)
    {
        $cliente = Cliente::findOrFail($id);
        $cliente->usuario->update(['estado' => false]);

        return redirect()->back()->with('success', 'Cliente suspendido.');
    }
}