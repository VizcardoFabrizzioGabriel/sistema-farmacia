@extends('layouts.app')

@section('title', 'Dispensar Medicamentos - EDDUFARMA')

@section('styles')
<style>
    .producto-dispensar {
        border: 2px solid #e5e7eb;
        border-radius: 16px;
        padding: 20px;
        cursor: pointer;
        transition: all 0.3s;
        background: white;
        height: 100%;
    }

    .producto-dispensar:hover {
        border-color: #10b981;
        box-shadow: 0 8px 24px rgba(16, 185, 129, 0.15);
        transform: translateY(-4px);
    }

    .producto-dispensar.controlado {
        border-color: #f59e0b;
    }

    .producto-dispensar.controlado:hover {
        border-color: #f59e0b;
        box-shadow: 0 8px 24px rgba(245, 158, 11, 0.15);
    }

    .carrito-lateral {
        background: white;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border: 1px solid #e5e7eb;
        position: sticky;
        top: 20px;
    }

    .item-carrito-dispensar {
        background: #f0fdf4;
        border-radius: 12px;
        padding: 14px;
        margin-bottom: 10px;
    }

    .total-dispensar {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border-radius: 16px;
        padding: 20px;
        color: white;
    }

    .busqueda-dispensar {
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 14px 20px;
        font-size: 16px;
    }

    .busqueda-dispensar:focus {
        border-color: #10b981;
        box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
    }

    .categoria-chip {
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 20px;
        padding: 8px 18px;
        cursor: pointer;
        transition: all 0.3s;
        white-space: nowrap;
        font-size: 14px;
        font-weight: 500;
    }

    .categoria-chip:hover,
    .categoria-chip.activa {
        background: #10b981;
        color: white;
        border-color: #10b981;
    }

    .cantidad-badge {
        background: #10b981;
        color: white;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: bold;
    }
</style>
@endsection

@section('content')
<div class="row">
    {{-- Panel Izquierdo: Productos --}}
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold" style="color: #1f2937;">
                    <i class="fas fa-hand-holding-medical text-success me-2"></i>Dispensar Medicamentos
                </h3>
                <p class="text-muted mb-0">Seleccione los productos para la venta</p>
            </div>
            <input type="text" id="buscarDispensar" class="form-control busqueda-dispensar"
                   placeholder="Buscar por nombre o código..." style="max-width: 320px;">
        </div>

        {{-- Filtros de categoría --}}
        <div class="d-flex gap-2 mb-4 overflow-auto pb-2">
            <span class="categoria-chip activa" onclick="filtrarPorCategoria('')">Todos</span>
            @foreach(\App\Models\Categoria::all() as $cat)
                <span class="categoria-chip" onclick="filtrarPorCategoria('{{ strtolower($cat->nombre) }}')">{{ $cat->nombre }}</span>
            @endforeach
        </div>

        {{-- Grid de productos --}}
        <div class="row g-3" id="gridProductos">
            @foreach($productos as $producto)
                <div class="col-md-4 col-lg-3 producto-grid-item" data-nombre="{{ strtolower($producto->nombre) }}" data-categoria="{{ strtolower($producto->categoria->nombre) }}">
                    <div class="producto-dispensar {{ $producto->es_controlado || $producto->requiere_receta ? 'controlado' : '' }}"
                         onclick="seleccionarProducto({{ $producto->id_producto }}, '{{ $producto->nombre }}', {{ $producto->precio_venta }}, {{ $producto->stock_actual }}, {{ $producto->es_controlado || $producto->requiere_receta ? 'true' : 'false' }})">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge {{ $producto->es_controlado || $producto->requiere_receta ? 'bg-warning text-dark' : 'bg-success bg-opacity-10 text-success' }} small">
                                {{ $producto->es_controlado ? 'Controlado' : 'Libre' }}
                            </span>
                            <span class="text-muted small">Stock: {{ $producto->stock_actual }}</span>
                        </div>
                        <h6 class="fw-bold mb-1" style="color: #1f2937; font-size: 14px;">{{ $producto->nombre }}</h6>
                        <p class="text-muted small mb-2" style="font-size: 12px;">{{ $producto->categoria->nombre }}</p>
                        <div class="d-flex justify-content-between align-items-center mt-auto">
                            <h5 class="text-success fw-bold mb-0" style="font-size: 18px;">S/ {{ number_format($producto->precio_venta, 2) }}</h5>
                            <div class="cantidad-badge d-none" id="badge-{{ $producto->id_producto }}">0</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Panel Derecho: Carrito --}}
    <div class="col-lg-4">
        <div class="carrito-lateral p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0" style="color: #1f2937;">
                    <i class="fas fa-shopping-basket text-success me-2"></i>Carrito
                </h5>
                <span class="badge bg-success bg-opacity-10 text-success" id="cantidadItems">0 items</span>
            </div>

            <div id="itemsCarrito" style="max-height: 320px; overflow-y: auto;">
                <div class="text-center text-muted py-4">
                    <i class="fas fa-shopping-basket fa-2x mb-2 opacity-25"></i>
                    <p class="small">Agregue productos al carrito</p>
                </div>
            </div>

            <hr class="my-3">

            <div class="total-dispensar mb-3">
                <div class="d-flex justify-content-between mb-1">
                    <span>Subtotal</span>
                    <span id="subtotalDispensar">S/ 0.00</span>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <span>IGV (18%)</span>
                    <span id="igvDispensar">S/ 0.00</span>
                </div>
                <div class="d-flex justify-content-between mt-2 pt-2" style="border-top: 1px solid rgba(255,255,255,0.3);">
                    <span class="fw-bold fs-5">TOTAL</span>
                    <span class="fw-bold fs-5" id="totalDispensar">S/ 0.00</span>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold small">Método de Pago</label>
                <select id="metodoPagoDispensar" class="form-select" style="border-radius: 10px; border: 2px solid #e5e7eb;">
                    <option value="Efectivo">Efectivo</option>
                    <option value="Tarjeta">Tarjeta de Crédito/Débito</option>
                    <option value="Stripe">Stripe (Pago Online)</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold small">Tipo Comprobante</label>
                <select id="tipoComprobanteDispensar" class="form-select" style="border-radius: 10px; border: 2px solid #e5e7eb;">
                    <option value="Boleta">Boleta de Venta</option>
                    <option value="Factura">Factura Electrónica</option>
                </select>
            </div>

            <button class="btn btn-success w-100 py-3 fw-bold mb-2" onclick="procesarDispensar()"
                    style="border-radius: 12px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none;">
                <i class="fas fa-check-circle me-2"></i>Procesar Venta
            </button>

            <div class="row g-2">
                <div class="col-6">
                    <a href="{{ route('tecnico.carrito') }}" class="btn btn-outline-success w-100" style="border-radius: 10px;">
                        <i class="fas fa-expand me-2"></i>Ver Carrito
                    </a>
                </div>
                <div class="col-6">
                    <button class="btn btn-outline-danger w-100" onclick="vaciarCarritoDispensar()" style="border-radius: 10px;">
                        <i class="fas fa-trash-alt me-2"></i>Vaciar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Cantidad --}}
<div class="modal fade" id="modalCantidad" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; border: none;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" style="color: #1f2937;">
                    <i class="fas fa-plus-circle text-success me-2"></i>Agregar al Carrito
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4">
                <h6 class="fw-bold mb-1" id="nombreProductoModal" style="color: #1f2937;"></h6>
                <p class="text-success fw-bold mb-3" id="precioProductoModal"></p>

                <label class="form-label fw-semibold small">Cantidad</label>
                <div class="d-flex align-items-center gap-3 mb-3">
                    <button class="btn btn-outline-secondary rounded-circle" onclick="ajustarCantidadModal(-1)" style="width: 40px; height: 40px;">
                        <i class="fas fa-minus"></i>
                    </button>
                    <input type="number" id="cantidadModal" class="form-control text-center fw-bold" value="1" min="1" style="border-radius: 10px; border: 2px solid #e5e7eb; max-width: 100px;">
                    <button class="btn btn-outline-success rounded-circle" onclick="ajustarCantidadModal(1)" style="width: 40px; height: 40px;">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
                <p class="text-muted small mb-0">Stock disponible: <span id="stockModal" class="fw-bold text-success"></span></p>
            </div>
            <div class="modal-footer border-0 px-4 pb-4">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 10px;">Cancelar</button>
                <button type="button" class="btn btn-success" onclick="confirmarAgregar()" style="border-radius: 10px;">
                    <i class="fas fa-plus me-2"></i>Agregar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Receta --}}
<div class="modal fade" id="modalRecetaDispensar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; border: none;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" style="color: #1f2937;">
                    <i class="fas fa-file-prescription text-warning me-2"></i>Receta Médica Requerida
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4">
                <div class="alert border-0 mb-3" style="background: #fffbeb; border-radius: 12px;">
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                    Este medicamento requiere receta médica válida.
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Código de Receta</label>
                    <input type="text" id="codigoRecetaDispensar" class="form-control" style="border-radius: 10px; border: 2px solid #e5e7eb;">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small">DNI del Médico</label>
                    <input type="text" id="dniMedicoDispensar" class="form-control" style="border-radius: 10px; border: 2px solid #e5e7eb;">
                </div>
            </div>
            <div class="modal-footer border-0 px-4 pb-4">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 10px;">Cancelar</button>
                <button type="button" class="btn btn-success" onclick="validarRecetaDispensar()" style="border-radius: 10px;">
                    <i class="fas fa-check me-2"></i>Validar y Agregar
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let productoSeleccionado = null;
let cantidadSeleccionada = 1;

function seleccionarProducto(id, nombre, precio, stock, controlado) {
    if (stock <= 0) {
        alert('Producto sin stock disponible');
        return;
    }

    productoSeleccionado = {id, nombre, precio, stock};

    if (controlado) {
        document.getElementById('nombreProductoModal').textContent = nombre;
        document.getElementById('precioProductoModal').textContent = 'S/ ' + precio.toFixed(2);
        document.getElementById('stockModal').textContent = stock;
        document.getElementById('cantidadModal').value = 1;
        cantidadSeleccionada = 1;
        new bootstrap.Modal(document.getElementById('modalRecetaDispensar')).show();
    } else {
        document.getElementById('nombreProductoModal').textContent = nombre;
        document.getElementById('precioProductoModal').textContent = 'S/ ' + precio.toFixed(2);
        document.getElementById('stockModal').textContent = stock;
        document.getElementById('cantidadModal').value = 1;
        cantidadSeleccionada = 1;
        new bootstrap.Modal(document.getElementById('modalCantidad')).show();
    }
}

function ajustarCantidadModal(delta) {
    const input = document.getElementById('cantidadModal');
    let nueva = parseInt(input.value) + delta;
    if (nueva < 1) nueva = 1;
    if (nueva > productoSeleccionado.stock) nueva = productoSeleccionado.stock;
    input.value = nueva;
    cantidadSeleccionada = nueva;
}

function confirmarAgregar() {
    cantidadSeleccionada = parseInt(document.getElementById('cantidadModal').value);

    fetch('/tecnico/carrito/agregar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.csrfToken
        },
        body: JSON.stringify({
            id_producto: productoSeleccionado.id,
            cantidad: cantidadSeleccionada
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('modalCantidad')).hide();
            actualizarCarritoDispensar(data.carrito);
            actualizarBadge(productoSeleccionado.id, cantidadSeleccionada);
        } else {
            alert(data.message);
        }
    });
}

function validarRecetaDispensar() {
    const codigo = document.getElementById('codigoRecetaDispensar').value;
    const dni = document.getElementById('dniMedicoDispensar').value;

    if (!codigo || !dni) {
        alert('Ingrese todos los datos de la receta');
        return;
    }

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
            bootstrap.Modal.getInstance(document.getElementById('modalRecetaDispensar')).hide();
            new bootstrap.Modal(document.getElementById('modalCantidad')).show();
        } else {
            alert(data.message);
        }
    });
}

