<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SISGECLIN - @yield('title', 'Sistema de Gestión Clínica')</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/image-Photoroom.png') }}">
    <link rel="shortcut icon" href="{{ asset('img/image-Photoroom.ico') }}">
    <link href="{{ asset('vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/styleS.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dark-mode-improvements.css') }}">
    <link rel="stylesheet" href="{{ asset('css/pagination-pro.css') }}">
    <link rel="stylesheet" href="{{ asset('css/mobile.css') }}">
    <link rel="stylesheet" href="{{ asset('css/pro-ui.css') }}">
    {{-- Anti-FOUC script movido al inicio del body para aplicar clases reales --}}
    <style>
        /* Estado colapsado (SOLO desktop) */
        .sidebar-collapsed-init #sidebar { width: 70px !important; transition: none !important; }
        .sidebar-collapsed-init #content { margin-left: 70px !important; transition: none !important; }
        .sidebar-collapsed-init #sidebar .brand span,
        .sidebar-collapsed-init #sidebar .side-menu li a span,
        .sidebar-collapsed-init .sidebar-logout-btn span { display: none !important; }
        .sidebar-collapsed-init #sidebar .brand { justify-content: center; padding: 20px 0; }
        .sidebar-collapsed-init #sidebar .side-menu li a { justify-content: center; padding: 14px 0; }
        .sidebar-collapsed-init .sidebar-logout-btn { justify-content: center !important; padding: 14px 0 !important; gap: 0 !important; }
        /* Dark mode inmediato */
        .dark-mode-init body { background: #0f172a; color: #f1f5f9; }

        /* MÓVIL: nunca aplicar collapsed-init */
        @media (max-width: 768px) {
            .sidebar-collapsed-init #sidebar {
                width: var(--sidebar-mobile-width, 280px) !important;
                transform: translateX(-100%) !important;
                margin-left: 0 !important;
            }
            .sidebar-collapsed-init #content {
                margin-left: 0 !important;
            }
        }
        /* ── Grupos en sidebar colapsado ── */
        .sidebar-collapsed-init #sidebar .grp-label,
        .sidebar-collapsed-init #sidebar .grp-arrow { display: none !important; }
        .sidebar-collapsed-init #sidebar .side-menu-group-btn { justify-content: center !important; padding: 13px 0 !important; position: relative; }
        .sidebar-collapsed-init #sidebar .grp-icon { font-size: 22px !important; }
        /* flyout: mostrar submenú a la derecha al hover */
        .sidebar-collapsed-init #sidebar .side-menu-group { position: relative; }
        .sidebar-collapsed-init #sidebar .side-submenu {
            display: none !important;
            position: fixed !important;
            left: 70px !important;
            top: auto !important;
            width: 200px !important;
            background: var(--bg-card) !important;
            border-radius: 0 10px 10px 0 !important;
            box-shadow: 4px 4px 20px rgba(0,0,0,.2) !important;
            padding: 6px 0 !important;
            max-height: none !important;
            opacity: 1 !important;
            z-index: 9999 !important;
        }
        .sidebar-collapsed-init #sidebar .side-menu-group:hover > .side-submenu { display: block !important; }
        .sidebar-collapsed-init #sidebar .side-submenu li a {
            padding: 9px 16px !important;
            border-radius: 0 !important;
            font-size: 13px !important;
        }
    </style>
    <style>
        /* ── Notificaciones dropdown ── */
        .notif-wrapper { position: relative; }

        .notif-btn {
            position: relative;
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-secondary);
            font-size: 22px;
            display: flex;
            align-items: center;
            padding: 4px;
            border-radius: 50%;
            transition: background .2s;
        }
        .notif-btn:hover { background: var(--bg-body); }

        .notif-badge {
            position: absolute;
            top: -4px; right: -4px;
            background: #dc3545;
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            min-width: 18px;
            height: 18px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 4px;
            line-height: 1;
        }
        .notif-badge.hidden { display: none; }

        .notif-dropdown {
            display: none;
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            width: 340px;
            background: var(--bg-card);
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0,0,0,.25);
            z-index: 9999;
            overflow: hidden;
            border: 1px solid var(--border-color);
        }
        body.dark-mode .notif-dropdown {
            box-shadow: 0 8px 40px rgba(0,0,0,.6);
            border-color: #334155;
        }
        .notif-dropdown.open { display: block; }

        .notif-list { max-height: 360px; overflow-y: auto; }

        .notif-item {
            display: flex;
            gap: 12px;
            padding: 12px 16px;
            border-bottom: 1px solid var(--border-color, #f0f0f0);
            cursor: pointer;
            transition: background .15s;
            text-decoration: none;
            color: var(--text-primary);
        }
        .notif-item:hover { background: var(--bg-body); }
        .notif-item.unread { background: rgba(13,110,253,.08); }
        .notif-item.unread:hover { background: rgba(13,110,253,.14); }
        body.dark-mode .notif-item.unread { background: rgba(13,110,253,.15); }
        body.dark-mode .notif-item.unread:hover { background: rgba(13,110,253,.22); }

        .notif-icon {
            width: 38px; height: 38px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }
        /* Modo oscuro — colores vibrantes para mejor contraste */
        .notif-icon.primary { background: rgba(13,110,253,.25);  color: #6ea8fe; }
        .notif-icon.success { background: rgba(25,135,84,.25);   color: #75b798; }
        .notif-icon.danger  { background: rgba(220,53,69,.25);   color: #ea868f; }
        .notif-icon.warning { background: rgba(255,193,7,.25);   color: #ffda6a; }
        .notif-icon.info    { background: rgba(13,202,240,.25);  color: #6edff6; }

        /* Modo claro — íconos más oscuros y saturados */
        body:not(.dark-mode) .notif-icon.primary { background: #cfe2ff; color: #0a58ca; }
        body:not(.dark-mode) .notif-icon.success { background: #d1e7dd; color: #146c43; }
        body:not(.dark-mode) .notif-icon.danger  { background: #f8d7da; color: #b02a37; }
        body:not(.dark-mode) .notif-icon.warning { background: #fff3cd; color: #664d03; }
        body:not(.dark-mode) .notif-icon.info    { background: #cff4fc; color: #087990; }

        .notif-body { flex: 1; min-width: 0; }
        .notif-title { font-size: 13px; font-weight: 600; color: var(--text-primary); margin-bottom: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .notif-msg   { font-size: 12px; color: var(--text-secondary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .notif-time  { font-size: 11px; color: var(--text-muted); margin-top: 2px; }

        .notif-empty { padding: 30px; text-align: center; color: var(--text-muted); font-size: 13px; }
        .notif-empty i { font-size: 36px; display: block; margin-bottom: 8px; opacity: .5; }

        /* Header del dropdown */
        .notif-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 16px;
            border-bottom: 1px solid var(--border-color);
            font-weight: 600;
            font-size: 14px;
            color: var(--text-primary);
            background: var(--bg-card);
        }
        .notif-header button {
            background: none;
            border: none;
            font-size: 12px;
            color: var(--primary);
            cursor: pointer;
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 6px;
            transition: background .15s;
        }
        .notif-header button:hover { background: rgba(13,110,253,.1); }

        /* Mensajes badge en sidebar */
        .msg-badge {
            background: #dc3545;
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            min-width: 18px;
            height: 18px;
            border-radius: 9px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 4px;
            margin-left: auto;
        }
    </style>
    <style>
        /* ── Sidebar Groups (acordeón plegable) ── */
        .side-menu-group { list-style: none; }

        .side-menu-group-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            background: none;
            border: none;
            cursor: pointer;
            padding: 10px 16px;
            border-radius: 10px;
            color: var(--text-secondary);
            font-size: 14px;
            font-weight: 500;
            text-align: left;
            transition: background .2s, color .2s;
            position: relative;
        }
        .side-menu-group-btn:hover {
            background: var(--bg-body);
            color: var(--primary);
        }
        .side-menu-group.open > .side-menu-group-btn {
            color: var(--primary);
            background: rgba(13,110,253,.08);
        }

        .grp-icon { font-size: 20px; display: flex; align-items: center; flex-shrink: 0; }
        .grp-label { flex: 1; }
        .grp-arrow {
            font-size: 18px;
            transition: transform .25s ease;
            margin-left: auto;
            flex-shrink: 0;
        }
        .side-menu-group.open > .side-menu-group-btn .grp-arrow {
            transform: rotate(-180deg);
        }

        /* Submenú colapsable */
        .side-submenu {
            list-style: none;
            padding: 0;
            margin: 0;
            overflow: hidden;
            max-height: 0;
            transition: max-height .3s ease, opacity .25s ease;
            opacity: 0;
        }
        .side-menu-group.open > .side-submenu {
            max-height: 400px;
            opacity: 1;
        }
        .side-submenu li a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 16px 8px 42px;
            border-radius: 8px;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 13.5px;
            transition: background .15s, color .15s;
        }
        .side-submenu li a:hover {
            background: var(--bg-body);
            color: var(--primary);
        }
        .side-submenu li.active > a {
            background: rgba(13,110,253,.12);
            color: var(--primary);
            font-weight: 600;
        }
        .side-submenu li a i { font-size: 17px; }

        /* Sidebar colapsado: ocultar labels y arrows */
        .sidebar-collapsed-init #sidebar .grp-label,
        .sidebar-collapsed-init #sidebar .grp-arrow { display: none !important; }
        .sidebar-collapsed-init #sidebar .side-menu-group-btn { justify-content: center; padding: 14px 0; }
        .sidebar-collapsed-init #sidebar .grp-icon { font-size: 22px; }
        .sidebar-collapsed-init #sidebar .side-submenu { display: none !important; }
    </style>
    @stack('styles')
</head>
<body>
    <script>
        // Aplicar clases INMEDIATAMENTE después de abrir el body para evitar FOUC
        (function() {
            if (localStorage.getItem('darkMode') === 'true') {
                document.body.classList.add('dark-mode');
            }
            if (window.innerWidth > 768 && localStorage.getItem('sidebarCollapsed') === 'true') {
                document.body.classList.add('sidebar-collapsed');
            }
        })();
    </script>

<!-- OVERLAY PARA MÓVIL -->
<div id="sidebar-overlay" class="sidebar-overlay"></div>

<!-- SIDEBAR -->
<section id="sidebar">
    <a href="{{ route('dashboard') }}" class="brand">
        <i class='bx bxs-clinic'></i>
        <span>SISGECLIN</span>
    </a>

    @php
        /* Cargar permisos del usuario una sola vez para todo el sidebar */
        $usuarioActual = auth()->user();
        if (!$usuarioActual->isAdmin()) {
            $usuarioActual->load('permisos');
        }
    @endphp

    <div class="sidebar-scroll">
        <ul class="side-menu">
            {{-- Dashboard — siempre visible --}}
            <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}" data-tooltip="Panel de Control">
                    <i class='bx bxs-dashboard'></i><span>Panel de Control</span>
                </a>
            </li>

            {{-- ── GRUPO: CLÍNICO ── --}}
            @php
                $clinicaActiva = request()->routeIs('pacientes.*') || request()->routeIs('consultas.*') || request()->routeIs('citas.*');
                $showClinica   = $usuarioActual->puedeEn('pacientes','ver') || $usuarioActual->puedeEn('consultas','ver') || $usuarioActual->puedeEn('citas','ver');
            @endphp
            @if($showClinica)
            <li class="side-menu-group {{ $clinicaActiva ? 'open' : '' }}" id="grp-clinica">
                <button class="side-menu-group-btn" onclick="toggleGroup('grp-clinica')" data-tooltip="Clínico">
                    <span class="grp-icon"><i class='bx bxs-heart-circle'></i></span>
                    <span class="grp-label">Clínico</span>
                    <i class='bx bx-chevron-down grp-arrow'></i>
                </button>
                <ul class="side-submenu">
                    @if($usuarioActual->puedeEn('pacientes','ver'))
                    <li class="{{ request()->routeIs('pacientes.*') ? 'active' : '' }}">
                        <a href="{{ route('pacientes.index') }}"><i class='bx bxs-user-detail'></i><span>Pacientes</span></a>
                    </li>
                    @endif
                    @if($usuarioActual->puedeEn('consultas','ver'))
                    <li class="{{ request()->routeIs('consultas.*') ? 'active' : '' }}">
                        <a href="{{ route('consultas.index') }}"><i class='bx bx-clipboard'></i><span>Consultas</span></a>
                    </li>
                    @endif
                    @if($usuarioActual->puedeEn('citas','ver'))
                    <li class="{{ request()->routeIs('citas.*') ? 'active' : '' }}">
                        <a href="{{ route('citas.index') }}"><i class='bx bx-calendar-event'></i><span>Agenda de Citas</span></a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif

            {{-- ── GRUPO: DOCUMENTOS ── --}}
            @php
                $docsActiva = request()->routeIs('recetas.*') || request()->routeIs('informes-medicos.*') || request()->routeIs('gastos-medicos.*');
                $showDocs   = $usuarioActual->puedeEn('recetas','ver') || $usuarioActual->puedeEn('informes','ver') || $usuarioActual->puedeEn('gastos','ver');
            @endphp
            @if($showDocs)
            <li class="side-menu-group {{ $docsActiva ? 'open' : '' }}" id="grp-documentos">
                <button class="side-menu-group-btn" onclick="toggleGroup('grp-documentos')" data-tooltip="Documentos">
                    <span class="grp-icon"><i class='bx bx-file'></i></span>
                    <span class="grp-label">Documentos</span>
                    <i class='bx bx-chevron-down grp-arrow'></i>
                </button>
                <ul class="side-submenu">
                    @if($usuarioActual->puedeEn('recetas','ver'))
                    <li class="{{ request()->routeIs('recetas.*') ? 'active' : '' }}">
                        <a href="{{ route('recetas.index') }}"><i class='bx bx-file-blank'></i><span>Recetas</span></a>
                    </li>
                    @endif
                    @if($usuarioActual->puedeEn('informes','ver'))
                    <li class="{{ request()->routeIs('informes-medicos.*') ? 'active' : '' }}">
                        <a href="{{ route('informes-medicos.index') }}"><i class='bx bx-id-card'></i><span>Informes Médicos</span></a>
                    </li>
                    @endif
                    @if($usuarioActual->puedeEn('gastos','ver'))
                    <li class="{{ request()->routeIs('gastos-medicos.*') ? 'active' : '' }}">
                        <a href="{{ route('gastos-medicos.index') }}"><i class='bx bx-receipt'></i><span>Gastos Médicos</span></a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif

            {{-- ── GRUPO: LABORATORIO ── --}}
            @if($usuarioActual->puedeEn('examenes','ver'))
            @php $labActiva = request()->routeIs('examenes-laboratorio.*'); @endphp
            <li class="side-menu-group {{ $labActiva ? 'open' : '' }}" id="grp-laboratorio">
                <button class="side-menu-group-btn" onclick="toggleGroup('grp-laboratorio')" data-tooltip="Laboratorio">
                    <span class="grp-icon"><i class='bx bx-test-tube'></i></span>
                    <span class="grp-label">Laboratorio</span>
                    <i class='bx bx-chevron-down grp-arrow'></i>
                </button>
                <ul class="side-submenu">
                    <li class="{{ $labActiva ? 'active' : '' }}">
                        <a href="{{ route('examenes-laboratorio.index') }}"><i class='bx bx-test-tube'></i><span>Exámenes de Lab.</span></a>
                    </li>
                </ul>
            </li>
            @endif

            {{-- ── GRUPO: INVENTARIO ── --}}
            @php
                $invActiva = request()->routeIs('medicina.*') || request()->routeIs('inventario.*') || (!$usuarioActual->isAdmin() && request()->routeIs('solicitudes.*'));
                $showInv   = $usuarioActual->puedeEn('medicina','ver') || $usuarioActual->puedeEn('inventario','ver') || (!$usuarioActual->isAdmin() && $usuarioActual->puedeEn('solicitudes','ver'));
            @endphp
            @if($showInv)
            <li class="side-menu-group {{ $invActiva ? 'open' : '' }}" id="grp-inventario">
                <button class="side-menu-group-btn" onclick="toggleGroup('grp-inventario')" data-tooltip="Inventario">
                    <span class="grp-icon"><i class='bx bxs-package'></i></span>
                    <span class="grp-label">Inventario</span>
                    <i class='bx bx-chevron-down grp-arrow'></i>
                </button>
                <ul class="side-submenu">
                    @if($usuarioActual->puedeEn('medicina','ver'))
                    <li class="{{ request()->routeIs('medicina.*') ? 'active' : '' }}">
                        <a href="{{ route('medicina.index') }}"><i class='bx bxs-capsule'></i><span>Medicamentos</span></a>
                    </li>
                    @endif
                    @if($usuarioActual->puedeEn('inventario','ver'))
                    <li class="{{ request()->routeIs('inventario.*') ? 'active' : '' }}">
                        <a href="{{ route('inventario.index') }}"><i class='bx bx-transfer'></i><span>Movimientos</span></a>
                    </li>
                    @endif
                    @if(!$usuarioActual->isAdmin() && $usuarioActual->puedeEn('solicitudes','ver'))
                    <li class="{{ request()->routeIs('solicitudes.*') ? 'active' : '' }}">
                        <a href="{{ route('solicitudes.index') }}"><i class='bx bx-send'></i><span>Solicitudes</span></a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif

            {{-- ── Mensajes (enlace directo) ── --}}
            @if($usuarioActual->puedeEn('mensajes','ver'))
            @php $unreadMsg = auth()->user()->mensajesNoLeidos(); @endphp
            <li class="{{ request()->routeIs('mensajes.*') ? 'active' : '' }}">
                <a href="{{ route('mensajes.index') }}" data-tooltip="Mensajes">
                    <i class='bx bxs-envelope'></i>
                    <span>Mensajes</span>
                    <span class="msg-badge" id="sidebar-msg-badge" {{ $unreadMsg === 0 ? 'style=display:none' : '' }}>{{ $unreadMsg ?: '' }}</span>
                </a>
            </li>
            @endif

            {{-- ── Reportes (enlace directo) ── --}}
            @if($usuarioActual->puedeEn('reportes','ver'))
            <li class="{{ request()->routeIs('reportes.*') ? 'active' : '' }}">
                <a href="{{ route('reportes.index') }}" data-tooltip="Reportes">
                    <i class='bx bx-bar-chart-alt-2'></i><span>Reportes</span>
                </a>
            </li>
            @endif

            {{-- ── GRUPO: ADMINISTRACIÓN — solo admins ── --}}
            @if($usuarioActual->isAdmin())
            @php $adminActiva = request()->routeIs('nomina.*') || request()->routeIs('usuarios.*') || request()->routeIs('solicitudes.*') || request()->routeIs('bitacora.*'); @endphp
            <li class="side-menu-group {{ $adminActiva ? 'open' : '' }}" id="grp-admin">
                <button class="side-menu-group-btn" onclick="toggleGroup('grp-admin')" data-tooltip="Administración">
                    <span class="grp-icon"><i class='bx bxs-cog'></i></span>
                    <span class="grp-label">Administración</span>
                    <i class='bx bx-chevron-down grp-arrow'></i>
                </button>
                <ul class="side-submenu">
                    <li class="{{ request()->routeIs('nomina.*') ? 'active' : '' }}">
                        <a href="{{ route('nomina.index') }}"><i class='bx bxs-group'></i><span>Nómina</span></a>
                    </li>
                    <li class="{{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
                        <a href="{{ route('usuarios.index') }}"><i class='bx bxs-user-account'></i><span>Usuarios</span></a>
                    </li>
                    <li class="{{ request()->routeIs('solicitudes.*') ? 'active' : '' }}">
                        <a href="{{ route('solicitudes.index') }}"><i class='bx bx-send'></i><span>Solicitudes</span></a>
                    </li>
                    <li class="{{ request()->routeIs('bitacora.*') ? 'active' : '' }}">
                        <a href="{{ route('bitacora.index') }}"><i class='bx bx-history'></i><span>Bitácora</span></a>
                    </li>
                </ul>
            </li>
            @endif

        </ul>
    </div>

    {{-- ── Pie del sidebar ── --}}
    <ul class="side-menu bottom">
        <li class="{{ request()->routeIs('perfil.*') ? 'active' : '' }}">
            <a href="{{ route('perfil.index') }}" data-tooltip="Mi Perfil">
                <i class='bx bxs-user-circle'></i><span>Mi Perfil</span>
            </a>
        </li>
        <li>
            <form method="POST" action="{{ route('logout') }}" id="logout-form">
                @csrf
                <button type="submit" class="logout sidebar-logout-btn" data-tooltip="Cerrar Sesión">
                    <i class='bx bx-power-off'></i>
                    <span>Cerrar Sesión</span>
                </button>
            </form>
        </li>
    </ul>
</section>

<!-- CONTENT -->
<section id="content">
    <!-- NAVBAR -->
    <nav>
        <button type="button" id="menu-toggle" class="bx bx-menu sidebar-toggle-btn" aria-label="Abrir menú"></button>
        <span class="nav-title">@yield('nav-title', 'Dashboard')</span>

        <form action="{{ request()->url() }}" method="get" class="search-form" id="search-form" autocomplete="off">
            @foreach(request()->except('search', 'page') as $key => $val)
                <input type="hidden" name="{{ $key }}" value="{{ $val }}">
            @endforeach
            <div class="form-input" style="position:relative;">
                <input type="search" name="search" id="global-search" placeholder="Buscar..." value="{{ request('search') }}" autocomplete="off">
                <button type="submit"><i class='bx bx-search'></i></button>
                <!-- Dropdown de resultados AJAX -->
                <div id="search-dropdown" style="display:none;position:absolute;top:calc(100% + 6px);left:0;right:0;background:var(--bg-card);border-radius:10px;box-shadow:0 8px 30px rgba(0,0,0,.18);z-index:9999;overflow:hidden;max-height:380px;overflow-y:auto;"></div>
            </div>
        </form>

        <div class="nav-actions">
            <!-- Modo oscuro -->
            <div class="switch-mode" id="dark-toggle" title="Activar modo oscuro">
                <i class='bx bxs-moon'></i>
                <i class='bx bx-sun'></i>
                <div class="ball"></div>
            </div>

            <!-- ── Mensajes ── -->
            <div class="notif-wrapper">
                <a href="{{ route('mensajes.index') }}" class="notif-btn" title="Mensajes" style="text-decoration:none; color:inherit;">
                    <i class='bx bxs-envelope'></i>
                    <span class="notif-badge {{ auth()->user()->mensajesNoLeidos() === 0 ? 'hidden' : '' }}" id="nav-msg-badge">
                        {{ auth()->user()->mensajesNoLeidos() ?: '' }}
                    </span>
                </a>
            </div>

            <!-- ── Notificaciones ── -->
            <div class="notif-wrapper" id="notif-wrapper">
                <button class="notif-btn" id="notif-btn" title="Notificaciones" onclick="toggleNotifDropdown()">
                    <i class='bx bxs-bell'></i>
                    @php $totalNotif = auth()->user()->notificacionesNoLeidas(); @endphp
                    <span class="notif-badge {{ $totalNotif === 0 ? 'hidden' : '' }}" id="nav-notif-badge">
                        {{ $totalNotif ?: '' }}
                    </span>
                </button>

                <!-- Dropdown -->
                <div class="notif-dropdown" id="notif-dropdown">
                    <div class="notif-header">
                        <span><i class='bx bxs-bell'></i> Notificaciones</span>
                        <button onclick="marcarTodasLeidas()">Marcar todas como leídas</button>
                    </div>
                    <div class="notif-list" id="notif-list">
                        <div class="notif-empty">
                            <i class='bx bx-bell-off'></i>
                            Cargando...
                        </div>
                    </div>
                </div>
            </div>

            <!-- Perfil -->
            <div class="profile-wrapper">
                <a href="{{ route('perfil.index') }}" class="profile" title="Mi perfil">
                    <img src="" data-avatar="{{ auth()->user()->nombre_completo }}" data-bg="0d6efd" data-size="40" alt="Perfil" style="border-radius:50%;width:36px;height:36px;">
                </a>
                <div class="profile-menu">
                    <ul>
                        <li><strong>{{ auth()->user()->nombre_completo }}</strong></li>
                        <li><small>{{ ucfirst(auth()->user()->rol) }}</small></li>
                        <li><a href="{{ route('perfil.index') }}"><i class='bx bx-user'></i> Mi Perfil</a></li>
                        <li><a href="/SISGECLIN/docs/manual_usuario.html" target="_blank"><i class='bx bxs-book-open'></i> Manual de Usuario</a></li>
                        <li>
                            <button type="submit" form="logout-form" style="background:none;border:none;cursor:pointer;width:100%;text-align:left;padding:0;color:var(--danger);display:flex;align-items:center;gap:8px;font-size:14px;">
                                <i class='bx bx-log-out'></i> Cerrar Sesión
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- MAIN -->
    <main>
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @yield('content')
    </main>
</section>

<!-- TOAST CONTAINER -->
<div id="toast-container" class="toast-container" style="pointer-events:none;"></div>

<!-- MODAL CONFIRMAR ELIMINACIÓN -->
<div class="modal" id="modal-confirm-delete">
    <div class="modal-content modal-sm">
        <div class="modal-header modal-header-danger">
            <h2><i class='bx bx-error-circle'></i> Confirmar Eliminación</h2>
            <button class="modal-close" onclick="closeModal('modal-confirm-delete')">&times;</button>
        </div>
        <div class="modal-body text-center">
            <p>¿Está seguro de eliminar este registro?</p>
            <p class="text-muted" id="delete-item-name"></p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-secondary" onclick="closeModal('modal-confirm-delete')">Cancelar</button>
            <button type="button" class="btn-danger" id="btn-confirm-delete">
                <i class='bx bx-trash'></i> Eliminar
            </button>
        </div>
    </div>
</div>

<!-- MODAL CONFIRMAR CAMBIO DE ESTADO -->
<div class="modal" id="modal-confirm-estado">
    <div class="modal-content modal-sm">
        <div class="modal-header modal-header-warning">
            <h2><i class='bx bx-info-circle'></i> Cambiar Estado</h2>
            <button class="modal-close" onclick="closeModal('modal-confirm-estado')">&times;</button>
        </div>
        <div class="modal-body text-center">
            <p id="estado-message">¿Desea cambiar el estado de este usuario?</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-secondary" onclick="closeModal('modal-confirm-estado')">Cancelar</button>
            <button type="button" class="btn-warning" id="btn-confirm-estado">
                <i class='bx bx-check'></i> Confirmar
            </button>
        </div>
    </div>
</div>

<script src="{{ asset('css/script.js') }}"></script>
<script src="{{ asset('js/avatar.js') }}"></script>
<script>
window.CSRF_TOKEN = '{{ csrf_token() }}';
window.BASE_URL   = '{{ url('/') }}';

// ── Notificaciones ────────────────────────────────────────────────────────────

const NOTIF_URL = '{{ route('notificaciones.index') }}';
const COUNT_URL = '{{ route('notificaciones.count') }}';
const READ_ALL_URL = '{{ route('notificaciones.readAll') }}';

let notifOpen = false;

function toggleNotifDropdown() {
    notifOpen = !notifOpen;
    document.getElementById('notif-dropdown').classList.toggle('open', notifOpen);
    if (notifOpen) cargarNotificaciones();
}

// Cerrar al hacer clic fuera
document.addEventListener('click', function(e) {
    const wrapper = document.getElementById('notif-wrapper');
    if (wrapper && !wrapper.contains(e.target)) {
        notifOpen = false;
        document.getElementById('notif-dropdown').classList.remove('open');
    }
});

async function cargarNotificaciones() {
    try {
        const res  = await fetch(NOTIF_URL, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': window.CSRF_TOKEN } });
        const data = await res.json();
        renderNotificaciones(data.notificaciones);
    } catch(e) {
        document.getElementById('notif-list').innerHTML = '<div class="notif-empty"><i class="bx bx-error"></i>Error al cargar</div>';
    }
}

function renderNotificaciones(items) {
    const list = document.getElementById('notif-list');
    if (!items || items.length === 0) {
        list.innerHTML = '<div class="notif-empty"><i class="bx bx-bell-off"></i>Sin notificaciones nuevas</div>';
        return;
    }

    const colorMap = { primary:'primary', success:'success', danger:'danger', warning:'warning', info:'info' };

    list.innerHTML = items.map(n => {
        const color = colorMap[n.color] || 'primary';
        const url   = n.url ? (window.BASE_URL + n.url) : '#';
        const time  = formatTime(n.created_at);
        return `
        <a class="notif-item ${!n.leida ? 'unread' : ''}" href="${url}"
           onclick="marcarLeida(${n.id}, event)">
            <div class="notif-icon ${color}">
                <i class='bx bx-${n.icono}'></i>
            </div>
            <div class="notif-body">
                <div class="notif-title">${escHtml(n.titulo)}</div>
                <div class="notif-msg">${escHtml(n.mensaje)}</div>
                <div class="notif-time">${time}</div>
            </div>
        </a>`;
    }).join('');
}

async function marcarLeida(id, e) {
    // No prevenir la navegación, solo marcar como leída en background
    try {
        await fetch(`{{ url('notificaciones') }}/${id}/read`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN, 'Content-Type': 'application/x-www-form-urlencoded' },
            body: '_method=PATCH'
        });
    } catch(_) {}
}

async function marcarTodasLeidas() {
    try {
        await fetch(READ_ALL_URL, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN, 'Content-Type': 'application/x-www-form-urlencoded' },
            body: '_method=PATCH'
        });
        actualizarBadges(0, null);
        document.getElementById('notif-list').innerHTML = '<div class="notif-empty"><i class="bx bx-bell-off"></i>Sin notificaciones nuevas</div>';
    } catch(_) {}
}

function actualizarBadges(notif, mensajes) {
    // Badge notificaciones
    const nb = document.getElementById('nav-notif-badge');
    if (nb) {
        if (notif > 0) { nb.textContent = notif > 99 ? '99+' : notif; nb.classList.remove('hidden'); }
        else           { nb.textContent = ''; nb.classList.add('hidden'); }
    }
    // Badge mensajes navbar
    if (mensajes !== null) {
        const mb = document.getElementById('nav-msg-badge');
        if (mb) {
            if (mensajes > 0) { mb.textContent = mensajes > 99 ? '99+' : mensajes; mb.classList.remove('hidden'); }
            else              { mb.textContent = ''; mb.classList.add('hidden'); }
        }
        // Badge mensajes sidebar
        const sb = document.getElementById('sidebar-msg-badge');
        if (sb) {
            if (mensajes > 0) { sb.textContent = mensajes > 99 ? '99+' : mensajes; sb.style.display = 'inline-flex'; }
            else              { sb.style.display = 'none'; }
        }
    }
}

// Polling cada 30 segundos
async function pollBadges() {
    try {
        const res  = await fetch(COUNT_URL, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': window.CSRF_TOKEN } });
        const data = await res.json();
        if (data.success) {
            actualizarBadges(data.notificaciones, data.mensajes);
            // Si el dropdown está abierto, recargar la lista
            if (notifOpen) cargarNotificaciones();
        }
    } catch(_) {}
}

// Helpers
function escHtml(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function formatTime(dateStr) {
    if (!dateStr) return '';
    const d    = new Date(dateStr);
    const now  = new Date();
    const diff = Math.floor((now - d) / 1000);
    if (diff < 60)   return 'Hace un momento';
    if (diff < 3600) return `Hace ${Math.floor(diff/60)} min`;
    if (diff < 86400)return `Hace ${Math.floor(diff/3600)} h`;
    return d.toLocaleDateString('es-DO', { day:'2-digit', month:'short' });
}

// ── Búsqueda global AJAX ─────────────────────────────────────────────────────
(function() {
    const input    = document.getElementById('global-search');
    const dropdown = document.getElementById('search-dropdown');
    const SEARCH_URL = '{{ route('buscar') }}';
    let searchTimer = null;

    if (!input) return;

    input.addEventListener('input', function() {
        clearTimeout(searchTimer);
        const q = this.value.trim();
        if (q.length < 2) { dropdown.style.display = 'none'; return; }
        searchTimer = setTimeout(() => doSearch(q), 280);
    });

    input.addEventListener('focus', function() {
        if (this.value.trim().length >= 2) doSearch(this.value.trim());
    });

    document.addEventListener('click', function(e) {
        if (!input.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });

    async function doSearch(q) {
        dropdown.innerHTML = '<div style="padding:14px 16px;font-size:13px;color:var(--text-muted);display:flex;align-items:center;gap:8px;"><i class=\'bx bx-loader-alt bx-spin\'></i> Buscando...</div>';
        dropdown.style.display = 'block';
        try {
            const res  = await fetch(`${SEARCH_URL}?q=${encodeURIComponent(q)}`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': window.CSRF_TOKEN }
            });
            const data = await res.json();
            renderResults(data.results, q);
        } catch(e) {
            dropdown.innerHTML = '<div style="padding:14px 16px;font-size:13px;color:var(--danger);">Error al buscar</div>';
        }
    }

    function renderResults(results, q) {
        if (!results || results.length === 0) {
            dropdown.innerHTML = '<div style="padding:20px 16px;text-align:center;font-size:13px;color:var(--text-muted);"><i class="bx bx-search-alt" style="font-size:28px;display:block;margin-bottom:6px;opacity:.4;"></i>Sin resultados para <strong>' + escHtml(q) + '</strong></div>';
            return;
        }

        let html = '<div style="padding:8px 16px 6px;font-size:10px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;border-bottom:1px solid var(--border-color);">' + results.length + ' resultado' + (results.length !== 1 ? 's' : '') + ' para &ldquo;' + escHtml(q) + '&rdquo;</div>';

        results.forEach(function(r) {
            html += '<a href="' + r.url + '" style="display:block;padding:10px 16px;border-bottom:1px solid var(--border-color);text-decoration:none;color:inherit;" onmouseover="this.style.background=\'var(--bg-body)\'" onmouseout="this.style.background=\'\'">'
                + '<div style="display:flex;align-items:center;gap:10px;">'
                + '<div style="width:32px;height:32px;border-radius:8px;flex-shrink:0;background:' + r.color + '22;display:flex;align-items:center;justify-content:center;font-size:16px;color:' + r.color + ';"><i class="bx ' + r.icon + '"></i></div>'
                + '<div style="flex:1;min-width:0;">'
                + '<div style="font-size:13px;font-weight:600;color:var(--text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + escHtml(r.texto) + '</div>'
                + '<div style="font-size:11px;color:var(--text-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + escHtml(r.sub) + '</div>'
                + '</div>'
                + '<span style="font-size:10px;font-weight:600;padding:2px 8px;border-radius:20px;background:' + r.color + '22;color:' + r.color + ';flex-shrink:0;">' + escHtml(r.tipo) + '</span>'
                + '</div></a>';
        });

        dropdown.innerHTML = html;
    }
})();

// ── Exportación global ────────────────────────────────────────────────────────
function toggleFechasExportModal(modulo) {
    const periodo = document.getElementById('exp-' + modulo + '-periodo');
    const fechas  = document.getElementById('exp-' + modulo + '-fechas');
    if (periodo && fechas) {
        fechas.style.display = periodo.value === 'rango' ? 'block' : 'none';
    }
}

function exportarModulo(modulo, formato, baseUrl) {
    let url = baseUrl + (baseUrl.includes('?') ? '&' : '?') + 'formato=' + formato;

    const periodoEl = document.getElementById('exp-' + modulo + '-periodo');
    if (periodoEl) {
        const periodo = periodoEl.value;
        url += '&periodo=' + periodo;
        if (periodo === 'rango') {
            const fi = document.getElementById('exp-' + modulo + '-fi');
            const ff = document.getElementById('exp-' + modulo + '-ff');
            if (fi && ff) {
                if (!fi.value || !ff.value) { showToast('Selecciona ambas fechas.', 'error'); return; }
                url += '&fecha_inicio=' + fi.value + '&fecha_fin=' + ff.value;
            }
        }
    }

    window.location.href = url;
    closeModal('modal-export-' + modulo);
    showToast('Descargando ' + formato.toUpperCase() + '...', 'success');
}

// ── Acordeón del sidebar ─────────────────────────────────────────────────────
function toggleGroup(id) {
    const el = document.getElementById(id);
    if (!el) return;
    const wasOpen = el.classList.contains('open');

    // Comportamiento de acordeón: cerrar los demás grupos si vamos a abrir este
    if (!wasOpen) {
        document.querySelectorAll('.side-menu-group.open').forEach(function(group) {
            if (group.id !== id) {
                group.classList.remove('open');
                try { localStorage.setItem('sg-' + group.id, '0'); } catch(e) {}
            }
        });
    }

    el.classList.toggle('open', !wasOpen);
    try { localStorage.setItem('sg-' + id, !wasOpen ? '1' : '0'); } catch(e) {}
}

// Restaurar estado desde localStorage (sin flash)
(function() {
    const groups = ['grp-clinica','grp-documentos','grp-laboratorio','grp-inventario','grp-comunicacion','grp-reportes','grp-admin'];
    groups.forEach(function(id) {
        const el  = document.getElementById(id);
        if (!el) return;
        // Si ya tiene 'open' (php activo), mantenerlo; si no, revisar localStorage
        if (!el.classList.contains('open')) {
            if (localStorage.getItem('sg-' + id) === '1') {
                el.classList.add('open');
            }
        } else {
            // Guardar estado activo en localStorage
            localStorage.setItem('sg-' + id, '1');
        }
    });
})();

// Iniciar polling
pollBadges();
setInterval(pollBadges, 30000);

</script>
@stack('scripts')
</body>
</html>
