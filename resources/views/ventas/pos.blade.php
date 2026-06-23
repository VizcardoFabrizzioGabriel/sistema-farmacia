@extends('layouts.app')

@section('title', 'Punto de Venta - EDDUFARMA')

@section('styles')
<style>
    .pos-producto {
        border: 2px solid #e5e7eb;
        border-radius: 16px;
        padding: 16px;
        cursor: pointer;
        transition: all 0.3s;
        background: white;
        height: 100%;
    }
    
    .pos-producto:hover {
        border-color: #10b981;
        box-shadow: 0 8px 24px rgba(16, 185, 129, 0.15);
        transform: translateY(-4px);
    }
    
    .pos-producto.controlado {
        border-color: #f59e0b;
    }
    
    .pos-producto.controlado:hover {
        border-color: #f59e0b;
        box-shadow: 0 8px 24px rgba(245, 158, 11, 0.15);
    }
    
    .pos-carrito {
        background: white;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border: 1px solid #e5e7eb;
        position: sticky;
        top: 20px;
    }
    
    .pos-item {
        background: #f0fdf4;
        border-radius: 12px;
        padding: 12px;
        margin-bottom: 8px;
    }
    
    .pos-total {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border-radius: 16px;
        padding: 20px;
        color: white;
    }
    
    .pos-teclado {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
        margin-top: 12px;
    }
    
    .pos-tecla {
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 16px;
        font-weight: 600;
        font-size: 18px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .pos-tecla:hover {
        border-color: #10b981;
        background: #f0fdf4;
    }
    
    .pos-tecla.success {
        background: #10b981;
        color: white;
        border-color: #10b981;
    }
    
    .pos-tecla.danger {
        background: #ef4444;
        color: white;
        border-color: #ef4444;
    }
    
    .categoria-filtro {
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        padding: 8px 16px;
        cursor: pointer;
        transition: all 0.3s;
        white-space: nowrap;
    }
    
    .categoria-filtro:hover,
    .categoria-filtro.active {
        background: #10b981;
        color: white;
        border-color: #10b981;
    }
    
    .barra-busqueda {
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 14px 20px;
        font-size: 16px;
    }
    
    .barra-busqueda:focus {
        border-color: #10b981;
        box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
    }
</style>
@endsection

@section('content')
<div class="row">
    {{-- Panel Izquierdo: Productos --}}
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold" style="color: #1f2937;">
                <i class="fas fa-cash-register text-success me-2"></i>Punto de Venta
            </h3>
            <input type="text" id="buscarPOS" class="form-control barra-busqueda" 
                   placeholder="Buscar medicamento..." style="max-width: 300px;">
        </div>
        
        {{-- Filtros de categoría --}}
        <div class="d-flex gap-2 mb-4 overflow-auto pb-2">
            <span class="categoria-filtro active" onclick="filtrarCategoria('')">Todos</span>
            @foreach(\App\Models\Categoria::all() as $cat)
                <span class="categoria-filtro" onclick="filtrarCategoria('{{ strtolower($cat->nombre) }}')">{{ $cat->nombre }}</span>
            @endforeach
        </div>
        
        <div class="row g-3" id="productosPOS">
            @foreach($productos as $producto)
                <div class="col-md-4 col-lg-3 pos-item-grid" data-nombre="{{ strtolower($producto->nombre) }}" data-categoria="{{ strtolower($producto->categoria->nombre) }}">
                    <div class="pos-producto {{ $producto->es_controlado || $producto->requiere_receta ? 'controlado' : '' }}" onclick="agregarProductoPOS({{ $producto->id_producto }}, '{{ $producto->nombre }}', {{ $producto->precio_venta }}, {{ $producto->stock_actual }}, {{ $producto->es_controlado || $producto->requiere_receta ? 'true' : 'false' }})">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge {{ $producto->es_controlado || $producto->requiere_receta ? 'bg-warning text-dark' : 'bg-success bg-opacity-10 text-success' }} small">
                                {{ $producto->es_controlado ? 'Controlado' : 'Libre' }}
                            </span>
                            <span class="text-muted small">Stock: {{ $producto->stock_actual }}</span>
                        </div>
                        <h6 class="fw-bold mb-1" style="color: #1f2937; font-size: 14px;">{{ $producto->nombre }}</h6>
                        <p class="text-muted small mb-2" style="font-size: 12px;">{{ $producto->categoria->nombre }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="text-success fw-bold mb-0" style="font-size: 18px;">S/ {{ number_format($producto->precio_venta, 2) }}</h5>
                            <i class="fas fa-plus-circle text-success" style="font-size: 20px;"></i>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    
    {{-- Panel Derecho: Carrito y Teclado --}}
    <div class="col-lg-4">
        <div class="pos-carrito p-4">
            <h5 class="fw-bold mb-3" style="color: #1f2937;">
                <i class="fas fa-shopping-cart text-success me-2"></i>Carrito
            </h5>
            
            <div id="carritoPOS" style="max-height: 250px; overflow-y: auto;">
                <div class="text-center text-muted py-4">
                    <i class="fas fa-cart-plus fa-3x mb-2 opacity-25"></i>
                    <p class="small">Carrito vacío</p>
                </div>
            </div>
            
            <div class="pos-total mt-3">
                <div class="d-flex justify-content-between mb-1">
                    <span>Subtotal</span>
                    <span id="posSubtotal">S/ 0.00</span>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <span>IGV</span>
                    <span id="posIGV">S/ 0.00</span>
                </div>
                <div class="d-flex justify-content-between mt-2 pt-2" style="border-top: 1px solid rgba(255,255,255,0.3);">
                    <span class="fw-bold fs-5">TOTAL</span>
                    <span class="fw-bold fs-5" id="posTotal">S/ 0.00</span>
                </div>
            </div>
            
            <div class="pos-teclado">
                <div class="pos-tecla" onclick="cambiarCantidad(1)">1</div>
                <div class="pos-tecla" onclick="cambiarCantidad(2)">2</div>
                <div class="pos-tecla" onclick="cambiarCantidad(3)">3</div>
                <div class="pos-tecla" onclick="cambiarCantidad(4)">4</div>
                <div class="pos-tecla" onclick="cambiarCantidad(5)">5</div>
                <div class="pos-tecla" onclick="cambiarCantidad(6)">6</div>
                <div class="pos-tecla" onclick="cambiarCantidad(7)">7</div>
                <div class="pos-tecla" onclick="cambiarCantidad(8)">8</div>
                <div class="pos-tecla" onclick="cambiarCantidad(9)">9</div>
                <div class="pos-tecla danger" onclick="limpiarCantidad()"><i class="fas fa-times"></i></div>
                <div class="pos-tecla" onclick="cambiarCantidad(0)">0</div>
                <div class="pos-tecla success" onclick="confirmarCantidad()"><i class="fas fa-check"></i></div>
            </div>
            
            <button class="btn btn-success w-100 mt-3 py-3 fw-bold" onclick="procesarVentaPOS()" 
                    style="border-radius: 12px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none;">
                <i class="fas fa-check-circle me-2"></i>Procesar Venta
            </button>
            
            <button class="btn btn-outline-secondary w-100 mt-2" onclick="vaciarCarritoPOS()" style="border-radius: 10px;">
                <i class="fas fa-trash-alt me-2"></i>Vaciar
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let carritoPOS = {};
let cantidadTemporal = 1;
let productoTemporal = null;

function cambiarCantidad(num) {
    cantidadTemporal = parseInt(num);
    // Visual feedback
    document.querySelectorAll('.pos-tecla').forEach(t => t.style.transform = 'scale(1)');
    event.target.style.transform = 'scale(0.95)';
    setTimeout(() => event.target.style.transform = 'scale(1)', 100);
}

function limpiarCantidad() {
    cantidadTemporal = 1;
}

function confirmarCantidad() {
    if (productoTemporal) {
        agregarAlCarritoPOS(productoTemporal.id, productoTemporal.nombre, productoTemporal.precio, productoTemporal.stock, productoTemporal.controlado);
        productoTemporal = null;
    }
}

function agregarProductoPOS(id, nombre, precio, stock, controlado) {
    if (controlado) {
        // Para controlados, pedir receta primero
        const codigo = prompt('Ingrese código de receta:');
        const dni = prompt('Ingrese DNI del médico:');
        if (!codigo || !dni) return;
        
        fetch('/api/recetas/validar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken
            },
            body: JSON.stringify({codigo_receta: codigo, dni_medico: dni})
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                productoTemporal = {id, nombre, precio, stock, controlado: false};
                agregarAlCarritoPOS(id, nombre, precio, stock, false);
            } else {
                alert(data.message);
            }
        });
        return;
    }
    
    productoTemporal = {id, nombre, precio, stock, controlado};
    agregarAlCarritoPOS(id, nombre, precio, stock, controlado);
}