function actualizarCarritoDispensar(carrito) {
    const container = document.getElementById('itemsCarrito');
    const cantidadBadge = document.getElementById('cantidadItems');

    cantidadBadge.textContent = carrito.cantidad_items + ' items';

    if (carrito.cantidad_items === 0) {
        container.innerHTML = `
            <div class="text-center text-muted py-4">
                <i class="fas fa-shopping-basket fa-2x mb-2 opacity-25"></i>
                <p class="small">Agregue productos al carrito</p>
            </div>`;
    } else {
        container.innerHTML = '';
        Object.values(carrito.items).forEach(item => {
            container.innerHTML += `
                <div class="item-carrito-dispensar">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="fw-bold mb-1" style="color: #1f2937; font-size: 14px;">${item.nombre}</h6>
                            <small class="text-muted">${item.cantidad} x S/ ${item.precio_unitario.toFixed(2)}</small>
                        </div>
                        <button class="btn btn-sm btn-outline-danger rounded-circle" onclick="quitarItemDispensar(${item.id_producto})" style="width: 24px; height: 24px; padding: 0;">
                            <i class="fas fa-times" style="font-size: 10px;"></i>
                        </button>
                    </div>
                    <div class="text-end fw-bold text-success mt-1" style="font-size: 14px;">S/ ${item.subtotal.toFixed(2)}</div>
                </div>`;
        });
    }

    document.getElementById('subtotalDispensar').textContent = 'S/ ' + carrito.subtotal.toFixed(2);
    document.getElementById('igvDispensar').textContent = 'S/ ' + carrito.impuestos.toFixed(2);
    document.getElementById('totalDispensar').textContent = 'S/ ' + carrito.total.toFixed(2);
}

function actualizarBadge(idProducto, cantidad) {
    const badge = document.getElementById('badge-' + idProducto);
    if (badge) {
        badge.textContent = cantidad;
        badge.classList.remove('d-none');
    }
}

function quitarItemDispensar(idProducto) {
    fetch('/tecnico/carrito/quitar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.csrfToken
        },
        body: JSON.stringify({id_producto: idProducto})
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            actualizarCarritoDispensar(data.carrito);
            const badge = document.getElementById('badge-' + idProducto);
            if (badge) {
                badge.textContent = '0';
                badge.classList.add('d-none');
            }
        }
    });
}

function vaciarCarritoDispensar() {
    if (!confirm('¿Vaciar carrito?')) return;

    fetch('/tecnico/carrito', {
        method: 'DELETE',
        headers: {'X-CSRF-TOKEN': window.csrfToken}
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            actualizarCarritoDispensar({items: {}, subtotal: 0, impuestos: 0, total: 0, cantidad_items: 0});
            document.querySelectorAll('.cantidad-badge').forEach(b => {
                b.textContent = '0';
                b.classList.add('d-none');
            });
        }
    });
}

function procesarDispensar() {
    const metodo = document.getElementById('metodoPagoDispensar').value;
    const tipo = document.getElementById('tipoComprobanteDispensar').value;

    fetch('/tecnico/venta', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.csrfToken
        },
        body: JSON.stringify({
            metodo_pago: metodo,
            tipo_comprobante: tipo
        })
    })
    .then(r => {
        if (r.redirected) {
            window.location.href = r.url;
            return;
        }
        return r.json();
    });
}

function filtrarPorCategoria(categoria) {
    document.querySelectorAll('.categoria-chip').forEach(el => el.classList.remove('activa'));
    event.target.classList.add('activa');

    document.querySelectorAll('.producto-grid-item').forEach(el => {
        if (!categoria || el.dataset.categoria === categoria) {
            el.style.display = '';
        } else {
            el.style.display = 'none';
        }
    });
}

// Búsqueda
document.getElementById('buscarDispensar').addEventListener('input', function() {
    const term = this.value.toLowerCase();
    document.querySelectorAll('.producto-grid-item').forEach(el => {
        el.style.display = el.dataset.nombre.includes(term) ? '' : 'none';
    });
});

// Cargar carrito al iniciar
fetch('/tecnico/carrito')
    .then(r => r.json())
    .then(data => {
        if (data.success) actualizarCarritoDispensar(data.carrito);
    });
</script>
@endsection