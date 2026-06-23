@extends('layouts.app')

@section('title', 'Ticket - EDDUFARMA')

@section('styles')
<style>
    .ticket-container {
        max-width: 400px;
        margin: 0 auto;
        background: white;
        border-radius: 16px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.12);
        padding: 32px;
    }
    
    .ticket-header {
        text-align: center;
        border-bottom: 2px dashed #e5e7eb;
        padding-bottom: 20px;
        margin-bottom: 20px;
    }
    
    .ticket-logo {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 12px;
        color: white;
        font-size: 24px;
    }
    
    .ticket-line {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px solid #f3f4f6;
    }
    
    .ticket-total {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        border-radius: 12px;
        padding: 16px;
        margin-top: 16px;
    }
    
    .barcode {
        text-align: center;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 2px dashed #e5e7eb;
    }
    
    .barcode-line {
        height: 60px;
        background: repeating-linear-gradient(
            90deg,
            #1f2937 0px,
            #1f2937 2px,
            transparent 2px,
            transparent 4px
        );
        margin: 10px 0;
    }
    
    @media print {
        body { background: white; }
        .ticket-container { box-shadow: none; }
        .no-print { display: none; }
    }
</style>
@endsection

@section('content')
<div class="ticket-container">
    <div class="ticket-header">
        <div class="ticket-logo">
            <i class="fas fa-pills"></i>
        </div>
        <h4 class="fw-bold mb-1" style="color: #1f2937;">EDDUFARMA</h4>
        <p class="text-muted small mb-0">Sistema Integral de Gestión</p>
        <p class="text-muted small">RUC: 20123456789</p>
    </div>
    
    <div class="mb-3">
        <div class="ticket-line">
            <span class="text-muted">Comprobante:</span>
            <span class="fw-bold">{{ $ticket['numero_serie'] }}</span>
        </div>
        <div class="ticket-line">
            <span class="text-muted">Fecha:</span>
            <span>{{ $ticket['fecha'] }}</span>
        </div>
        <div class="ticket-line">
            <span class="text-muted">Cliente:</span>
            <span>{{ $ticket['cliente'] }}</span>
        </div>
        <div class="ticket-line">
            <span class="text-muted">Atendido por:</span>
            <span>{{ $ticket['empleado'] }}</span>
        </div>
    </div>
    
    <h6 class="fw-bold mb-2" style="color: #1f2937;">DETALLE DE COMPRA</h6>
    
    @foreach($ticket['detalles'] as $detalle)
        <div class="ticket-line">
            <span>{{ $detalle['cantidad'] }}x {{ $detalle['producto'] }}</span>
            <span class="fw-semibold">S/ {{ number_format($detalle['subtotal'], 2) }}</span>
        </div>
    @endforeach
    
    <div class="ticket-total">
        <div class="d-flex justify-content-between mb-1">
            <span>Subtotal</span>
            <span>S/ {{ number_format($ticket['subtotal'], 2) }}</span>
        </div>
        <div class="d-flex justify-content-between mb-1">
            <span>IGV (18%)</span>
            <span>S/ {{ number_format($ticket['impuestos'], 2) }}</span>
        </div>
        <div class="d-flex justify-content-between mt-2 pt-2" style="border-top: 1px solid rgba(255,255,255,0.3);">
            <span class="fw-bold fs-5">TOTAL</span>
            <span class="fw-bold fs-5">S/ {{ number_format($ticket['total'], 2) }}</span>
        </div>
    </div>
    
    <div class="barcode">
        <div class="barcode-line"></div>
        <p class="text-muted small mb-0">{{ $ticket['numero_serie'] }}</p>
        <p class="text-muted small">Gracias por su preferencia</p>
    </div>
</div>

<div class="text-center mt-4 no-print">
    <button onclick="window.print()" class="btn btn-success me-2" style="border-radius: 10px;">
        <i class="fas fa-print me-2"></i>Imprimir Ticket
    </button>
    <a href="{{ route('tecnico.dispensar') }}" class="btn btn-outline-success" style="border-radius: 10px;">
        <i class="fas fa-arrow-left me-2"></i>Nueva Venta
    </a>
</div>
@endsection