function agregarAlCarritoPOS(id, nombre, precio, stock, controlado) {
    if (stock < cantidadTemporal) {
        alert('Stock insuficiente');
        return;
    }
    
    fetch('/tecnico/carrito/agregar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.csrfToken
        },
        body: JSON.stringify({id_producto: id, cantidad: cantidadTemporal})
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) actualizarCarritoPOS(data.carrito);
    });
    
    cantidadTemporal = 1;
    productoTemporal = null;
}

function actualizarCarritoPOS(data) {
    carritoPOS = data;
    const container = document.getElementById('carritoPOS');
    
    if (data.cantidad_items === 0) {
        container.innerHTML = `
            <div class="text-center text-muted py-4">
                <i class="fas fa-cart-plus fa-3x mb-2 opacity-25"></i>
                <p class="small">Carrito vacío</p>
            </div>`;
    } else {
        container.innerHTML = '';
        Object.values(data.items).forEach(item => {
            container.innerHTML += `
                <div class="pos-item">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="fw-bold mb-1" style="color: #1f2937; font-size: 14px;">${item.nombre}</h6>
                            <small class="text-muted">${item.cantidad} x S/ ${item.precio_unitario.toFixed(2)}</small>
                        </div>
                        <button class="btn btn-sm btn-outline-danger rounded-circle" onclick="quitarDelCarritoPOS(${item.id_producto})" style="width: 28px; height: 28px; padding: 0;">
                            <i class="fas fa-times" style="font-size: 10px;"></i>
                        </button>
                    </div>
                    <div class="text-end fw-bold text-success mt-1" style="font-size: 14px;">S/ ${item.subtotal.toFixed(2)}</div>
                </div>`;
        });
    }
    
    document.getElementById('posSubtotal').textContent = 'S/ ' + data.subtotal.toFixed(2);
    document.getElementById('posIGV').textContent = 'S/ ' + data.impuestos.toFixed(2);
    document.getElementById('posTotal').textContent = 'S/ ' + data.total.toFixed(2);
}

