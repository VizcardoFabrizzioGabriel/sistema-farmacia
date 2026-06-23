@extends('layouts.app')

@section('title', 'Medicamentos - EDDUFARMA')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="color: #1f2937;">
            <i class="fas fa-capsules text-success me-2"></i>Gestión de Medicamentos
        </h2>
        <p class="text-muted mb-0">Catálogo de productos farmacéuticos</p>
    </div>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCrearProducto" style="border-radius: 10px;">
        <i class="fas fa-plus me-2"></i>Nuevo Medicamento
    </button>
</div>

<div class="card card-dashboard">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: #f0fdf4;">
                    <tr>
                        <th class="text-success fw-semibold">Código</th>
                        <th class="text-success fw-semibold">Nombre</th>
                        <th class="text-success fw-semibold">Categoría</th>
                        <th class="text-success fw-semibold">Precio</th>
                        <th class="text-success fw-semibold">Stock</th>
                        <th class="text-success fw-semibold">Controlado</th>
                        <th class="text-success fw-semibold text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($productos as $producto)
                        <tr>
                            <td class="fw-semibold text-muted">{{ $producto->codigo_barras }}</td>
                            <td class="fw-semibold">{{ $producto->nombre }}</td>
                            <td>
                                <span class="badge bg-success bg-opacity-10 text-success">
                                    {{ $producto->categoria->nombre }}
                                </span>
                            </td>
                            <td class="fw-bold text-success">S/ {{ number_format($producto->precio_venta, 2) }}</td>
                            <td>
                                <span class="badge {{ $producto->stock_actual <= $producto->stock_minimo ? 'bg-danger' : 'bg-success' }} bg-opacity-10 {{ $producto->stock_actual <= $producto->stock_minimo ? 'text-danger' : 'text-success' }}">
                                    {{ $producto->stock_actual }}
                                </span>
                            </td>
                            <td>
                                @if($producto->es_controlado)
                                    <span class="badge bg-warning bg-opacity-10 text-warning">
                                        <i class="fas fa-exclamation-triangle me-1"></i>Sí
                                    </span>
                                @else
                                    <span class="badge bg-success bg-opacity-10 text-success">No</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-success" style="border-radius: 8px;">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('admin.productos.destroy', $producto->id_producto) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius: 8px;" onclick="return confirm('¿Eliminar medicamento?')">
                                        <i class="fas fa-trash"></i>
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

{{ $productos->links() }}

{{-- Modal Crear Producto --}}
<div class="modal fade" id="modalCrearProducto" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; border: none;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" style="color: #1f2937;">
                    <i class="fas fa-plus-circle text-success me-2"></i>Nuevo Medicamento
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.productos.store') }}" method="POST">
                @csrf
                <div class="modal-body px-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Categoría</label>
                            <select name="id_categoria" class="form-select" style="border-radius: 10px; border: 2px solid #e5e7eb;" required>
                                @foreach(\App\Models\Categoria::all() as $cat)
                                    <option value="{{ $cat->id_categoria }}">{{ $cat->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Código de Barras</label>
                            <input type="text" name="codigo_barras" class="form-control" style="border-radius: 10px; border: 2px solid #e5e7eb;" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Nombre</label>
                            <input type="text" name="nombre" class="form-control" style="border-radius: 10px; border: 2px solid #e5e7eb;" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Descripción</label>
                            <textarea name="descripcion" class="form-control" rows="2" style="border-radius: 10px; border: 2px solid #e5e7eb;"></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Precio Venta</label>
                            <input type="number" step="0.01" name="precio_venta" class="form-control" style="border-radius: 10px; border: 2px solid #e5e7eb;" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Stock Mínimo</label>
                            <input type="number" name="stock_minimo" class="form-control" style="border-radius: 10px; border: 2px solid #e5e7eb;" required>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" name="es_controlado" value="1" id="checkControlado">
                                <label class="form-check-label fw-semibold" for="checkControlado">Medicamento Controlado</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" name="requiere_receta" value="1" id="checkReceta">
                                <label class="form-check-label fw-semibold" for="checkReceta">Requiere Receta</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 10px;">Cancelar</button>
                    <button type="submit" class="btn btn-success" style="border-radius: 10px;">
                        <i class="fas fa-save me-2"></i>Guardar Medicamento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection