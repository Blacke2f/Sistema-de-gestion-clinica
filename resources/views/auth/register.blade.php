<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SISGECLIN — Solicitar Acceso</title>
    <link rel="icon" type="image/png" href="{{ asset('img/image-Photoroom.png') }}">
    <link rel="shortcut icon" href="{{ asset('img/image-Photoroom.ico') }}">
    <link href="{{ asset('vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
    <style>
        *, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }
        html, body { height:100%; font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif; background:#1a2234; }
        ul, ol { list-style:none; }
        a { text-decoration:none; color:inherit; }

        .page { display:flex; min-height:100vh; }

        /* ── Columna izquierda ── */
        .col-left {
            width:42%;
            background:linear-gradient(160deg,#1a2234 0%,#263046 55%,#0d4a8a 100%);
            display:flex; flex-direction:column; justify-content:center;
            padding:60px 50px; color:#fff; position:relative; overflow:hidden;
        }
        .col-left::before {
            content:''; position:absolute; width:320px; height:320px;
            border-radius:50%; background:rgba(13,110,253,.12); top:-80px; right:-80px;
        }
        .col-left::after {
            content:''; position:absolute; width:220px; height:220px;
            border-radius:50%; background:rgba(255,255,255,.04); bottom:-60px; left:-60px;
        }
        .brand-row { display:flex; align-items:center; gap:16px; margin-bottom:36px; position:relative; z-index:2; }
        .brand-logo { width:68px; height:68px; border-radius:50%; border:3px solid rgba(255,255,255,.25); object-fit:contain; background:rgba(255,255,255,.08); padding:6px; flex-shrink:0; }
        .brand-text h1 { font-size:28px; font-weight:800; letter-spacing:2px; margin-bottom:4px; }
        .brand-text p  { font-size:13px; opacity:.7; color:#cce0ff; }

        .feat-list { position:relative; z-index:2; border-top:1px solid rgba(255,255,255,.1); padding-top:20px; }
        .feat-list li { display:flex; align-items:center; gap:14px; padding:9px 0; font-size:14px; color:rgba(255,255,255,.85); border-bottom:1px solid rgba(255,255,255,.07); }
        .feat-list li:last-child { border-bottom:none; }
        .feat-list li i { font-size:20px; color:#4facfe; width:24px; text-align:center; flex-shrink:0; }

        /* ── Columna derecha ── */
        .col-right { flex:1; background:#fff; display:flex; align-items:center; justify-content:center; padding:40px 60px; overflow-y:auto; }
        .form-box { width:100%; max-width:390px; }

        .form-title    { font-size:24px; font-weight:700; color:#1a2234; margin-bottom:5px; }
        .form-subtitle { font-size:13px; color:#888; margin-bottom:16px; }

        /* Badge pendiente */
        .badge-pend {
            display:inline-flex; align-items:center; gap:7px;
            background:#fff3cd; color:#856404; border:1px solid #ffecb5;
            border-radius:20px; padding:6px 14px; font-size:12px; font-weight:600; margin-bottom:16px;
        }

        /* Campos */
        .fld { margin-bottom:15px; }
        .fld label { display:block; font-size:13px; font-weight:600; color:#444; margin-bottom:5px; }
        .fld-wrap { position:relative; }
        .fld-wrap .ico { position:absolute; left:13px; top:50%; transform:translateY(-50%); font-size:18px; color:#aaa; pointer-events:none; z-index:1; }
        .fld-wrap input,
        .fld-wrap select {
            width:100%; padding:11px 14px 11px 42px;
            border:2px solid #e4e4e4; border-radius:10px;
            font-size:14px; color:#333; background:#f9f9f9;
            outline:none; transition:border-color .2s,background .2s;
            -webkit-appearance:none; appearance:none;
        }
        .fld-wrap input:focus,
        .fld-wrap select:focus { border-color:#0d6efd; background:#fff; }
        .fld-wrap input.err,
        .fld-wrap select.err { border-color:#dc3545; }
        .btn-eye { position:absolute; right:12px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; color:#aaa; font-size:18px; padding:0; z-index:1; }
        .btn-eye:hover { color:#0d6efd; }

        /* Botón */
        .btn-go {
            display:block; width:100%; padding:13px;
            background:linear-gradient(135deg,#198754 0%,#157347 100%);
            color:#fff; border:none; border-radius:10px;
            font-size:15px; font-weight:600; cursor:pointer;
            transition:opacity .2s,transform .1s; letter-spacing:.3px; margin-top:4px;
        }
        .btn-go:hover  { opacity:.9; transform:translateY(-1px); }
        .btn-go:active { transform:translateY(0); }

        /* Alertas */
        .alert { padding:12px 16px; border-radius:8px; margin-bottom:16px; font-size:13px; line-height:1.5; }
        .alert p { margin:0; }
        .alert-danger  { background:#fde8e8; color:#c0392b; border:1px solid #f5c6cb; }
        .alert-success { background:#d1e7dd; color:#0f5132; border:1px solid #badbcc; }

        /* Link */
        .switch-link { text-align:center; font-size:13px; color:#888; margin-top:18px; }
        .switch-link a { color:#0d6efd; font-weight:600; }
        .switch-link a:hover { text-decoration:underline; }

        .pg-footer { text-align:center; margin-top:20px; font-size:11px; color:#bbb; }

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
            <li><i class='bx bx-shield-check'></i> Acceso controlado por administrador</li>
            <li><i class='bx bx-user-check'></i> Tu cuenta será revisada y activada</li>
            <li><i class='bx bx-bell'></i> El admin recibirá tu solicitud</li>
            <li><i class='bx bx-lock'></i> Datos protegidos y seguros</li>
            <li><i class='bx bx-time-five'></i> Activación en menos de 24 horas</li>
        </ul>
    </div>

    {{-- ── Columna derecha ── --}}
    <div class="col-right">
        <div class="form-box">

            <h2 class="form-title">Solicitar Acceso</h2>
            <p class="form-subtitle">Completa el formulario. Un administrador activará tu cuenta.</p>

            <div class="badge-pend">
                <i class='bx bx-time-five'></i>
                Tu cuenta quedará pendiente de aprobación
            </div>

            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
                </div>
            @endif
            @if(session('success'))
                <div class="alert alert-success"><p>{{ session('success') }}</p></div>
            @endif

            <form method="POST" action="{{ route('register.post') }}">
                @csrf

                <div class="fld">
                    <label>Nombre Completo *</label>
                    <div class="fld-wrap">
                        <i class='bx bx-id-card ico'></i>
                        <input type="text" name="nombre_completo" value="{{ old('nombre_completo') }}"
                               placeholder="Tu nombre completo"
                               class="{{ $errors->has('nombre_completo') ? 'err' : '' }}" required>
                    </div>
                </div>

                <div class="fld">
                    <label>Nombre de Usuario *</label>
                    <div class="fld-wrap">
                        <i class='bx bx-user ico'></i>
                        <input type="text" name="username" value="{{ old('username') }}"
                               placeholder="Ej: jperez"
                               class="{{ $errors->has('username') ? 'err' : '' }}" required>
                    </div>
                </div>

                <div class="fld">
                    <label>Correo Electrónico *</label>
                    <div class="fld-wrap">
                        <i class='bx bx-envelope ico'></i>
                        <input type="email" name="email" value="{{ old('email') }}"
                               placeholder="correo@ejemplo.com"
                               class="{{ $errors->has('email') ? 'err' : '' }}" required>
                    </div>
                </div>

                <div class="fld">
                    <label>Rol Solicitado *</label>
                    <div class="fld-wrap">
                        <i class='bx bx-briefcase ico'></i>
                        <select name="rol" class="{{ $errors->has('rol') ? 'err' : '' }}" required>
                            <option value="">Seleccionar rol</option>
                            <option value="recepcionista" {{ old('rol')==='recepcionista'?'selected':'' }}>Recepcionista</option>
                            <option value="medico"        {{ old('rol')==='medico'       ?'selected':'' }}>Médico</option>
                        </select>
                    </div>
                </div>

                <div class="fld">
                    <label>Contraseña *</label>
                    <div class="fld-wrap">
                        <i class='bx bx-lock ico'></i>
                        <input type="password" name="password" placeholder="Mínimo 6 caracteres"
                               class="{{ $errors->has('password') ? 'err' : '' }}" required>
                        <button type="button" class="btn-eye" onclick="togglePwd('pwd1','eye1')">
                            <i class='bx bx-show' id="eye1"></i>
                        </button>
                    </div>
                </div>

                <div class="fld">
                    <label>Confirmar Contraseña *</label>
                    <div class="fld-wrap">
                        <i class='bx bx-lock-alt ico'></i>
                        <input type="password" id="pwd1" name="password_confirmation"
                               placeholder="Repite tu contraseña" required>
                        <button type="button" class="btn-eye" onclick="togglePwd('pwd2','eye2')">
                            <i class='bx bx-show' id="eye2"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-go">
                    <i class='bx bx-send'></i>&nbsp; Enviar Solicitud
                </button>
            </form>

            <div class="switch-link">
                ¿Ya tienes cuenta? <a href="{{ route('login') }}">Iniciar sesión</a>
            </div>

            <div class="pg-footer">
                © {{ date('Y') }} SISGECLIN v2.5 — Todos los derechos reservados
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
