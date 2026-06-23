@extends('layouts.app')

@section('title', 'Mapa de Proveedores - EDDUFARMA')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="color: #1f2937;">
            <i class="fas fa-map-marked-alt text-success me-2"></i>Mapa de Proveedores
        </h2>
        <p class="text-muted mb-0">Ubicación de proveedores y sede EDDUFARMA</p>
    </div>
    <a href="{{ route('almacen.dashboard') }}" class="btn btn-outline-success" style="border-radius: 10px;">
        <i class="fas fa-arrow-left me-2"></i>Volver al Dashboard
    </a>
</div>

<div class="card card-dashboard">
    <div class="card-body p-0">
        <div id="map"></div>
    </div>
</div>

<div class="row g-4 mt-2">
    <div class="col-md-6">
        <div class="card card-dashboard">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0" style="color: #1f2937;">
                    <i class="fas fa-building text-success me-2"></i>Sede EDDUFARMA
                </h5>
            </div>
            <div class="card-body px-4 pb-4">
                <p class="mb-1"><strong>Latitud:</strong> {{ $farmacia['lat'] }}</p>
                <p class="mb-1"><strong>Longitud:</strong> {{ $farmacia['lng'] }}</p>
                <p class="mb-0"><strong>Dirección:</strong> Lima, Perú</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card card-dashboard">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0" style="color: #1f2937;">
                    <i class="fas fa-list text-success me-2"></i>Proveedores Cercanos
                </h5>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Proveedor</th>
                                <th>Distancia</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($proveedores as $prov)
                                <tr>
                                    <td>{{ $prov['nombre'] }}</td>
                                    <td class="fw-bold text-success">{{ $prov['distancia_km'] }} km</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const farmacia = @json($farmacia);
    const proveedores = @json($proveedores);

    const map = L.map('map').setView([farmacia.lat, farmacia.lng], 12);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    // Icono farmacia (verde)
    const iconFarmacia = L.divIcon({
        className: 'custom-div-icon',
        html: `<div style="background: #10b981; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3);"><i class="fas fa-pills"></i></div>`,
        iconSize: [36, 36],
        iconAnchor: [18, 18]
    });

    // Icono proveedor (azul)
    const iconProveedor = L.divIcon({
        className: 'custom-div-icon',
        html: `<div style="background: #3b82f6; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3);"><i class="fas fa-truck" style="font-size: 12px;"></i></div>`,
        iconSize: [30, 30],
        iconAnchor: [15, 15]
    });

    // Marcador farmacia
    L.marker([farmacia.lat, farmacia.lng], {icon: iconFarmacia})
        .addTo(map)
        .bindPopup(`<strong style="color: #10b981;">${farmacia.nombre}</strong><br>Sede Principal`);

    // Marcadores proveedores
    proveedores.forEach(prov => {
        L.marker([prov.lat, prov.lng], {icon: iconProveedor})
            .addTo(map)
            .bindPopup(`
                <strong>${prov.nombre}</strong><br>
                Contacto: ${prov.contacto || 'N/A'}<br>
                Tel: ${prov.telefono || 'N/A'}<br>
                <span style="color: #10b981; font-weight: bold;">${prov.distancia_km} km</span>
            `);
    });
</script>
@endsection