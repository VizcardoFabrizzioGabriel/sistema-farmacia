<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'EDDUFARMA')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Leaflet CSS (Mapas) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <style>
        :root {
            --primary: #2c5aa0;
            --secondary: #48bb78;
            --danger: #e53e3e;
            --warning: #ed8936;
            --info: #4299e1;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #1a365d 0%, #2c5aa0 100%);
            color: white;
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            border-radius: 8px;
            margin: 4px 8px;
            transition: all 0.3s;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.15);
            color: white;
        }
        
        .sidebar .nav-link i {
            width: 24px;
            margin-right: 10px;
        }
        
        .main-content {
            padding: 20px;
        }
        
        .card-dashboard {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            transition: transform 0.2s;
        }
        
        .card-dashboard:hover {
            transform: translateY(-2px);
        }
        
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
        
        .bg-gradient-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .bg-gradient-success { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
        .bg-gradient-warning { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .bg-gradient-info { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        
        .btn-primary-custom {
            background: var(--primary);
            border: none;
            border-radius: 8px;
            padding: 10px 24px;
        }
        
        .table-custom thead th {
            background: var(--primary);
            color: white;
            border: none;
        }
        
        .badge-cuarentena { background: #e53e3e; }
        .badge-porvencer { background: #ed8936; }
        .badge-activo { background: #48bb78; }
        .badge-baja { background: #718096; }
        
        .alert-float {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
        }
        
        #map { height: 500px; border-radius: 12px; }
    </style>
    
    @yield('styles')
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            @auth
                @include('layouts.partials.sidebar')
            @endauth
            
            <!-- Main Content -->
            <main class="col {{ auth()->check() ? 'ms-sm-auto col-lg-10' : 'col-12' }}">
                @include('layouts.partials.navbar')
                
                <div class="main-content">
                    @if(session('success'))
                        <div class="alert alert-success alert-float alert-dismissible fade show">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-float alert-dismissible fade show">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    @if(session('warning'))
                        <div class="alert alert-warning alert-float alert-dismissible fade show">
                            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('warning') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <!-- Stripe JS -->
    <script src="https://js.stripe.com/v3/"></script>
    
    <script>
        // Token CSRF global para AJAX
        window.csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        
        // Auto-cerrar alertas
        setTimeout(() => {
            document.querySelectorAll('.alert-float').forEach(alert => {
                bootstrap.Alert.getOrCreateInstance(alert).close();
            });
        }, 5000);
    </script>
    
    @yield('scripts')
</body>
</html>