function quitarDelCarritoPOS(id) {
    fetch('/tecnico/carrito/quitar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.csrfToken
        },
        body: JSON.stringify({id_producto: id})
    })
    .then(r => r.json())
    .then(data => actualizarCarritoPOS(data.carrito));
}

function vaciarCarritoPOS() {
    fetch('/tecnico/carrito', {
        method: 'DELETE',
        headers: {'X-CSRF-TOKEN': window.csrfToken}
    })
    .then(r => r.json())
    .then(data => actualizarCarritoPOS({items: {}, subtotal: 0, impuestos: 0, total: 0, cantidad_items: 0}));
}

function procesarVentaPOS() {
    if (carritoPOS.cantidad_items === 0) {
        alert('El carrito está vacío');
        return;
    }
    
    fetch('/tecnico/venta', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.csrfToken
        },
        body: JSON.stringify({
            metodo_pago: 'Efectivo',
            tipo_comprobante: 'Boleta'
        })
    })
    .then(r => {
        if (r.redirected) window.location.href = r.url;
        return r.json();
    });
}

function filtrarCategoria(categoria) {
    document.querySelectorAll('.categoria-filtro').forEach(el => el.classList.remove('active'));
    event.target.classList.add('active');
    
    document.querySelectorAll('.pos-item-grid').forEach(el => {
        if (!categoria || el.dataset.categoria === categoria) {
            el.style.display = '';
        } else {
            el.style.display = 'none';
        }
    });
}

// Búsqueda
document.getElementById('buscarPOS').addEventListener('input', function() {
    const term = this.value.toLowerCase();
    document.querySelectorAll('.pos-item-grid').forEach(el => {
        el.style.display = el.dataset.nombre.includes(term) ? '' : 'none';
    });
});

// Cargar carrito
fetch('/tecnico/carrito')
    .then(r => r.json())
    .then(data => actualizarCarritoPOS(data.carrito));
</script>
@endsection