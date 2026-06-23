@extends('layouts.app')

@section('title', 'Gestión de Lotes - EDDUFARMA')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="color: #1f2937;">
            <i class="fas fa-boxes text-success me-2"></i>Gestión de Lotes
        </h2>
        <p class="text-muted mb-0">Control de inventario por lotes FEFO</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('almacen.lotes') }}?ordenar=heapsort" class="btn btn-outline-success" style="border-radius: 10px;">
            <i class="fas fa-sort-amount-down me-2"></i>Ordenar Heapsort
        </a>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCrearLote" style="border-radius: 10px;">
            <i class="fas fa-plus me-2"></i>Nuevo Lote
        </button>
    </div>
</div>

<div class="card card-dashboard">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: #f0fdf4;">
                    <tr>
                        <th class="text-success fw-semibold">Lote</th>
                        <th class="text-success fw-semibold">Producto</th>
                        <th class="text-success fw-semibold">Proveedor</th>
                        <th class="text-success fw-semibold">Fecha Venc.</th>
                        <th class="text-success fw-semibold">Cantidad</th>
                        <th class="text-success fw-semibold">Estado</th>
                        <th class="text-success fw-semibold text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lotes as $lote)
                        <tr>
                            <td class="fw-semibold">{{ $lote->numero_lote }}</td>
                            <td>{{ $lote->producto->nombre }}</td>
                            <td>{{ $lote->proveedor->razon_social }}</td>
                            <td>{{ $lote->fecha_vencimiento->format('d/m/Y') }}</td>
                            <td>{{ $lote->cantidad_actual }}/{{ $lote->cantidad_inicial }}</td>
                            <td>
                                <span class="badge badge-{{ strtolower($lote->estado) }}">
                                    {{ $lote->estado }}
                                </span>
                            </td>
                            <td class="text-end">
                                <form action="{{ route('almacen.lotes.estado', $lote->id_lote) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <select name="estado" class="form-select form-select-sm d-inline w-auto" onchange="this.form.submit()" style="border-radius: 8px;">
                                        <option value="Activo" {{ $lote->estado == 'Activo' ? 'selected' : '' }}>Activo</option>
                                        <option value="PorVencer" {{ $lote->estado == 'PorVencer' ? 'selected' : '' }}>PorVencer</option>
                                        <option value="Cuarentena" {{ $lote->estado == 'Cuarentena' ? 'selected' : '' }}>Cuarentena</option>
                                        <option value="Baja" {{ $lote->estado == 'Baja' ? 'selected' : '' }}>Baja</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{ $lotes->links() }}

{{-- Modal Crear Lote --}}
<div class="modal fade" id="modalCrearLote" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; border: none;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" style="color: #1f2937;">
                    <i class="fas fa-plus-circle text-success me-2"></i>Nuevo Lote
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('almacen.lotes.store') }}" method="POST">
                @csrf
                <div class="modal-body px-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Producto</label>
                            <select name="id_producto" class="form-select" style="border-radius: 10px; border: 2px solid #e5e7eb;" required>
                                @foreach(\App\Models\Producto::all() as $prod)
                                    <option value="{{ $prod->id_producto }}">{{ $prod->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Proveedor</label>
                            <select name="id_proveedor" class="form-select" style="border-radius: 10px; border: 2px solid #e5e7eb;" required>
                                @foreach(\App\Models\Proveedor::all() as $prov)
                                    <option value="{{ $prov->id_proveedor }}">{{ $prov->razon_social }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Número de Lote</label>
                            <input type="text" name="numero_lote" class="form-control" style="border-radius: 10px; border: 2px solid #e5e7eb;" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Fecha Fabricación</label>
                            <input type="date" name="fecha_fabricacion" class="form-control" style="border-radius: 10px; border: 2px solid #e5e7eb;" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Fecha Vencimiento</label>
                            <input type="date" name="fecha_vencimiento" class="form-control" style="border-radius: 10px; border: 2px solid #e5e7eb;" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Cantidad Inicial</label>
                            <input type="number" name="cantidad_inicial" class="form-control" style="border-radius: 10px; border: 2px solid #e5e7eb;" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 10px;">Cancelar</button>
                    <button type="submit" class="btn btn-success" style="border-radius: 10px;">
                        <i class="fas fa-save me-2"></i>Registrar Lote
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection