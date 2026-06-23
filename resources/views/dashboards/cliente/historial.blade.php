@extends('layouts.app')

@section('title', 'Historial - EDDUFARMA')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="color: #1f2937;">
            <i class="fas fa-history text-success me-2"></i>Historial de Compras
        </h2>
        <p class="text-muted mb-0">Tus pedidos realizados en EDDUFARMA</p>
    </div>
</div>

<div class="card card-dashboard">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: #f0fdf4;">
                    <tr>
                        <th class="text-success fw-semibold">ID</th>
                        <th class="text-success fw-semibold">Fecha</th>
                        <th class="text-success fw-semibold">Productos</th>
                        <th class="text-success fw-semibold">Total</th>
                        <th class="text-success fw-semibold">Estado</th>
                        <th class="text-success fw-semibold">Comprobante</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ventas as $venta)
                        <tr>
                            <td class="fw-semibold">#{{ $venta->id_venta }}</td>
                            <td>{{ $venta->fecha_hora->format('d/m/Y H:i') }}</td>
                            <td>{{ $venta->detalles->count() }} productos</td>
                            <td class="fw-bold text-success">S/ {{ number_format($venta->total, 2) }}</td>
                            <td>
                                <span class="badge bg-success bg-opacity-10 text-success">
                                    <i class="fas fa-check-circle me-1"></i>{{ $venta->estado }}
                                </span>
                            </td>
                            <td>
                                @if($venta->comprobante)
                                    <span class="text-muted small">{{ $venta->comprobante->numero_serie }}</span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="fas fa-shopping-bag fa-3x mb-3 opacity-25"></i>
                                <p>No has realizado compras aún</p>
                                <a href="{{ route('cliente.catalogo') }}" class="btn btn-success btn-sm" style="border-radius: 8px;">
                                    <i class="fas fa-th-large me-2"></i>Ver Catálogo
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{ $ventas->links() }}
@endsection