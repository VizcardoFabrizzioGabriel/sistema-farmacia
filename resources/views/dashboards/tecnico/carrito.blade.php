@extends('layouts.app')

@section('title', 'Carrito de Ventas - EDDUFARMA')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1" style="color: #1f2937;">
                    <i class="fas fa-shopping-cart text-success me-2"></i>Carrito de Ventas
                </h2>
                <p class="text-muted mb-0">Revisa los productos antes de procesar</p>
            </div>
            <a href="{{ route('tecnico.dispensar') }}" class="btn btn-outline-success" style="border-radius: 10px;">
                <i class="fas fa-arrow-left me-2"></i>Seguir Comprando
            </a>
        </div>

        <div class="card card-dashboard mb-4">
            <div class="card-body p-0">
                <div id="carritoContainer">
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-cart-plus fa-3x mb-3 opacity-25"></i>
                        <p>El carrito está vacío</p>
                        <a href="{{ route('tecnico.dispensar') }}" class="btn btn-success" style="border-radius: 10px;">
                            <i class="fas fa-cash-register me-2"></i>Ir al Punto de Venta
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div id="resumenPago" class="d-none">
            <div class="card card-dashboard" style="border-left: 4px solid #10b981;">
                <div class="card-body">
                    <h5 class="fw-bold mb-3" style="color: #1f2937;">
                        <i class="fas fa-credit-card text-success me-2"></i>Resumen de Pago
                    </h5>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Método de Pago</label>
                            <select id="metodoPagoCarrito" class="form-select" style="border-radius: 10px; border: 2px solid #e5e7eb;">
                                <option value="Efectivo">Efectivo</option>
                                <option value="Tarjeta">Tarjeta</option>
                                <option value="Stripe">Stripe (Online)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Tipo Comprobante</label>
                            <select id="tipoComprobanteCarrito" class="form-select" style="border-radius: 10px; border: 2px solid #e5e7eb;">
                                <option value="Boleta">Boleta</option>
                                <option value="Factura">Factura</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button class="btn btn-success py-3 fw-bold" onclick="procesarVentaCarrito()" style="border-radius: 12px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none;">
                            <i class="fas fa-check-circle me-2"></i>Confirmar y Procesar Venta
                        </button>
                        <button class="btn btn-outline-danger" onclick="vaciarCarritoCompleto()" style="border-radius: 10px;">
                            <i class="fas fa-trash-alt me-2"></i>Vaciar Carrito
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function cargarCarrito() {
    fetch('/tecnico/carrito')
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                mostrarCarrito(data.carrito);
            }
        });
}

function mostrarCarrito(carrito) {
    const container = document.getElementById('carritoContainer');
    const resumen = document.getElementById('resumenPago');

    if (carrito.cantidad_items === 0) {
        container.innerHTML = `
            <div class="text-center py-5 text-muted">
                <i class="fas fa-cart-plus fa-3x mb-3 opacity-25"></i>
                <p>El carrito está vacío</p>
                <a href="{{ route('tecnico.dispensar') }}" class="btn btn-success" style="border-radius: 10px;">
                    <i class="fas fa-cash-register me-2"></i>Ir al Punto de Venta
                </a>
            </div>`;
        resumen.classList.add('d-none');
        return;
    }

    resumen.classList.remove('d-none');

    let html = '<div class="table-responsive"><table class="table table-hover align-middle mb-0">';
    html += '<thead style="background: #f0fdf4;"><tr>';
    html += '<th class="text-success">Producto</th>';
    html += '<th class="text-success text-center">Cantidad</th>';
    html += '<th class="text-success text-end">P. Unit.</th>';
    html += '<th class="text-success text-end">Subtotal</th>';
    html += '<th class="text-success text-center"></th>';
    html += '</tr></thead><tbody>';

    Object.values(carrito.items).forEach(item => {
        html += `<tr>
            <td class="fw-semibold">${item.nombre}</td>
            <td class="text-center">
                <div class="d-flex align-items-center justify-content-center gap-2">
                    <button class="btn btn-sm btn-outline-secondary rounded-circle" onclick="actualizarCantidadCarrito(${item.id_producto}, ${item.cantidad - 1})" style="width: 28px; height: 28px; padding: 0;">
                        <i class="fas fa-minus" style="font-size: 10px;"></i>
                    </button>
                    <span class="fw-semibold">${item.cantidad}</span>
                    <button class="btn btn-sm btn-outline-success rounded-circle" onclick="actualizarCantidadCarrito(${item.id_producto}, ${item.cantidad + 1})" style="width: 28px; height: 28px; padding: 0;">
                        <i class="fas fa-plus" style="font-size: 10px;"></i>
                    </button>
                </div>
            </td>
            <td class="text-end">S/ ${item.precio_unitario.toFixed(2)}</td>
            <td class="text-end fw-bold text-success">S/ ${item.subtotal.toFixed(2)}</td>
            <td class="text-center">
                <button class="btn btn-sm btn-outline-danger rounded-circle" onclick="quitarDelCarrito(${item.id_producto})" style="width: 28px; height: 28px; padding: 0;">
                    <i class="fas fa-times" style="font-size: 10px;"></i>
                </button>
            </td>
        </tr>`;
    });

    html += `</tbody><tfoot style="background: #f0fdf4;">
        <tr>
            <td colspan="3" class="fw-bold text-end">Subtotal:</td>
            <td class="fw-bold text-end">S/ ${carrito.subtotal.toFixed(2)}</td>
            <td></td>
        </tr>
        <tr>
            <td colspan="3" class="fw-bold text-end">IGV (18%):</td>
            <td class="fw-bold text-end">S/ ${carrito.impuestos.toFixed(2)}</td>
            <td></td>
        </tr>
        <tr style="border-top: 2px solid #10b981;">
            <td colspan="3" class="fw-bold fs-5 text-end" style="color: #1f2937;">TOTAL:</td>
            <td class="fw-bold fs-5 text-success text-end">S/ ${carrito.total.toFixed(2)}</td>
            <td></td>
        </tr>
    </tfoot></table></div>`;

    container.innerHTML = html;
}

function actualizarCantidadCarrito(idProducto, nuevaCantidad) {
    if (nuevaCantidad < 1) {
        quitarDelCarrito(idProducto);
        return;
    }

    fetch('/tecnico/carrito/agregar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.csrfToken
        },
        body: JSON.stringify({id_producto: idProducto, cantidad: nuevaCantidad})
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) mostrarCarrito(data.carrito);
    });
}

function quitarDelCarrito(idProducto) {
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
        if (data.success) mostrarCarrito(data.carrito);
    });
}

function vaciarCarritoCompleto() {
    if (!confirm('¿Está seguro de vaciar el carrito?')) return;

    fetch('/tecnico/carrito', {
        method: 'DELETE',
        headers: {'X-CSRF-TOKEN': window.csrfToken}
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) mostrarCarrito({items: {}, subtotal: 0, impuestos: 0, total: 0, cantidad_items: 0});
    });
}

function procesarVentaCarrito() {
    const metodo = document.getElementById('metodoPagoCarrito').value;
    const tipo = document.getElementById('tipoComprobanteCarrito').value;

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
    })
    .then(data => {
        if (data && data.error) {
            alert(data.error);
        }
    });
}

// Cargar al iniciar
cargarCarrito();
</script>
@endsection