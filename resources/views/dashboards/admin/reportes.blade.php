@extends('layouts.app')

@section('title', 'Reportes - EDDUFARMA')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="color: #1f2937;">
            <i class="fas fa-file-invoice-dollar text-success me-2"></i>Reportes de Ventas
        </h2>
        <p class="text-muted mb-0">Análisis y estadísticas del sistema</p>
    </div>
</div>

{{-- Filtros --}}
<div class="card card-dashboard mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-semibold small">Fecha Inicio</label>
                <input type="date" name="fecha_inicio" class="form-control" value="{{ $fechaInicio }}" style="border-radius: 10px; border: 2px solid #e5e7eb;">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small">Fecha Fin</label>
                <input type="date" name="fecha_fin" class="form-control" value="{{ $fechaFin }}" style="border-radius: 10px; border: 2px solid #e5e7eb;">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-success w-100" style="border-radius: 10px;">
                    <i class="fas fa-filter me-2"></i>Generar Reporte
                </button>
            </div>
        </form>
    </div>
</div>

{{-- KPIs --}}
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card card-dashboard h-100 text-center" style="border-left: 4px solid #10b981;">
            <div class="card-body">
                <div class="stat-icon bg-success bg-opacity-10 text-success mx-auto mb-3">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <h3 class="fw-bold" style="color: #1f2937;">{{ $totalVentas }}</h3>
                <p class="text-muted mb-0">Total Ventas</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-dashboard h-100 text-center" style="border-left: 4px solid #3b82f6;">
            <div class="card-body">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary mx-auto mb-3">
                    <i class="fas fa-coins"></i>
                </div>
                <h3 class="fw-bold" style="color: #1f2937;">S/ {{ number_format($montoTotal, 2) }}</h3>
                <p class="text-muted mb-0">Monto Total</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-dashboard h-100 text-center" style="border-left: 4px solid #f59e0b;">
            <div class="card-body">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning mx-auto mb-3">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3 class="fw-bold" style="color: #1f2937;">S/ {{ number_format($promedioVenta, 2) }}</h3>
                <p class="text-muted mb-0">Promedio por Venta</p>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Ventas por Empleado --}}
    <div class="col-md-6">
        <div class="card card-dashboard h-100">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0" style="color: #1f2937;">
                    <i class="fas fa-users text-success me-2"></i>Ventas por Empleado
                </h5>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Empleado</th>
                                <th>Cantidad</th>
                                <th>Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ventasPorEmpleado as $item)
                                <tr>
                                    <td>{{ $item->empleado->usuario->nombres }} {{ $item->empleado->usuario->apellidos }}</td>
                                    <td>{{ $item->cantidad }}</td>
                                    <td class="fw-bold text-success">S/ {{ number_format($item->monto, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Productos Top --}}
    <div class="col-md-6">
        <div class="card card-dashboard h-100">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0" style="color: #1f2937;">
                    <i class="fas fa-trophy text-success me-2"></i>Productos Más Vendidos
                </h5>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($productosTop as $item)
                                <tr>
                                    <td>{{ $item->producto->nombre }}</td>
                                    <td>{{ $item->total_vendido }}</td>
                                    <td class="fw-bold text-success">S/ {{ number_format($item->monto_total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Ventas por Día --}}
<div class="card card-dashboard mt-4">
    <div class="card-header bg-white border-0 pt-4 px-4">
        <h5 class="fw-bold mb-0" style="color: #1f2937;">
            <i class="fas fa-calendar-alt text-success me-2"></i>Ventas por Día
        </h5>
    </div>
    <div class="card-body px-4 pb-4">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead style="background: #f0fdf4;">
                    <tr>
                        <th class="text-success fw-semibold">Fecha</th>
                        <th class="text-success fw-semibold">Cantidad Ventas</th>
                        <th class="text-success fw-semibold">Monto Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ventasPorDia as $dia)
                        <tr>
                            <td class="fw-semibold">{{ \Carbon\Carbon::parse($dia->fecha)->format('d/m/Y') }}</td>
                            <td>{{ $dia->cantidad }}</td>
                            <td class="fw-bold text-success">S/ {{ number_format($dia->monto, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection