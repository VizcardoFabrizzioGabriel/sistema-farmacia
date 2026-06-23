@extends('layouts.app')

@section('title', 'Iniciar Sesión - EDDUFARMA')

@section('styles')
<style>
    body {
        background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 50%, #a7f3d0 100%);
        min-height: 100vh;
    }
    
    .login-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .login-card {
        background: white;
        border-radius: 24px;
        box-shadow: 0 20px 60px rgba(16, 185, 129, 0.15);
        padding: 48px;
        width: 100%;
        max-width: 440px;
        border: 1px solid #e5e7eb;
    }
    
    .login-logo {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 24px;
        box-shadow: 0 8px 24px rgba(16, 185, 129, 0.3);
    }
    
    .login-logo i {
        font-size: 36px;
        color: white;
    }
    
    .form-control-custom {
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 14px 18px;
        font-size: 15px;
        transition: all 0.3s;
    }
    
    .form-control-custom:focus {
        border-color: #10b981;
        box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
    }
    
    .btn-login {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border: none;
        border-radius: 12px;
        padding: 14px;
        font-weight: 600;
        font-size: 16px;
        color: white;
        width: 100%;
        transition: all 0.3s;
        box-shadow: 0 4px 16px rgba(16, 185, 129, 0.3);
    }
    
    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(16, 185, 129, 0.4);
    }
    
    .input-group-text-custom {
        background: #f0fdf4;
        border: 2px solid #e5e7eb;
        border-right: none;
        border-radius: 12px 0 0 12px;
        color: #10b981;
    }
    
    .form-control-custom.input-with-icon {
        border-left: none;
        border-radius: 0 12px 12px 0;
    }
</style>
@endsection

@section('content')
<div class="login-container">
    <div class="login-card">
        <div class="login-logo">
            <i class="fas fa-pills"></i>
        </div>
        
        <h3 class="text-center fw-bold mb-1" style="color: #1f2937;">EDDUFARMA</h3>
        <p class="text-center text-muted mb-4">Sistema Integral de Gestión Farmacéutica</p>
        
        @if($errors->any())
            <div class="alert alert-danger rounded-3 border-0" style="background: #fef2f2; color: #dc2626;">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ $errors->first() }}
            </div>
        @endif
        
        <form method="POST" action="{{ route('login') }}">
            @csrf
            
            <div class="mb-3">
                <label class="form-label fw-semibold text-secondary" style="font-size: 14px;">Correo Electrónico</label>
                <div class="input-group">
                    <span class="input-group-text input-group-text-custom">
                        <i class="fas fa-envelope"></i>
                    </span>
                    <input type="email" name="email" class="form-control form-control-custom input-with-icon" 
                           placeholder="usuario@eddufarma.com" required autofocus>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="form-label fw-semibold text-secondary" style="font-size: 14px;">Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text input-group-text-custom">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input type="password" name="password" class="form-control form-control-custom input-with-icon" 
                           placeholder="••••••••" required>
                </div>
            </div>
            
            <button type="submit" class="btn btn-login">
                <i class="fas fa-sign-in-alt me-2"></i>Ingresar al Sistema
            </button>
        </form>
        
        <div class="text-center mt-4">
            <small class="text-muted">
                <i class="fas fa-shield-alt me-1"></i>
                Acceso seguro y monitoreado
            </small>
        </div>
    </div>
</div>
@endsection