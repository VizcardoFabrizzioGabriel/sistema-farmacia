@extends('layouts.app')

@section('title', 'Validar Receta - EDDUFARMA')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card card-dashboard">
            <div class="card-body text-center py-5">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning mx-auto mb-4" style="width: 80px; height: 80px; font-size: 32px;">
                    <i class="fas fa-file-prescription"></i>
                </div>
                
                <h3 class="fw-bold mb-2" style="color: #1f2937;">Validación de Receta Requerida</h3>
                <p class="text-muted mb-4">Esta venta contiene medicamentos controlados que requieren validación del farmacéutico.</p>
                
                <div class="alert border-0 mb-4 text-start" style="background: #fffbeb; border-radius: 12px;">
                    <h6 class="fw-bold text-warning mb-2"><i class="fas fa-info-circle me-2"></i>Información de la Venta</h6>
                    <p class="mb-1 small"><strong>ID Venta:</strong> #{{ $venta->id_venta }}</p>
                    <p class="mb-1 small"><strong>Total:</strong> <span class="text-success fw-bold">S/ {{ number_format($venta->total, 2) }}</span></p>
                    <p class="mb-0 small"><strong>Productos controlados:</strong></p>
                    <ul class="mb-0 mt-1">
                        @foreach($venta->detalles as $detalle)
                            @if($detalle->producto->es_controlado || $detalle->producto->requiere_receta)
                                <li class="small">{{ $detalle->producto->nombre }} (x{{ $detalle->cantidad }})</li>
                            @endif
                        @endforeach
                    </ul>
                </div>
                
                <div class="d-grid gap-2">
                    <a href="{{ route('farmaceutico.recetas') }}" class="btn btn-success py-3 fw-bold" style="border-radius: 12px;">
                        <i class="fas fa-arrow-right me-2"></i>Ir a Validar Receta
                    </a>
                    <a href="{{ route('tecnico.dispensar') }}" class="btn btn-outline-secondary" style="border-radius: 10px;">
                        <i class="fas fa-arrow-left me-2"></i>Volver al Punto de Venta
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection