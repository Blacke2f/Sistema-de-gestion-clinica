@extends('layouts.app')

@section('title', 'Mi Perfil')
@section('nav-title', 'Mi Perfil')

@section('content')
<div class="head-title">
    <div class="left">
        <h1>Mi Perfil</h1>
        <ul class="breadcrumb">
            <li><a href="{{ route('dashboard') }}">Inicio</a></li>
            <li><i class='bx bx-chevron-right'></i></li>
            <li><a class="active" href="#">Perfil</a></li>
        </ul>
    </div>
</div>

<div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; max-width:900px;">

    {{-- Datos del perfil --}}
    <div class="table-container">
        <div class="table-header">
            <h3><i class='bx bx-user-circle'></i> Información Personal</h3>
        </div>
        <div style="padding:20px;">
            <div style="text-align:center; margin-bottom:20px;">
                <img src="" data-avatar="{{ $usuario->nombre_completo }}" data-bg="#0d6efd" data-size="80" alt="Avatar" style="border-radius:50%; width:80px; height:80px;">
                <h3 style="margin-top:10px;">{{ $usuario->nombre_completo }}</h3>
                <span class="badge badge-primary">{{ ucfirst($usuario->rol) }}</span>
            </div>

            <form id="form-perfil">
                @csrf
                <div class="form-group">
                    <label>Nombre Completo *</label>
                    <input type="text" name="nombre_completo" value="{{ $usuario->nombre_completo }}" required>
                </div>
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" value="{{ $usuario->email }}" required>
                </div>
                <div class="form-group">
                    <label>Usuario</label>
                    <input type="text" value="{{ $usuario->username }}" disabled style="opacity:0.6;">
                </div>
                <button type="submit" class="btn-primary" style="width:100%;">
                    <i class='bx bx-save'></i> Actualizar Perfil
                </button>
            </form>
        </div>
    </div>

    {{-- Cambiar contraseña --}}
    <div class="table-container">
        <div class="table-header">
            <h3><i class='bx bx-lock'></i> Cambiar Contraseña</h3>
        </div>
        <div style="padding:20px;">
            <form id="form-password">
                @csrf
                <div class="form-group">
                    <label>Contraseña Actual *</label>
                    <input type="password" name="password_actual" required placeholder="Contraseña actual">
                </div>
                <div class="form-group">
                    <label>Nueva Contraseña *</label>
                    <input type="password" name="password_nuevo" required placeholder="Mínimo 6 caracteres">
                </div>
                <div class="form-group">
                    <label>Confirmar Contraseña *</label>
                    <input type="password" name="password_nuevo_confirmation" required placeholder="Repita la nueva contraseña">
                </div>
                <button type="submit" class="btn-warning" style="width:100%;">
                    <i class='bx bx-key'></i> Cambiar Contraseña
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('form-perfil').addEventListener('submit', async function(e) {
    e.preventDefault();
    const res  = await fetch('{{ route('perfil.update') }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN, 'Accept': 'application/json' },
        body: new FormData(this)
    });
    const data = await res.json();
    if (data.success) showToast(data.message, 'success');
    else showToast(data.message || 'Error.', 'error');
});

document.getElementById('form-password').addEventListener('submit', async function(e) {
    e.preventDefault();
    const fd = new FormData(this);
    fd.set('_method', 'PUT');
    const res  = await fetch('{{ route('perfil.password') }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN, 'Accept': 'application/json' },
        body: fd
    });
    const data = await res.json();
    if (data.success) { showToast(data.message, 'success'); this.reset(); }
    else showToast(data.message || 'Error.', 'error');
});
</script>
@endpush
