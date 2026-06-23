@extends('layouts.app')

@section('title', 'Proveedores - EDDUFARMA')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="color: #1f2937;">
            <i class="fas fa-truck text-success me-2"></i>Gestión de Proveedores
        </h2>
        <p class="text-muted mb-0">Proveedores de medicamentos y productos</p>
    </div>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCrearProveedor" style="border-radius: 10px;">
        <i class="fas fa-plus me-2"></i>Nuevo Proveedor
    </button>
</div>

<div class="row g-4">
    @foreach($proveedores as $proveedor)
        <div class="col-md-6 col-lg-4">
            <div class="card card-dashboard h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start mb-3">
                        <div class="stat-icon bg-success bg-opacity-10 text-success me-3">
                            <i class="fas fa-building"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1" style="color: #1f2937;">{{ $proveedor->razon_social }}</h5>
                            <p class="text-muted small mb-0">RUC: {{ $proveedor->ruc }}</p>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <p class="mb-1 small"><i class="fas fa-user text-success me-2"></i>{{ $proveedor->contacto ?? 'Sin contacto' }}</p>
                        <p class="mb-1 small"><i class="fas fa-phone text-success me-2"></i>{{ $proveedor->telefono ?? 'Sin teléfono' }}</p>
                        @if($proveedor->latitud && $proveedor->longitud)
                            <p class="mb-0 small"><i class="fas fa-map-marker-alt text-success me-2"></i>{{ $proveedor->latitud }}, {{ $proveedor->longitud }}</p>
                        @endif
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge bg-success bg-opacity-10 text-success">{{ $proveedor->lotes_count }} lotes</span>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-outline-success" style="border-radius: 8px;">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('almacen.proveedores.destroy', $proveedor->id_proveedor) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius: 8px;" onclick="return confirm('¿Eliminar proveedor?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

{{ $proveedores->links() }}

{{-- Modal Crear Proveedor --}}
<div class="modal fade" id="modalCrearProveedor" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; border: none;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" style="color: #1f2937;">
                    <i class="fas fa-plus-circle text-success me-2"></i>Nuevo Proveedor
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('almacen.proveedores.store') }}" method="POST">
                @csrf
                <div class="modal-body px-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">RUC</label>
                            <input type="text" name="ruc" class="form-control" style="border-radius: 10px; border: 2px solid #e5e7eb;" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Razón Social</label>
                            <input type="text" name="razon_social" class="form-control" style="border-radius: 10px; border: 2px solid #e5e7eb;" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Contacto</label>
                            <input type="text" name="contacto" class="form-control" style="border-radius: 10px; border: 2px solid #e5e7eb;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Teléfono</label>
                            <input type="text" name="telefono" class="form-control" style="border-radius: 10px; border: 2px solid #e5e7eb;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Latitud</label>
                            <input type="number" step="any" name="latitud" class="form-control" style="border-radius: 10px; border: 2px solid #e5e7eb;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Longitud</label>
                            <input type="number" step="any" name="longitud" class="form-control" style="border-radius: 10px; border: 2px solid #e5e7eb;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 10px;">Cancelar</button>
                    <button type="submit" class="btn btn-success" style="border-radius: 10px;">
                        <i class="fas fa-save me-2"></i>Guardar Proveedor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection