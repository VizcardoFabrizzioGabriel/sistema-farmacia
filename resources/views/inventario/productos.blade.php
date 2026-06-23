@extends('layouts.app')

@section('title', 'Inventario de Productos - EDDUFARMA')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="color: #1f2937;">
            <i class="fas fa-boxes text-success me-2"></i>Inventario de Productos
        </h2>
        <p class="text-muted mb-0">Gestión completa del stock farmacéutico</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('almacen.lotes') }}" class="btn btn-outline-success" style="border-radius: 10px;">
            <i class="fas fa-list me-2"></i>Ver Lotes
        </a>
        <a href="{{ route('almacen.lotes') }}?ordenar=heapsort" class="btn btn-success" style="border-radius: 10px;">
            <i class="fas fa-sort-amount-down me-2"></i>Ordenar Heapsort
        </a>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card card-dashboard h-100" style="border-left: 4px solid #10b981;">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-success bg-opacity-10 text-success me-3">
                    <i class="fas fa-capsules"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Total Productos</h6>
                    <h3 class="fw-bold mb-0" style="color: #1f2937;">{{ $productos->total() ?? \App\Models\Producto::count() }}</h3>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card card-dashboard h-100" style="border-left: 4px solid #ef4444;">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-danger bg-opacity-10 text-danger me-3">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Stock Bajo</h6>
                    <h3 class="fw-bold mb-0" style="color: #1f2937;">{{ \App\Models\Producto::whereColumn('stock_actual', '<=', 'stock_minimo')->count() }}</h3>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card card-dashboard h-100" style="border-left: 4px solid #f59e0b;">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning me-3">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Por Vencer</h6>
                    <h3 class="fw-bold mb-0" style="color: #1f2937;">{{ \App\Models\Lote::where('estado', 'PorVencer')->count() }}</h3>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card card-dashboard h-100" style="border-left: 4px solid #3b82f6;">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Valor Inventario</h6>
                    <h3 class="fw-bold mb-0" style="color: #1f2937;">S/ {{ number_format(\App\Models\Producto::sum(\DB::raw('stock_actual * precio_venta')), 0) }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card card-dashboard">
    <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0" style="color: #1f2937;">
            <i class="fas fa-list text-success me-2"></i>Listado de Productos
        </h5>
        <input type="text" id="buscarInventario" class="form-control" placeholder="Buscar producto..." style="max-width: 250px; border-radius: 10px; border: 2px solid #e5e7eb;">
    </div>
    <div class="card-body px-4 pb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="tablaInventario">
                <thead style="background: #f0fdf4;">
                    <tr>
                        <th class="text-success fw-semibold">Código</th>
                        <th class="text-success fw-semibold">Producto</th>
                        <th class="text-success fw-semibold">Categoría</th>
                        <th class="text-success fw-semibold">Precio</th>
                        <th class="text-success fw-semibold">Stock</th>
                        <th class="text-success fw-semibold">Mínimo</th>
                        <th class="text-success fw-semibold">Estado</th>
                        <th class="text-success fw-semibold">Controlado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($productos ?? \App\Models\Producto::with('categoria')->paginate(20) as $producto)
                        <tr class="producto-fila" data-nombre="{{ strtolower($producto->nombre) }}">
                            <td class="fw-semibold text-muted">{{ $producto->codigo_barras }}</td>
                            <td class="fw-semibold">{{ $producto->nombre }}</td>
                            <td>
                                <span class="badge bg-success bg-opacity-10 text-success">
                                    {{ $producto->categoria->nombre }}
                                </span>
                            </td>
                            <td class="fw-bold text-success">S/ {{ number_format($producto->precio_venta, 2) }}</td>
                            <td>
                                <span class="badge {{ $producto->stock_actual <= $producto->stock_minimo ? 'bg-danger' : 'bg-success' }} bg-opacity-10 {{ $producto->stock_actual <= $producto->stock_minimo ? 'text-danger' : 'text-success' }}">
                                    {{ $producto->stock_actual }}
                                </span>
                            </td>
                            <td>{{ $producto->stock_minimo }}</td>
                            <td>
                                @if($producto->stock_actual == 0)
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary">Agotado</span>
                                @elseif($producto->stock_actual <= $producto->stock_minimo)
                                    <span class="badge bg-danger bg-opacity-10 text-danger">Crítico</span>
                                @elseif($producto->stock_actual <= $producto->stock_minimo * 2)
                                    <span class="badge bg-warning bg-opacity-10 text-warning">Bajo</span>
                                @else
                                    <span class="badge bg-success bg-opacity-10 text-success">Normal</span>
                                @endif
                            </td>
                            <td>
                                @if($producto->es_controlado)
                                    <span class="badge bg-warning bg-opacity-10 text-warning">
                                        <i class="fas fa-exclamation-triangle me-1"></i>Sí
                                    </span>
                                @else
                                    <span class="badge bg-success bg-opacity-10 text-success">No</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="fas fa-box-open fa-2x mb-2 opacity-25"></i>
                                <p>No hay productos registrados</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if(isset($productos) && method_exists($productos, 'links'))
            {{ $productos->links() }}
        @endif
    </div>
</div>

<div class="card card-dashboard mt-4">
    <div class="card-header bg-white border-0 pt-4 px-4">
        <h5 class="fw-bold mb-0" style="color: #1f2937;">
            <i class="fas fa-clipboard-list text-success me-2"></i>Sugerencias de Compra Automáticas
        </h5>
    </div>
    <div class="card-body px-4 pb-4">
        @php
            $sugerencias = (new \App\Services\InventarioService())->generarSugerenciaCompra();
        @endphp
        
        @if(count($sugerencias) > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead style="background: #fef2f2;">
                        <tr>
                            <th class="text-danger fw-semibold">Producto</th>
                            <th class="text-danger fw-semibold">Stock Actual</th>
                            <th class="text-danger fw-semibold">Stock Mínimo</th>
                            <th class="text-danger fw-semibold">Cantidad Sugerida</th>
                            <th class="text-danger fw-semibold">Proveedor</th>
                            <th class="text-danger fw-semibold">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sugerencias as $sugerencia)
                            <tr>
                                <td class="fw-semibold">{{ $sugerencia['nombre'] }}</td>
                                <td><span class="badge bg-danger bg-opacity-10 text-danger">{{ $sugerencia['stock_actual'] }}</span></td>
                                <td>{{ $sugerencia['stock_minimo'] }}</td>
                                <td class="fw-bold text-success">{{ $sugerencia['cantidad_sugerida'] }}</td>
                                <td>{{ $sugerencia['proveedor_principal'] }}</td>
                                <td>
                                    <a href="{{ route('almacen.ordenes') }}" class="btn btn-sm btn-success" style="border-radius: 8px;">
                                        <i class="fas fa-plus me-1"></i>Crear Orden
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center text-muted py-4">
                <i class="fas fa-check-circle text-success me-2"></i>
                No hay sugerencias de compra pendientes. El inventario está en niveles óptimos.
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
document.getElementById('buscarInventario').addEventListener('input', function() {
    const term = this.value.toLowerCase();
    document.querySelectorAll('.producto-fila').forEach(fila => {
        fila.style.display = fila.dataset.nombre.includes(term) ? '' : 'none';
    });
});
</script>
@endsection