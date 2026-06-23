@extends('layouts.app')

@section('title', 'Dashboard Almacén - EDDUFARMA')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="color: #1f2937;">
            <i class="fas fa-warehouse text-success me-2"></i>Panel de Almacén
        </h2>
        <p class="text-muted mb-0">Gestión de inventario, lotes y proveedores</p>
    </div>
    <a href="{{ route('almacen.mapa') }}" class="btn btn-success" style="border-radius: 10px;">
        <i class="fas fa-map-marked-alt me-2"></i>Ver Mapa de Proveedores
    </a>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card card-dashboard h-100" style="border-left: 4px solid #10b981;">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-success bg-opacity-10 text-success me-3">
                    <i class="fas fa-boxes"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Total Productos</h6>
                    <h3 class="fw-bold mb-0" style="color: #1f2937;">{{ $totalProductos }}</h3>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card card-dashboard h-100" style="border-left: 4px solid #f59e0b;">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning me-3">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Por Vencer</h6>
                    <h3 class="fw-bold mb-0" style="color: #1f2937;">{{ $lotesPorVencer }}</h3>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card card-dashboard h-100" style="border-left: 4px solid #ef4444;">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-danger bg-opacity-10 text-danger me-3">
                    <i class="fas fa-ban"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">En Cuarentena</h6>
                    <h3 class="fw-bold mb-0" style="color: #1f2937;">{{ $lotesCuarentena }}</h3>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card card-dashboard h-100" style="border-left: 4px solid #3b82f6;">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3">
                    <i class="fas fa-truck"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Proveedores</h6>
                    <h3 class="fw-bold mb-0" style="color: #1f2937;">{{ $totalProveedores }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card card-dashboard">
    <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0" style="color: #1f2937;">
            <i class="fas fa-clipboard-list text-success me-2"></i>Sugerencias de Compra
        </h5>
        <a href="{{ route('almacen.lotes') }}?ordenar=heapsort" class="btn btn-outline-success btn-sm" style="border-radius: 8px;">
            <i class="fas fa-sort-amount-down me-2"></i>Ordenar con Heapsort
        </a>
    </div>
    <div class="card-body px-4 pb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Producto</th>
                        <th>Stock Actual</th>
                        <th>Stock Mínimo</th>
                        <th>Cantidad Sugerida</th>
                        <th>Proveedor Principal</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sugerenciasCompra as $sugerencia)
                        <tr>
                            <td class="fw-semibold">{{ $sugerencia['nombre'] }}</td>
                            <td><span class="badge bg-danger bg-opacity-10 text-danger">{{ $sugerencia['stock_actual'] }}</span></td>
                            <td>{{ $sugerencia['stock_minimo'] }}</td>
                            <td class="fw-bold text-success">{{ $sugerencia['cantidad_sugerida'] }}</td>
                            <td>{{ $sugerencia['proveedor_principal'] }}</td>
                            <td>
                                <a href="{{ route('almacen.ordenes') }}" class="btn btn-success btn-sm" style="border-radius: 8px;">
                                    <i class="fas fa-plus me-1"></i>Crear Orden
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fas fa-check-circle text-success me-2"></i>No hay sugerencias de compra pendientes
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection