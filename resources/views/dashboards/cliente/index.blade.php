@extends('layouts.app')

@section('title', 'Mi Cuenta - EDDUFARMA')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="color: #1f2937;">
            <i class="fas fa-user-circle text-success me-2"></i>Bienvenido, {{ auth()->user()->nombres }}
        </h2>
        <p class="text-muted mb-0">Panel de cliente EDDUFARMA</p>
    </div>
    <div class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">
        <i class="fas fa-star me-1"></i>{{ $puntosFidelidad }} Puntos Fidelidad
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card card-dashboard h-100 text-center" style="border-left: 4px solid #10b981;">
            <div class="card-body">
                <div class="stat-icon bg-success bg-opacity-10 text-success mx-auto mb-3">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <h3 class="fw-bold" style="color: #1f2937;">{{ $totalCompras }}</h3>
                <p class="text-muted mb-0">Compras Realizadas</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card card-dashboard h-100 text-center" style="border-left: 4px solid #3b82f6;">
            <div class="card-body">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary mx-auto mb-3">
                    <i class="fas fa-wallet"></i>
                </div>
                <h3 class="fw-bold" style="color: #1f2937;">S/ {{ number_format($totalGastado, 2) }}</h3>
                <p class="text-muted mb-0">Total Gastado</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card card-dashboard h-100 text-center" style="border-left: 4px solid #f59e0b;">
            <div class="card-body">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning mx-auto mb-3">
                    <i class="fas fa-gift"></i>
                </div>
                <h3 class="fw-bold" style="color: #1f2937;">{{ $puntosFidelidad }}</h3>
                <p class="text-muted mb-0">Puntos Acumulados</p>
            </div>
        </div>
    </div>
</div>

<div class="card card-dashboard mb-4">
    <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0" style="color: #1f2937;">
            <i class="fas fa-th-large text-success me-2"></i>Productos Destacados
        </h5>
        <a href="{{ route('cliente.catalogo') }}" class="btn btn-success btn-sm" style="border-radius: 8px;">
            <i class="fas fa-th me-2"></i>Ver Catálogo Completo
        </a>
    </div>
    <div class="card-body px-4 pb-4">
        <div class="row g-3">
            @foreach($productosDestacados as $producto)
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm" style="border-radius: 16px;">
                        <div class="card-body">
                            <span class="badge {{ $producto->es_controlado ? 'bg-warning text-dark' : 'bg-success bg-opacity-10 text-success' }} mb-2">
                                {{ $producto->es_controlado ? 'Controlado' : 'Venta Libre' }}
                            </span>
                            <h6 class="fw-bold" style="color: #1f2937;">{{ $producto->nombre }}</h6>
                            <p class="text-muted small mb-2">{{ $producto->categoria->nombre }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="text-success fw-bold mb-0">S/ {{ number_format($producto->precio_venta, 2) }}</h5>
                                <span class="text-muted small">Stock: {{ $producto->stock_actual }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <a href="{{ route('cliente.historial') }}" class="card card-dashboard text-decoration-none h-100">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-success bg-opacity-10 text-success me-3">
                    <i class="fas fa-history"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-1" style="color: #1f2937;">Historial de Compras</h5>
                    <p class="text-muted mb-0">Revisa tus pedidos anteriores</p>
                </div>
            </div>
        </a>
    </div>
    
    <div class="col-md-6">
        <a href="{{ route('cliente.catalogo') }}" class="card card-dashboard text-decoration-none h-100">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-1" style="color: #1f2937;">Catálogo de Productos</h5>
                    <p class="text-muted mb-0">Explora nuestros medicamentos</p>
                </div>
            </div>
        </a>
    </div>
</div>
@endsection