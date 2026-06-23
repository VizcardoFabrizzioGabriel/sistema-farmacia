@extends('layouts.app')

@section('title', 'Dashboard Farmacéutico - EDDUFARMA')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="color: #1f2937;">
            <i class="fas fa-user-md text-success me-2"></i>Panel del Farmacéutico
        </h2>
        <p class="text-muted mb-0">Validación de recetas y supervisión médica</p>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card card-dashboard h-100" style="border-left: 4px solid #f59e0b;">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning me-3">
                    <i class="fas fa-file-prescription"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Recetas Pendientes</h6>
                    <h3 class="fw-bold mb-0" style="color: #1f2937;">{{ $recetasPendientes }}</h3>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card card-dashboard h-100" style="border-left: 4px solid #10b981;">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-success bg-opacity-10 text-success me-3">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Recetas Hoy</h6>
                    <h3 class="fw-bold mb-0" style="color: #1f2937;">{{ $recetasHoy }}</h3>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card card-dashboard h-100" style="border-left: 4px solid #ef4444;">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-danger bg-opacity-10 text-danger me-3">
                    <i class="fas fa-ban"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Ventas Anulables</h6>
                    <h3 class="fw-bold mb-0" style="color: #1f2937;">{{ $ventasAnulables }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <a href="{{ route('farmaceutico.recetas') }}" class="card card-dashboard text-decoration-none h-100">
            <div class="card-body text-center py-5">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning mx-auto mb-3" style="width: 64px; height: 64px; font-size: 28px;">
                    <i class="fas fa-file-prescription"></i>
                </div>
                <h4 class="fw-bold" style="color: #1f2937;">Validar Recetas</h4>
                <p class="text-muted mb-0">Revisar y aprobar recetas médicas pendientes</p>
            </div>
        </a>
    </div>
    
    <div class="col-md-6">
        <a href="{{ route('farmaceutico.anular') }}" class="card card-dashboard text-decoration-none h-100">
            <div class="card-body text-center py-5">
                <div class="stat-icon bg-danger bg-opacity-10 text-danger mx-auto mb-3" style="width: 64px; height: 64px; font-size: 28px;">
                    <i class="fas fa-undo-alt"></i>
                </div>
                <h4 class="fw-bold" style="color: #1f2937;">Anular Ventas</h4>
                <p class="text-muted mb-0">Cancelar ventas con error del día actual</p>
            </div>
        </a>
    </div>
</div>

<div class="card card-dashboard mt-4">
    <div class="card-header bg-white border-0 pt-4 px-4">
        <h5 class="fw-bold mb-0" style="color: #1f2937;">
            <i class="fas fa-robot text-success me-2"></i>Asistente Gemini - Interacciones Medicamentosas
        </h5>
    </div>
    <div class="card-body px-4 pb-4">
        <div class="row g-3">
            <div class="col-md-5">
                <input type="text" id="med1" class="form-control" placeholder="Medicamento 1" style="border-radius: 10px; border: 2px solid #e5e7eb;">
            </div>
            <div class="col-md-5">
                <input type="text" id="med2" class="form-control" placeholder="Medicamento 2" style="border-radius: 10px; border: 2px solid #e5e7eb;">
            </div>
            <div class="col-md-2">
                <button class="btn btn-success w-100" onclick="consultarInteracciones()" style="border-radius: 10px;">
                    <i class="fas fa-search me-2"></i>Consultar
                </button>
            </div>
        </div>
        <div id="resultadoGemini" class="mt-3 p-3 rounded-3 d-none" style="background: #f0fdf4; border: 1px solid #a7f3d0;"></div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function consultarInteracciones() {
    const med1 = document.getElementById('med1').value;
    const med2 = document.getElementById('med2').value;
    
    if (!med1 || !med2) {
        alert('Ingrese ambos medicamentos');
        return;
    }
    
    const resultado = document.getElementById('resultadoGemini');
    resultado.classList.remove('d-none');
    resultado.innerHTML = '<i class="fas fa-spinner fa-spin text-success"></i> Consultando...';
    
    fetch('/api/gemini/interacciones', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.csrfToken
        },
        body: JSON.stringify({medicamento1: med1, medicamento2: med2})
    })
    .then(r => r.json())
    .then(data => {
        resultado.innerHTML = data.success 
            ? `<strong style="color: #059669;">Resultado:</strong><br>${data.respuesta.replace(/\n/g, '<br>')}`
            : `<span class="text-danger">${data.message}</span>`;
    });
}
</script>
@endsection