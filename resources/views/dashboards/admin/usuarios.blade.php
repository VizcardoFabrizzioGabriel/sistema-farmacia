@extends('layouts.app')

@section('title', 'Gestión de Usuarios - EDDUFARMA')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="color: #1f2937;">
            <i class="fas fa-users text-success me-2"></i>Gestión de Usuarios
        </h2>
        <p class="text-muted mb-0">Administrar personal y clientes del sistema</p>
    </div>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCrearUsuario" style="border-radius: 10px;">
        <i class="fas fa-plus me-2"></i>Nuevo Usuario
    </button>
</div>

<div class="card card-dashboard">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: #f0fdf4;">
                    <tr>
                        <th class="text-success fw-semibold">DNI</th>
                        <th class="text-success fw-semibold">Nombres</th>
                        <th class="text-success fw-semibold">Email</th>
                        <th class="text-success fw-semibold">Rol</th>
                        <th class="text-success fw-semibold">Estado</th>
                        <th class="text-success fw-semibold text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($usuarios as $usuario)
                        <tr>
                            <td class="fw-semibold">{{ $usuario->dni }}</td>
                            <td>{{ $usuario->nombres }} {{ $usuario->apellidos }}</td>
                            <td>{{ $usuario->email }}</td>
                            <td>
                                <span class="badge bg-success bg-opacity-10 text-success">
                                    {{ $usuario->rol->nombre }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $usuario->estado ? 'bg-success' : 'bg-secondary' }} bg-opacity-10 {{ $usuario->estado ? 'text-success' : 'text-secondary' }}">
                                    {{ $usuario->estado ? 'Activo' : 'Suspendido' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-success" style="border-radius: 8px;">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('admin.usuarios.destroy', $usuario->id_usuario) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius: 8px;" onclick="return confirm('¿Suspender usuario?')">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{ $usuarios->links() }}

{{-- Modal Crear Usuario --}}
<div class="modal fade" id="modalCrearUsuario" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; border: none;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" style="color: #1f2937;">
                    <i class="fas fa-user-plus text-success me-2"></i>Nuevo Usuario
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.usuarios.store') }}" method="POST">
                @csrf
                <div class="modal-body px-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Rol</label>
                            <select name="id_rol" class="form-select" style="border-radius: 10px; border: 2px solid #e5e7eb;" required>
                                @foreach($roles as $rol)
                                    <option value="{{ $rol->id_rol }}">{{ $rol->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">DNI</label>
                            <input type="text" name="dni" class="form-control" style="border-radius: 10px; border: 2px solid #e5e7eb;" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Nombres</label>
                            <input type="text" name="nombres" class="form-control" style="border-radius: 10px; border: 2px solid #e5e7eb;" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Apellidos</label>
                            <input type="text" name="apellidos" class="form-control" style="border-radius: 10px; border: 2px solid #e5e7eb;" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Email</label>
                            <input type="email" name="email" class="form-control" style="border-radius: 10px; border: 2px solid #e5e7eb;" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Contraseña</label>
                            <input type="password" name="password" class="form-control" style="border-radius: 10px; border: 2px solid #e5e7eb;" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Fecha Ingreso</label>
                            <input type="date" name="fecha_ingreso" class="form-control" style="border-radius: 10px; border: 2px solid #e5e7eb;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Turno</label>
                            <select name="turno" class="form-select" style="border-radius: 10px; border: 2px solid #e5e7eb;">
                                <option value="Mañana">Mañana</option>
                                <option value="Tarde">Tarde</option>
                                <option value="Noche">Noche</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 10px;">Cancelar</button>
                    <button type="submit" class="btn btn-success" style="border-radius: 10px;">
                        <i class="fas fa-save me-2"></i>Guardar Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection