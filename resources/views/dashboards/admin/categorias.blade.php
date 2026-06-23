@extends('layouts.app')

@section('title', 'Categorías - EDDUFARMA')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="color: #1f2937;">
            <i class="fas fa-tags text-success me-2"></i>Categorías de Medicamentos
        </h2>
        <p class="text-muted mb-0">Clasificación de productos farmacéuticos</p>
    </div>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCrearCategoria" style="border-radius: 10px;">
        <i class="fas fa-plus me-2"></i>Nueva Categoría
    </button>
</div>

<div class="row g-4">
    @foreach($categorias as $categoria)
        <div class="col-md-4">
            <div class="card card-dashboard h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="stat-icon bg-success bg-opacity-10 text-success">
                            <i class="fas fa-folder"></i>
                        </div>
                        <span class="badge bg-success bg-opacity-10 text-success">{{ $categoria->productos_count }} productos</span>
                    </div>
                    <h5 class="fw-bold mb-2" style="color: #1f2937;">{{ $categoria->nombre }}</h5>
                    <p class="text-muted small mb-3">{{ $categoria->descripcion ?? 'Sin descripción' }}</p>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-success" style="border-radius: 8px;">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="{{ route('admin.categorias.destroy', $categoria->id_categoria) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius: 8px;" onclick="return confirm('¿Eliminar categoría?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

{{-- Modal Crear Categoría --}}
<div class="modal fade" id="modalCrearCategoria" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; border: none;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" style="color: #1f2937;">
                    <i class="fas fa-plus-circle text-success me-2"></i>Nueva Categoría
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.categorias.store') }}" method="POST">
                @csrf
                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nombre</label>
                        <input type="text" name="nombre" class="form-control" style="border-radius: 10px; border: 2px solid #e5e7eb;" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="3" style="border-radius: 10px; border: 2px solid #e5e7eb;"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 10px;">Cancelar</button>
                    <button type="submit" class="btn btn-success" style="border-radius: 10px;">
                        <i class="fas fa-save me-2"></i>Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection