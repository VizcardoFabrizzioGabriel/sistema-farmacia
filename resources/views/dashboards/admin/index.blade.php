@extends('layouts.app')

@section('title', 'Dashboard Administrativo - EDDUFARMA')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="color: #1f2937;">
            <i class="fas fa-chart-line text-success me-2"></i>Panel Administrativo
        </h2>
        <p class="text-muted mb-0">Visión general del sistema EDDUFARMA</p>
    </div>
    <div class="text-end">
        <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">
            <i class="fas fa-calendar-alt me-1"></i> {{ now()->format('d/m/Y') }}
        </span>
    </div>
</div>

{{-- KPIs --}}
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card card-dashboard h-100" style="border-left: 4px solid #10b981;">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-success bg-opacity-10 text-success me-3">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Total Usuarios</h6>
                    <h3 class="fw-bold mb-0" style="color: #1f2937;">{{ $totalUsuarios }}</h3>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card card-dashboard h-100" style="border-left: 4px solid #3b82f6;">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3">
                    <i class="fas fa-capsules"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Medicamentos</h6>
                    <h3 class="fw-bold mb-0" style="color: #1f2937;">{{ $totalProductos }}</h3>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card card-dashboard h-100" style="border-left: 4px solid #f59e0b;">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning me-3">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Ventas Hoy</h6>
                    <h3 class="fw-bold mb-0" style="color: #1f2937;">{{ $totalVentasHoy }}</h3>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card card-dashboard h-100" style="border-left: 4px solid #ef4444;">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-danger bg-opacity-10 text-danger me-3">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Stock Bajo</h6>
                    <h3 class="fw-bold mb-0" style="color: #1f2937;">{{ $stockBajo }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Monto de ventas hoy --}}
<div class="card card-dashboard mb-4" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none;">
    <div class="card-body text-white">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h5 class="fw-semibold mb-1"><i class="fas fa-coins me-2"></i>Ventas del Día</h5>
                <h2 class="fw-bold mb-0">S/ {{ number_format($montoVentasHoy, 2) }}</h2>
            </div>
            <div class="col-md-4 text-end">
                <i class="fas fa-chart-bar" style="font-size: 64px; opacity: 0.2;"></i>
            </div>
        </div>
    </div>
</div>

{{-- Gráfico de ventas semana --}}
<div class="card card-dashboard">
    <div class="card-header bg-white border-0 pt-4 px-4">
        <h5 class="fw-bold mb-0" style="color: #1f2937;">
            <i class="fas fa-chart-area text-success me-2"></i>Ventas de los Últimos 7 Días
        </h5>
    </div>
    <div class="card-body px-4 pb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Fecha</th>
                        <th>Cantidad Ventas</th>
                        <th>Monto Total</th>
                        <th>Tendencia</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ventasSemana as $venta)
                        <tr>
                            <td class="fw-semibold">{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}</td>
                            <td><span class="badge bg-success bg-opacity-10 text-success">{{ $venta->cantidad }}</span></td>
                            <td class="fw-bold text-success">S/ {{ number_format($venta->monto, 2) }}</td>
                            <td><i class="fas fa-arrow-up text-success"></i></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">No hay ventas registradas esta semana</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection