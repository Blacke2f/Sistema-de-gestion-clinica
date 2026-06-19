<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SISGECLIN — Iniciar Sesión</title>
    <link rel="icon" type="image/png" href="{{ asset('img/image-Photoroom.png') }}">
    <link rel="shortcut icon" href="{{ asset('img/image-Photoroom.ico') }}">
    <link href="{{ asset('vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
    <style>
        /* Reset total — independiente del sistema */
        *, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }
        html, body { height:100%; font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif; background:#1a2234; }
        ul, ol { list-style:none; }
        a { text-decoration:none; color:inherit; }

        /* Layout de dos columnas */
        .page { display:flex; min-height:100vh; }

        /* ── Columna izquierda ── */
        .col-left {
            width:42%;
            background:linear-gradient(160deg,#1a2234 0%,#263046 55%,#0d4a8a 100%);
            display:flex;
            flex-direction:column;
            justify-content:center;
            padding:60px 50px;
            color:#fff;
            position:relative;
            overflow:hidden;
        }
        .col-left::before {
            content:'';
            position:absolute;
            width:320px; height:320px;
            border-radius:50%;
            background:rgba(13,110,253,.12);
            top:-80px; right:-80px;
        }
        .col-left::after {
            content:'';
            position:absolute;
            width:220px; height:220px;
            border-radius:50%;
            background:rgba(255,255,255,.04);
            bottom:-60px; left:-60px;
        }
        .brand-row {
            display:flex;
            align-items:center;
            gap:16px;
            margin-bottom:40px;
            position:relative;
            z-index:2;
        }
        .brand-logo {
            width:68px; height:68px;
            border-radius:50%;
            border:3px solid rgba(255,255,255,.25);
            object-fit:contain;
            background:rgba(255,255,255,.08);
            padding:6px;
            flex-shrink:0;
        }
        .brand-text h1 { font-size:28px; font-weight:800; letter-spacing:2px; margin-bottom:4px; }
        .brand-text p  { font-size:13px; opacity:.7; color:#cce0ff; }

        .feat-list { position:relative; z-index:2; border-top:1px solid rgba(255,255,255,.1); padding-top:24px; }
        .feat-list li {
            display:flex;
            align-items:center;
            gap:14px;
            padding:10px 0;
            font-size:14px;
            color:rgba(255,255,255,.85);
            border-bottom:1px solid rgba(255,255,255,.07);
        }
        .feat-list li:last-child { border-bottom:none; }
        .feat-list li i { font-size:20px; color:#4facfe; width:24px; text-align:center; flex-shrink:0; }

        /* ── Columna derecha ── */
        .col-right {
            flex:1;
            background:#fff;
            display:flex;
            align-items:center;
            justify-content:center;
            padding:50px 60px;
            overflow-y:auto;
        }
        .form-box { width:100%; max-width:380px; }

        .form-title    { font-size:26px; font-weight:700; color:#1a2234; margin-bottom:6px; }
        .form-subtitle { font-size:14px; color:#888; margin-bottom:28px; }

        /* Campos */
        .fld { margin-bottom:18px; }
        .fld label { display:block; font-size:13px; font-weight:600; color:#444; margin-bottom:6px; }
        .fld-wrap { position:relative; }
        .fld-wrap .ico {
            position:absolute; left:13px; top:50%; transform:translateY(-50%);
            font-size:18px; color:#aaa; pointer-events:none; z-index:1;
        }
        .fld-wrap input {
            width:100%; padding:12px 14px 12px 42px;
            border:2px solid #e4e4e4; border-radius:10px;
            font-size:14px; color:#333; background:#f9f9f9;
            outline:none; transition:border-color .2s,background .2s;
        }
        .fld-wrap input:focus { border-color:#0d6efd; background:#fff; }
        .fld-wrap input.err  { border-color:#dc3545; }
        .btn-eye {
            position:absolute; right:12px; top:50%; transform:translateY(-50%);
            background:none; border:none; cursor:pointer; color:#aaa; font-size:18px; padding:0; z-index:1;
        }
        .btn-eye:hover { color:#0d6efd; }

        /* Opciones */
        .opts { display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; font-size:13px; color:#666; }
        .opts label { display:flex; align-items:center; gap:7px; cursor:pointer; }

        /* Botón */
        .btn-go {
            display:block; width:100%; padding:13px;
            background:linear-gradient(135deg,#0d6efd 0%,#0b5ed7 100%);
            color:#fff; border:none; border-radius:10px;
            font-size:15px; font-weight:600; cursor:pointer;
            transition:opacity .2s,transform .1s; letter-spacing:.3px;
        }
        .btn-go:hover  { opacity:.9; transform:translateY(-1px); }
        .btn-go:active { transform:translateY(0); }

        /* Alertas */
        .alert { padding:12px 16px; border-radius:8px; margin-bottom:18px; font-size:13px; line-height:1.5; }
        .alert p { margin:0; }
        .alert-danger  { background:#fde8e8; color:#c0392b; border:1px solid #f5c6cb; }
        .alert-success { background:#d1e7dd; color:#0f5132; border:1px solid #badbcc; }

        /* Separador */
        .sep { display:flex; align-items:center; gap:12px; margin:20px 0; color:#ccc; font-size:12px; }
        .sep::before,.sep::after { content:''; flex:1; height:1px; background:#e8e8e8; }

        /* Link registro */
        .switch-link { text-align:center; font-size:13px; color:#888; margin-top:18px; }
        .switch-link a { color:#0d6efd; font-weight:600; }
        .switch-link a:hover { text-decoration:underline; }

        /* Footer */
        .pg-footer { text-align:center; margin-top:24px; font-size:11px; color:#bbb; }

        /* Responsive */
        @media(max-width:820px){
            .col-left { display:none; }
            .col-right { padding:40px 24px; }
        }
    </style>
</head>
<body>
<div class="page">

    {{-- ── Columna izquierda ── --}}
    <div class="col-left">
        <div class="brand-row">
            <img src="{{ asset('img/image-Photoroom.png') }}" alt="Logo" class="brand-logo">
            <div class="brand-text">
                <h1>SISGECLIN</h1>
                <p>Sistema de Gestión Clínica</p>
            </div>
        </div>
        <ul class="feat-list">
            <li><i class='bx bxs-user-detail'></i> Gestión completa de pacientes</li>
            <li><i class='bx bx-clipboard'></i> Control de consultas médicas</li>
            <li><i class='bx bxs-capsule'></i> Inventario de medicamentos</li>
            <li><i class='bx bxs-group'></i> Administración de nómina</li>
            <li><i class='bx bx-transfer'></i> Movimientos de inventario</li>
            <li><i class='bx bxs-file-pdf'></i> Exportación PDF / Excel / CSV</li>
        </ul>
    </div>

    {{-- ── Columna derecha ── --}}
    <div class="col-right">
        <div class="form-box">

            <h2 class="form-title">Bienvenido</h2>
            <p class="form-subtitle">Ingresa tus credenciales para continuar</p>

            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
                </div>
            @endif
            @if(session('status'))
                <div class="alert alert-success"><p>{{ session('status') }}</p></div>
            @endif

            <form method="POST" action="{{ route('login.post') }}">
                @csrf

                <div class="fld">
                    <label for="username">Usuario</label>
                    <div class="fld-wrap">
                        <i class='bx bx-user ico'></i>
                        <input type="text" id="username" name="username"
                               value="{{ old('username') }}"
                               placeholder="Ingresa tu usuario"
                               class="{{ $errors->has('username') ? 'err' : '' }}"
                               autocomplete="username" required autofocus>
                    </div>
                </div>

                <div class="fld">
                    <label for="password">Contraseña</label>
                    <div class="fld-wrap">
                        <i class='bx bx-lock ico'></i>
                        <input type="password" id="password" name="password"
                               placeholder="Ingresa tu contraseña"
                               class="{{ $errors->has('password') ? 'err' : '' }}"
                               autocomplete="current-password" required>
                        <button type="button" class="btn-eye" onclick="togglePwd('password','eye1')">
                            <i class='bx bx-show' id="eye1"></i>
                        </button>
                    </div>
                </div>

                <div class="opts">
                    <label><input type="checkbox" name="remember"> Recordarme</label>
                </div>

                <button type="submit" class="btn-go">
                    <i class='bx bx-log-in'></i>&nbsp; Iniciar Sesión
                </button>
            </form>

            <div class="sep">o</div>

            <div class="switch-link">
                ¿No tienes cuenta?
                <a href="{{ route('register') }}">Solicitar acceso</a>
            </div>

            <div class="pg-footer">
                © {{ date('Y') }} SISGECLIN v2.5.0 — Todos los derechos reservados
            </div>

        </div>
    </div>

</div>
<script>
function togglePwd(id, ico) {
    var i = document.getElementById(id);
    var e = document.getElementById(ico);
    if (i.type === 'password') { i.type = 'text'; e.className = 'bx bx-hide'; }
    else { i.type = 'password'; e.className = 'bx bx-show'; }
}
</script>
</body>
</html>
