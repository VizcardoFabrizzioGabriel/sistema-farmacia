@extends('layouts.app')

@section('title', 'Comprobante - EDDUFARMA')

@section('styles')
<style>
    .comprobante-container {
        max-width: 800px;
        margin: 0 auto;
        background: white;
        border-radius: 20px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.12);
        padding: 48px;
    }
    
    .comprobante-header {
        border-bottom: 3px solid #10b981;
        padding-bottom: 24px;
        margin-bottom: 24px;
    }
    
    .sello-pagado {
        display: inline-block;
        border: 3px solid #10b981;
        color: #10b981;
        padding: 8px 24px;
        border-radius: 8px;
        font-weight: bold;
        font-size: 18px;
        transform: rotate(-15deg);
        opacity: 0.8;
    }
    
    .qr-placeholder {
        width: 120px;
        height: 120px;
        background: #f3f4f6;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #9ca3af;
    }
    
    @media print {
        body { background: white; }
        .comprobante-container { box-shadow: none; }
        .no-print { display: none; }
    }
</style>
@endsection

@section('content')
<div class="comprobante-container">
    <div class="comprobante-header d-flex justify-content-between align-items-start">
        <div>
            <div class="d-flex align-items-center mb-3">
                <div class="stat-icon bg-success bg-opacity-10 text-success me-3" style="width: 56px; height: 56px; font-size: 24px;">
                    <i class="fas fa-pills"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-0" style="color: #1f2937;">EDDUFARMA</h3>
                    <p class="text-muted mb-0 small">Sistema Integral de Gestión</p>
                </div>
            </div>
            <p class="text-muted small mb-1">RUC: 20123456789</p>
            <p class="text-muted small mb-0">Av. Principal 123, Lima, Perú</p>
        </div>
        <div class="text-end">
            <div class="sello-pagado">PAGADO</div>
            <h5 class="fw-bold mt-3 mb-1" style="color: #1f2937;">{{ $comprobante->tipo }}</h5>
            <p class="text-muted small mb-0">{{ $comprobante->numero_serie }}</p>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-6">
            <h6 class="fw-bold text-success mb-2">CLIENTE</h6>
            <p class="mb-1"><strong>{{ $venta->cliente ? $venta->cliente->usuario->nombres . ' ' . $venta->cliente->usuario->apellidos : 'Cliente General' }}</strong></p>
            <p class="text-muted small mb-0">{{ $venta->cliente ? $venta->cliente->usuario->dni : '-' }}</p>
        </div>
        <div class="col-md-6 text-md-end">
            <h6 class="fw-bold text-success mb-2">FECHA DE EMISIÓN</h6>
            <p class="mb-1">{{ $venta->fecha_hora->format('d/m/Y') }}</p>
            <p class="text-muted small mb-0">{{ $venta->fecha_hora->format('H:i:s') }}</p>
        </div>
    </div>
    
    <table class="table mb-4">
        <thead style="background: #f0fdf4;">
            <tr>
                <th class="text-success">#</th>
                <th class="text-success">Descripción</th>
                <th class="text-success text-center">Cant.</th>
                <th class="text-success text-end">P. Unit.</th>
                <th class="text-success text-end">Importe</th>
            </tr>
        </thead>
        <tbody>
            @foreach($venta->detalles as $index => $detalle)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $detalle->producto->nombre }}</td>
                    <td class="text-center">{{ $detalle->cantidad }}</td>
                    <td class="text-end">S/ {{ number_format($detalle->precio_unitario, 2) }}</td>
                    <td class="text-end">S/ {{ number_format($detalle->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="row justify-content-end">
        <div class="col-md-5">
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Subtotal:</span>
                <span class="fw-semibold">S/ {{ number_format($venta->subtotal, 2) }}</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">IGV (18%):</span>
                <span class="fw-semibold">S/ {{ number_format($venta->impuestos, 2) }}</span>
            </div>
            <div class="d-flex justify-content-between pt-2" style="border-top: 2px solid #10b981;">
                <span class="fw-bold fs-5">TOTAL:</span>
                <span class="fw-bold fs-5 text-success">S/ {{ number_format($venta->total, 2) }}</span>
            </div>
        </div>
    </div>
    
    <div class="row mt-4 pt-4" style="border-top: 1px dashed #e5e7eb;">
        <div class="col-md-6">
            <div class="qr-placeholder mx-auto mx-md-0">
                <i class="fas fa-qrcode fa-3x"></i>
            </div>
            <p class="text-muted small text-center text-md-start mt-2 mb-0">Representación impresa del comprobante</p>
        </div>
        <div class="col-md-6 text-md-end d-flex flex-column justify-content-end">
            <p class="text-muted small mb-0">Gracias por su preferencia</p>
            <p class="text-success fw-bold mb-0">EDDUFARMA</p>
        </div>
    </div>
</div>

<div class="text-center mt-4 no-print">
    <button onclick="window.print()" class="btn btn-success me-2" style="border-radius: 10px;">
        <i class="fas fa-print me-2"></i>Imprimir Comprobante
    </button>
    <a href="{{ route('tecnico.dispensar') }}" class="btn btn-outline-success" style="border-radius: 10px;">
        <i class="fas fa-arrow-left me-2"></i>Nueva Venta
    </a>
</div>
@endsection