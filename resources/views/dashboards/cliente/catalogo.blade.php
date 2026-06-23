@extends('layouts.app')

@section('title', 'Catálogo - EDDUFARMA')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="color: #1f2937;">
            <i class="fas fa-th-large text-success me-2"></i>Catálogo de Productos
        </h2>
        <p class="text-muted mb-0">Explora nuestros medicamentos y productos</p>
    </div>
    <input type="text" id="buscarCatalogo" class="form-control" placeholder="Buscar producto..." style="max-width: 300px; border-radius: 10px; border: 2px solid #e5e7eb;">
</div>

<div class="row g-4" id="gridCatalogo">
    @foreach($productos as $producto)
        <div class="col-md-4 col-lg-3 producto-catalogo" data-nombre="{{ strtolower($producto->nombre) }}">
            <div class="card card-dashboard h-100 text-center">
                <div class="card-body">
                    <div class="stat-icon bg-success bg-opacity-10 text-success mx-auto mb-3" style="width: 64px; height: 64px; font-size: 28px;">
                        <i class="fas fa-capsules"></i>
                    </div>
                    
                    <span class="badge {{ $producto->es_controlado ? 'bg-warning text-dark' : 'bg-success bg-opacity-10 text-success' }} mb-2">
                        {{ $producto->es_controlado ? 'Controlado' : 'Venta Libre' }}
                    </span>
                    
                    <h6 class="fw-bold mb-1" style="color: #1f2937;">{{ $producto->nombre }}</h6>
                    <p class="text-muted small mb-2">{{ $producto->categoria->nombre }}</p>
                    <p class="text-muted small mb-3">{{ Str::limit($producto->descripcion, 60) }}</p>
                    
                    <div class="d-flex justify-content-between align-items-center mt-auto">
                        <h5 class="text-success fw-bold mb-0">S/ {{ number_format($producto->precio_venta, 2) }}</h5>
                        <span class="badge {{ $producto->stock_actual > 0 ? 'bg-success' : 'bg-secondary' }} bg-opacity-10 {{ $producto->stock_actual > 0 ? 'text-success' : 'text-secondary' }}">
                            {{ $producto->stock_actual > 0 ? 'Disponible' : 'Agotado' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

{{ $productos->links() }}
@endsection

@section('scripts')
<script>
document.getElementById('buscarCatalogo').addEventListener('input', function() {
    const term = this.value.toLowerCase();
    document.querySelectorAll('.producto-catalogo').forEach(el => {
        el.style.display = el.dataset.nombre.includes(term) ? '' : 'none';
    });
});
</script>
@endsection