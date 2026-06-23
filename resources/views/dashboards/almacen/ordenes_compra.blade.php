@extends('layouts.app')

@section('title', 'Órdenes de Compra - EDDUFARMA')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="color: #1f2937;">
            <i class="fas fa-clipboard-list text-success me-2"></i>Órdenes de Compra
        </h2>
        <p class="text-muted mb-0">Gestión de pedidos a proveedores</p>
    </div>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCrearOrden" style="border-radius: 10px;">
        <i class="fas fa-plus me-2"></i>Nueva Orden
    </button>
</div>

<div class="card card-dashboard">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: #f0fdf4;">
                    <tr>
                        <th class="text-success fw-semibold">ID</th>
                        <th class="text-success fw-semibold">Proveedor</th>
                        <th class="text-success fw-semibold">Fecha</th>
                        <th class="text-success fw-semibold">Total Est.</th>
                        <th class="text-success fw-semibold">Estado</th>
                        <th class="text-success fw-semibold text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ordenes as $orden)
                        <tr>
                            <td class="fw-semibold">#{{ $orden->id_orden }}</td>
                            <td>{{ $orden->proveedor->razon_social }}</td>
                            <td>{{ $orden->fecha_emision->format('d/m/Y') }}</td>
                            <td class="fw-bold text-success">S/ {{ number_format($orden->total_estimado, 2) }}</td>
                            <td>
                                <span class="badge 
                                    {{ $orden->estado == 'Borrador' ? 'bg-secondary' : '' }}
                                    {{ $orden->estado == 'Enviada' ? 'bg-warning text-dark' : '' }}
                                    {{ $orden->estado == 'Recibida' ? 'bg-success' : '' }}">
                                    {{ $orden->estado }}
                                </span>
                            </td>
                            <td class="text-end">
                                @if($orden->estado == 'Borrador')
                                    <form action="{{ route('almacen.ordenes.enviar', $orden->id_orden) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" style="border-radius: 8px;">
                                            <i class="fas fa-paper-plane me-1"></i>Enviar
                                        </button>
                                    </form>
                                @elseif($orden->estado == 'Enviada')
                                    <form action="{{ route('almacen.ordenes.recibir', $orden->id_orden) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-primary" style="border-radius: 8px;">
                                            <i class="fas fa-check me-1"></i>Recibir
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{ $ordenes->links() }}

{{-- Modal Crear Orden --}}
<div class="modal fade" id="modalCrearOrden" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 20px; border: none;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" style="color: #1f2937;">
                    <i class="fas fa-plus-circle text-success me-2"></i>Nueva Orden de Compra
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('almacen.ordenes.store') }}" method="POST" id="formOrden">
                @csrf
                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Proveedor</label>
                        <select name="id_proveedor" class="form-select" style="border-radius: 10px; border: 2px solid #e5e7eb;" required>
                            @foreach($proveedores as $prov)
                                <option value="{{ $prov->id_proveedor }}">{{ $prov->razon_social }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <h6 class="fw-bold mt-4 mb-3" style="color: #1f2937;">Productos</h6>
                    <div id="productosOrden">
                        <div class="row g-2 mb-2 producto-row">
                            <div class="col-md-6">
                                <select name="productos[0][id_producto]" class="form-select" style="border-radius: 10px; border: 2px solid #e5e7eb;" required>
                                    @foreach($productos as $prod)
                                        <option value="{{ $prod->id_producto }}">{{ $prod->nombre }} - S/ {{ number_format($prod->precio_venta, 2) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="number" name="productos[0][cantidad]" class="form-control" placeholder="Cantidad" style="border-radius: 10px; border: 2px solid #e5e7eb;" required>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-outline-danger w-100" onclick="this.closest('.producto-row').remove()" style="border-radius: 8px;">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <button type="button" class="btn btn-outline-success btn-sm mt-2" onclick="agregarProductoOrden()" style="border-radius: 8px;">
                        <i class="fas fa-plus me-1"></i>Agregar Producto
                    </button>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 10px;">Cancelar</button>
                    <button type="submit" class="btn btn-success" style="border-radius: 10px;">
                        <i class="fas fa-save me-2"></i>Crear Orden
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let contadorProductos = 1;

function agregarProductoOrden() {
    const container = document.getElementById('productosOrden');
    const nuevo = document.createElement('div');
    nuevo.className = 'row g-2 mb-2 producto-row';
    nuevo.innerHTML = `
        <div class="col-md-6">
            <select name="productos[${contadorProductos}][id_producto]" class="form-select" style="border-radius: 10px; border: 2px solid #e5e7eb;" required>
                @foreach($productos as $prod)
                    <option value="{{ $prod->id_producto }}">{{ $prod->nombre }} - S/ {{ number_format($prod->precio_venta, 2) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <input type="number" name="productos[${contadorProductos}][cantidad]" class="form-control" placeholder="Cantidad" style="border-radius: 10px; border: 2px solid #e5e7eb;" required>
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-outline-danger w-100" onclick="this.closest('.producto-row').remove()" style="border-radius: 8px;">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    container.appendChild(nuevo);
    contadorProductos++;
}
</script>
@endsection