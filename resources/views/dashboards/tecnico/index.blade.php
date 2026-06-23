@extends('layouts.app')

@section('title', 'Dashboard Técnico - EDDUFARMA')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="color: #1f2937;">
            <i class="fas fa-desktop text-success me-2"></i>Panel del Técnico Farmacéutico
        </h2>
        <p class="text-muted mb-0">Atención en mostrador y punto de venta</p>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card card-dashboard h-100" style="border-left: 4px solid #10b981;">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-success bg-opacity-10 text-success me-3">
                    <i class="fas fa-capsules"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Productos Disponibles</h6>
                    <h3 class="fw-bold mb-0" style="color: #1f2937;">{{ $productosDisponibles }}</h3>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card card-dashboard h-100" style="border-left: 4px solid #3b82f6;">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Mis Ventas Hoy</h6>
                    <h3 class="fw-bold mb-0" style="color: #1f2937;">{{ $misVentasHoy }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-12">
        <a href="{{ route('tecnico.dispensar') }}" class="card card-dashboard text-decoration-none">
            <div class="card-body text-center py-5">
                <div class="stat-icon bg-success bg-opacity-10 text-success mx-auto mb-3" style="width: 80px; height: 80px; font-size: 36px;">
                    <i class="fas fa-cash-register"></i>
                </div>
                <h3 class="fw-bold mb-2" style="color: #1f2937;">Abrir Punto de Venta</h3>
                <p class="text-muted mb-0">Iniciar nueva transacción de dispensación</p>
            </div>
        </a>
    </div>
</div>

<div class="card card-dashboard mt-4">
    <div class="card-header bg-white border-0 pt-4 px-4">
        <h5 class="fw-bold mb-0" style="color: #1f2937;">
            <i class="fas fa-lightbulb text-success me-2"></i>Consejo del Día
        </h5>
    </div>
    <div class="card-body px-4 pb-4">
        <div class="alert border-0 mb-0" style="background: #f0fdf4; border-radius: 12px;">
            <i class="fas fa-info-circle text-success me-2"></i>
            Recuerda verificar siempre la fecha de vencimiento antes de dispensar cualquier medicamento. La seguridad del paciente es nuestra prioridad.
        </div>
    </div>
</div>
@endsection