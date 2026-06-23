@extends('layouts.app')

@section('title', 'Validar Recetas - EDDUFARMA')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="color: #1f2937;">
            <i class="fas fa-file-prescription text-success me-2"></i>Validación de Recetas Médicas
        </h2>
        <p class="text-muted mb-0">Aprobar o rechazar recetas para medicamentos controlados</p>
    </div>
</div>

<div class="card card-dashboard">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: #f0fdf4;">
                    <tr>
                        <th class="text-success fw-semibold">Código</th>
                        <th class="text-success fw-semibold">Médico</th>
                        <th class="text-success fw-semibold">DNI Médico</th>
                        <th class="text-success fw-semibold">Fecha Emisión</th>
                        <th class="text-success fw-semibold">Productos</th>
                        <th class="text-success fw-semibold">Estado</th>
                        <th class="text-success fw-semibold text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recetas as $receta)
                        <tr>
                            <td class="fw-semibold">{{ $receta->codigo_receta }}</td>
                            <td>{{ $receta->nombre_medico }}</td>
                            <td>{{ $receta->dni_medico }}</td>
                            <td>{{ $receta->fecha_emision->format('d/m/Y') }}</td>
                            <td>
                                @if($receta->venta)
                                    @foreach($receta->venta->detalles as $detalle)
                                        <span class="badge bg-success bg-opacity-10 text-success me-1">{{ $detalle->producto->nombre }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted">Sin venta asociada</span>
                                @endif
                            </td>
                            <td>
                                @if($receta->estado_validacion)
                                    <span class="badge bg-success bg-opacity-10 text-success">
                                        <i class="fas fa-check-circle me-1"></i>Aprobada
                                    </span>
                                @else
                                    <span class="badge bg-warning bg-opacity-10 text-warning">
                                        <i class="fas fa-clock me-1"></i>Pendiente
                                    </span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if(!$receta->estado_validacion && $receta->venta)
                                    <form action="{{ route('farmaceutico.recetas.aprobar', $receta->id_receta) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" style="border-radius: 8px;">
                                            <i class="fas fa-check me-1"></i>Aprobar
                                        </button>
                                    </form>
                                    <form action="{{ route('farmaceutico.recetas.rechazar', $receta->id_receta) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius: 8px;" onclick="return confirm('¿Rechazar esta receta?')">
                                            <i class="fas fa-times me-1"></i>Rechazar
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{ $recetas->links() }}
@endsection