<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Empleado;
use App\Models\User;
use App\Models\Rol;

class EmpleadoController extends Controller
{
    public function index()
    {
        $empleados = Empleado::with('usuario.rol')->paginate(15);
        $roles = Rol::whereIn('nombre', ['Farmaceutico', 'TecnicoFarmaceutico', 'EncargadoAlmacen', 'Administrativo'])->get();

        return view('dashboards.admin.empleados', compact('empleados', 'roles'));
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
            'fecha_ingreso' => 'required|date',
            'turno' => 'required|string|max:20',
            'numero_colegiatura' => 'nullable|string|max:50',
            'caja_asignada' => 'nullable|integer',
            'cargo' => 'nullable|string|max:50',
        ]);

        $usuario = User::create([
            'id_rol' => $request->id_rol,
            'dni' => $request->dni,
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'email' => $request->email,
            'password_hash' => bcrypt($request->password),
            'estado' => true,
        ]);

        Empleado::create([
            'id_usuario' => $usuario->id_usuario,
            'fecha_ingreso' => $request->fecha_ingreso,
            'turno' => $request->turno,
            'numero_colegiatura' => $request->numero_colegiatura,
            'caja_asignada' => $request->caja_asignada,
            'cargo' => $request->cargo,
        ]);

        return redirect()->back()->with('success', 'Empleado registrado correctamente.');
    }

    public function update(Request $request, int $id)
    {
        $empleado = Empleado::findOrFail($id);
        $usuario = $empleado->usuario;

        $request->validate([
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'email' => 'required|email|unique:usuarios,email,' . $usuario->id_usuario . ',id_usuario',
            'turno' => 'required|string|max:20',
            'numero_colegiatura' => 'nullable|string|max:50',
            'caja_asignada' => 'nullable|integer',
            'cargo' => 'nullable|string|max:50',
        ]);

        $usuario->update($request->only(['nombres', 'apellidos', 'email']));
        $empleado->update($request->only(['turno', 'numero_colegiatura', 'caja_asignada', 'cargo']));

        return redirect()->back()->with('success', 'Empleado actualizado.');
    }

    public function destroy(int $id)
    {
        $empleado = Empleado::findOrFail($id);
        $empleado->usuario->update(['estado' => false]);

        return redirect()->back()->with('success', 'Empleado suspendido.');
    }
}