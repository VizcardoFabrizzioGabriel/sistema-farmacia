<header class="navbar navbar-light bg-white sticky-top flex-md-nowrap p-0 shadow-sm" style="border-bottom: 3px solid #10b981;">
    <button class="navbar-toggler d-md-none collapsed ms-3" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu">
        <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="w-100"></div>
    
    <div class="navbar-nav">
        <div class="nav-item text-nowrap d-flex align-items-center px-3">
            <div class="text-end me-3">
                <div class="fw-bold text-dark" style="font-size: 0.9rem;">
                    {{ auth()->user()->nombres }} {{ auth()->user()->apellidos }}
                </div>
                <small class="text-success fw-semibold">
                    <i class="fas fa-circle me-1" style="font-size: 8px;"></i>
                    {{ auth()->user()->rol->nombre }}
                </small>
            </div>
            <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                <i class="fas fa-user text-success" style="font-size: 18px;"></i>
            </div>
        </div>
    </div>
</header>