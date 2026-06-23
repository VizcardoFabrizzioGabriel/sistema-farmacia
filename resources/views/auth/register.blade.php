@extends('layouts.app')

@section('title', 'Registro - EDDUFARMA')

@section('styles')
<style>
    body {
        background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 50%, #a7f3d0 100%);
        min-height: 100vh;
    }

    .register-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
    }

    .register-card {
        background: white;
        border-radius: 24px;
        box-shadow: 0 20px 60px rgba(16, 185, 129, 0.15);
        padding: 48px;
        width: 100%;
        max-width: 520px;
        border: 1px solid #e5e7eb;
    }

    .register-logo {
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

    .register-logo i {
        font-size: 36px;
        color: white;
    }

    .form-control-register {
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 14px 18px;
        font-size: 15px;
        transition: all 0.3s;
    }

    .form-control-register:focus {
        border-color: #10b981;
        box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
    }

    .input-group-text-register {
        background: #f0fdf4;
        border: 2px solid #e5e7eb;
        border-right: none;
        border-radius: 12px 0 0 12px;
        color: #10b981;
    }

    .form-control-register.input-with-icon {
        border-left: none;
        border-radius: 0 12px 12px 0;
    }

    .btn-register {
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

    .btn-register:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(16, 185, 129, 0.4);
    }

    .login-link {
        color: #10b981;
        text-decoration: none;
        font-weight: 600;
    }

    .login-link:hover {
        color: #059669;
        text-decoration: underline;
    }
</style>
@endsection

@section('content')
<div class="register-container">
    <div class="register-card">
        <div class="register-logo">
            <i class="fas fa-pills"></i>
        </div>

        <h3 class="text-center fw-bold mb-1" style="color: #1f2937;">Crear Cuenta</h3>
        <p class="text-center text-muted mb-4">Regístrate en EDDUFARMA</p>

        @if($errors->any())
            <div class="alert alert-danger rounded-3 border-0" style="background: #fef2f2; color: #dc2626;">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-secondary" style="font-size: 14px;">DNI</label>
                    <div class="input-group">
                        <span class="input-group-text input-group-text-register">
                            <i class="fas fa-id-card"></i>
                        </span>
                        <input type="text" name="dni" class="form-control form-control-register input-with-icon"
                               placeholder="12345678" required maxlength="15" value="{{ old('dni') }}">
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-secondary" style="font-size: 14px;">Email</label>
                    <div class="input-group">
                        <span class="input-group-text input-group-text-register">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input type="email" name="email" class="form-control form-control-register input-with-icon"
                               placeholder="correo@ejemplo.com" required value="{{ old('email') }}">
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-secondary" style="font-size: 14px;">Nombres</label>
                    <div class="input-group">
                        <span class="input-group-text input-group-text-register">
                            <i class="fas fa-user"></i>
                        </span>
                        <input type="text" name="nombres" class="form-control form-control-register input-with-icon"
                               placeholder="Juan" required value="{{ old('nombres') }}">
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-secondary" style="font-size: 14px;">Apellidos</label>
                    <div class="input-group">
                        <span class="input-group-text input-group-text-register">
                            <i class="fas fa-user"></i>
                        </span>
                        <input type="text" name="apellidos" class="form-control form-control-register input-with-icon"
                               placeholder="Pérez" required value="{{ old('apellidos') }}">
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-secondary" style="font-size: 14px;">Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text input-group-text-register">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" name="password" class="form-control form-control-register input-with-icon"
                               placeholder="Mínimo 6 caracteres" required minlength="6">
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-secondary" style="font-size: 14px;">Confirmar</label>
                    <div class="input-group">
                        <span class="input-group-text input-group-text-register">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" name="password_confirmation" class="form-control form-control-register input-with-icon"
                               placeholder="Repetir contraseña" required>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-register mt-4">
                <i class="fas fa-user-plus me-2"></i>Crear Cuenta
            </button>
        </form>

        <div class="text-center mt-4">
            <p class="text-muted mb-0">
                ¿Ya tienes cuenta?
                <a href="{{ route('login') }}" class="login-link">Inicia sesión aquí</a>
            </p>
        </div>

        <div class="text-center mt-3">
            <small class="text-muted">
                <i class="fas fa-shield-alt me-1"></i>
                Tus datos están protegidos
            </small>
        </div>
    </div>
</div>
@endsection