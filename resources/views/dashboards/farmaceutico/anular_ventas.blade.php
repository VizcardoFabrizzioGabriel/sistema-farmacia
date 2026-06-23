@extends('layouts.app')

@section('title', 'Anular Ventas - EDDUFARMA')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="color: #1f2937;">
            <i class="fas fa-undo-alt text-success me-2"></i>Anular Ventas
        </h2>
        <p class="text-muted mb-0">Cancelar ventas del día actual por error médico</p>
    </div>
</div>

<div class="alert border-0 mb-4" style="background: #fffbeb; border-radius: 12px;">
    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
    <strong>Importante:</strong> Solo se pueden anular ventas realizadas durante el mismo día del turno actual.
</div>

<div class="card card-dashboard">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: #f0fdf4;">
                    <tr>
                        <th class="text-success fw-semibold">ID</th>
                        <th class="text-success fw-semibold">Fecha</th>
                        <th class="text-success fw-semibold">Cliente</th>
                        <th class="text-success fw-semibold">Empleado</th>
                        <th class="text-success fw-semibold">Total</th>
                        <th class="text-success fw-semibold">Método Pago</th>
                        <th class="text-success fw-semibold text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ventas as $venta)
                        <tr>
                            <td class="fw-semibold">#{{ $venta->id_venta }}</td>
                            <td>{{ $venta->fecha_hora->format('d/m/Y H:i') }}</td>
                            <td>{{ $venta->cliente ? $venta->cliente->usuario->nombres . ' ' . $venta->cliente->usuario->apellidos : 'Cliente General' }}</td>
                            <td>{{ $venta->empleado->usuario->nombres }} {{ $venta->empleado->usuario->apellidos }}</td>
                            <td class="fw-bold text-success">S/ {{ number_format($venta->total, 2) }}</td>
                            <td>
                                <span class="badge bg-success bg-opacity-10 text-success">
                                    {{ $venta->metodo_pago }}
                                </span>
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-danger" onclick="confirmarAnulacion({{ $venta->id_venta }})" style="border-radius: 8px;">
                                    <i class="fas fa-ban me-1"></i>Anular
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="fas fa-check-circle text-success me-2"></i>No hay ventas disponibles para anular hoy
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function confirmarAnulacion(idVenta) {
    if (!confirm('¿Está seguro de anular esta venta? El stock será devuelto al inventario.')) {
        return;
    }

    fetch('/farmaceutico/anular-venta/' + idVenta, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.csrfToken
        }
    })
    .then(r => {
        if (r.redirected) {
            window.location.href = r.url;
            return;
        }
        return r.json();
    })
    .then(data => {
        if (data) {
            alert(data.message || 'Operación completada');
            location.reload();
        }
    });
}
</script>
@endsection