<nav class="col-md-2 d-none d-md-block sidebar p-0">
    <div class="sidebar-sticky pt-3">
        <div class="text-center mb-4 px-3">
            <h4 class="fw-bold mb-0">
                <i class="fas fa-pills me-2"></i>EDDUFARMA
            </h4>
            <small class="text-white-50">Sistema Integral</small>
        </div>

        @php
            $rol = auth()->user()->rol->nombre ?? '';
        @endphp

        <ul class="nav flex-column">
            {{-- ADMIN --}}
            @if($rol === 'Administrativo')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-chart-line"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.usuarios') ? 'active' : '' }}" href="{{ route('admin.usuarios') }}">
                        <i class="fas fa-users"></i> Usuarios
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.productos') ? 'active' : '' }}" href="{{ route('admin.productos') }}">
                        <i class="fas fa-capsules"></i> Medicamentos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.categorias') ? 'active' : '' }}" href="{{ route('admin.categorias') }}">
                        <i class="fas fa-tags"></i> Categorías
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.reportes') ? 'active' : '' }}" href="{{ route('admin.reportes') }}">
                        <i class="fas fa-file-invoice-dollar"></i> Reportes
                    </a>
                </li>
            @endif

            {{-- FARMACÉUTICO --}}
            @if($rol === 'Farmaceutico')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('farmaceutico.dashboard') ? 'active' : '' }}" href="{{ route('farmaceutico.dashboard') }}">
                        <i class="fas fa-user-md"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('farmaceutico.recetas') ? 'active' : '' }}" href="{{ route('farmaceutico.recetas') }}">
                        <i class="fas fa-file-prescription"></i> Validar Recetas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('farmaceutico.anular') ? 'active' : '' }}" href="{{ route('farmaceutico.anular') }}">
                        <i class="fas fa-ban"></i> Anular Ventas
                    </a>
                </li>
            @endif

            {{-- TÉCNICO --}}
            @if($rol === 'TecnicoFarmaceutico')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('tecnico.dashboard') ? 'active' : '' }}" href="{{ route('tecnico.dashboard') }}">
                        <i class="fas fa-desktop"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('tecnico.dispensar') ? 'active' : '' }}" href="{{ route('tecnico.dispensar') }}">
                        <i class="fas fa-cash-register"></i> Punto de Venta
                    </a>
                </li>
            @endif

            {{-- ALMACÉN --}}
            @if($rol === 'EncargadoAlmacen')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('almacen.dashboard') ? 'active' : '' }}" href="{{ route('almacen.dashboard') }}">
                        <i class="fas fa-warehouse"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('almacen.lotes') ? 'active' : '' }}" href="{{ route('almacen.lotes') }}">
                        <i class="fas fa-boxes"></i> Lotes
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('almacen.proveedores') ? 'active' : '' }}" href="{{ route('almacen.proveedores') }}">
                        <i class="fas fa-truck"></i> Proveedores
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('almacen.ordenes') ? 'active' : '' }}" href="{{ route('almacen.ordenes') }}">
                        <i class="fas fa-clipboard-list"></i> Órdenes
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('almacen.mapa') ? 'active' : '' }}" href="{{ route('almacen.mapa') }}">
                        <i class="fas fa-map-marked-alt"></i> Mapa
                    </a>
                </li>
            @endif

            {{-- CLIENTE --}}
            @if($rol === 'Cliente')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('cliente.dashboard') ? 'active' : '' }}" href="{{ route('cliente.dashboard') }}">
                        <i class="fas fa-home"></i> Inicio
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('cliente.catalogo') ? 'active' : '' }}" href="{{ route('cliente.catalogo') }}">
                        <i class="fas fa-th-large"></i> Catálogo
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('cliente.historial') ? 'active' : '' }}" href="{{ route('cliente.historial') }}">
                        <i class="fas fa-history"></i> Historial
                    </a>
                </li>
            @endif

            <li class="nav-item mt-4">
                <form action="{{ route('logout') }}" method="POST" class="px-3">
                    @csrf
                    <button type="submit" class="btn btn-outline-light w-100">
                        <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                    </button>
                </form>
            </li>
        </ul>
    </div>
</nav>