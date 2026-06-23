@extends('layouts.app')

@section('title', 'Seguimiento de Pedidos - EDDUFARMA')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="color: #1f2937;">
            <i class="fas fa-shipping-fast text-success me-2"></i>Seguimiento de Pedidos
        </h2>
        <p class="text-muted mb-0">Estado de tus compras recientes</p>
    </div>
</div>

<div class="row g-4">
    @forelse($ventas as $venta)
        <div class="col-md-6">
            <div class="card card-dashboard h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="fw-bold mb-1" style="color: #1f2937;">Pedido #{{ $venta->id_venta }}</h5>
                            <p class="text-muted small mb-0">{{ $venta->fecha_hora->format('d/m/Y H:i') }}</p>
                        </div>
                        <span class="badge 
                            {{ $venta->estado == 'Pagada' ? 'bg-success' : '' }}
                            {{ $venta->estado == 'Pendiente' ? 'bg-warning text-dark' : '' }}
                            {{ $venta->estado == 'Validando' ? 'bg-info' : '' }}
                            {{ $venta->estado == 'Anulada' ? 'bg-secondary' : '' }}">
                            {{ $venta->estado }}
                        </span>
                    </div>

                    <div class="mb-3">
                        @foreach($venta->detalles as $detalle)
                            <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom: 1px solid #f3f4f6;">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon bg-success bg-opacity-10 text-success me-3" style="width: 36px; height: 36px; font-size: 14px;">
                                        <i class="fas fa-capsules"></i>
                                    </div>
                                    <div>
                                        <p class="fw-semibold mb-0" style="font-size: 14px;">{{ $detalle->producto->nombre }}</p>
                                        <p class="text-muted small mb-0">Cantidad: {{ $detalle->cantidad }}</p>
                                    </div>
                                </div>
                                <span class="fw-bold text-success">S/ {{ number_format($detalle->subtotal, 2) }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-2" style="border-top: 2px solid #10b981;">
                        <span class="fw-bold">Total</span>
                        <span class="fw-bold fs-5 text-success">S/ {{ number_format($venta->total, 2) }}</span>
                    </div>

                    @if($venta->comprobante)
                        <div class="mt-3 p-3 rounded-3" style="background: #f0fdf4;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="fw-semibold mb-0" style="font-size: 14px; color: #1f2937;">
                                        <i class="fas fa-file-invoice text-success me-2"></i>{{ $venta->comprobante->tipo }}
                                    </p>
                                    <p class="text-muted small mb-0">{{ $venta->comprobante->numero_serie }}</p>
                                </div>
                                <span class="badge bg-success bg-opacity-10 text-success">Emitido</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card card-dashboard text-center py-5">
                <div class="card-body">
                    <i class="fas fa-box-open fa-3x text-muted mb-3 opacity-25"></i>
                    <h5 class="fw-bold text-muted">No tienes pedidos recientes</h5>
                    <p class="text-muted mb-3">Realiza tu primera compra en nuestro catálogo</p>
                    <a href="{{ route('cliente.catalogo') }}" class="btn btn-success" style="border-radius: 10px;">
                        <i class="fas fa-th-large me-2"></i>Ver Catálogo
                    </a>
                </div>
            </div>
        </div>
    @endforelse
</div>
@endsection