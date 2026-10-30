<?php

namespace App\Http\Controllers;

/**
 * Controlador de Autenticación
 *
 * Maneja el inicio de sesión, cierre de sesión y registro de nuevos usuarios.
 * Los usuarios registrados quedan deshabilitados (activo=false) hasta que
 * el administrador los active desde el módulo de Usuarios.
 *
 * @version 2.5
 * @package App\Http\Controllers
 */

use App\Models\Bitacora;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Muestra el formulario de inicio de sesión.
     * Si el usuario ya está autenticado, lo redirige al dashboard.
     */
    public function showLogin()
    {
        // Si ya hay sesión activa, no mostrar el login — ir al dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Procesa el formulario de inicio de sesión.
     *
     * Flujo:
     * 1. Valida que username y password no estén vacíos
     * 2. Busca el usuario por username en la BD
     * 3. Verifica que la cuenta esté activa
     * 4. Verifica que la contraseña sea correcta (bcrypt)
     * 5. Inicia sesión y regenera el token de sesión (previene session fixation)
     * 6. Registra el acceso en la bitácora
     *
     * @param Request $request Datos del formulario: username, password, remember
     */
    public function login(Request $request)
    {
        // Paso 1: Validar que los campos no estén vacíos
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'El usuario es requerido.',
            'password.required' => 'La contraseña es requerida.',
        ]);

        // Rate limiting: máximo 5 intentos por IP en 1 minuto
        $throttleKey = 'login:' . Str::lower($request->username) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'username' => "Demasiados intentos fallidos. Intente de nuevo en {$seconds} segundos.",
            ])->withInput();
        }

        // Paso 2: Buscar el usuario por su nombre de usuario
        $usuario = Usuario::where('username', $request->username)->first();

        if (! $usuario) {
            RateLimiter::hit($throttleKey, 60);
            return back()->withErrors(['username' => 'Usuario no encontrado.'])->withInput();
        }

        // Paso 3: Verificar que la cuenta esté habilitada
        if (! $usuario->activo) {
            return back()->withErrors([
                'username' => 'Su cuenta está desactivada. Contacte al administrador.'
            ])->withInput();
        }

        // Paso 4: Verificar la contraseña
        if (! Hash::check($request->password, $usuario->password)) {
            RateLimiter::hit($throttleKey, 60);
            return back()->withErrors(['password' => 'Contraseña incorrecta.'])->withInput();
        }

        // Login exitoso — limpiar intentos fallidos
        RateLimiter::clear($throttleKey);

        // Paso 5: Iniciar sesión
        Auth::login($usuario, $request->boolean('remember'));
        $request->session()->regenerate();

        // Actualizar último acceso
        $usuario->update(['ultimo_acceso' => now()]);

        // Paso 6: Registrar en bitácora
        Bitacora::registrar($usuario->id, 'INICIO SESIÓN', 'usuarios', $usuario->id);

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Cierra la sesión del usuario autenticado.
     *
     * Flujo:
     * 1. Registra el cierre de sesión en la bitácora
     * 2. Cierra la sesión de Laravel
     * 3. Invalida la sesión actual (elimina todos los datos)
     * 4. Regenera el token CSRF para la siguiente sesión
     * 5. Redirige al login usando la URL dinámica del host actual
     *
     * @param Request $request
     */
    public function logout(Request $request)
    {
        // Guardar el ID antes de cerrar sesión (después Auth::id() retorna null)
        $userId = Auth::id();

        // Registrar el cierre de sesión en la bitácora si había usuario activo
        if ($userId) {
            Bitacora::registrar($userId, 'CIERRE SESIÓN', 'usuarios', $userId);
        }

        // Cerrar la sesión de Laravel (elimina el usuario autenticado)
        Auth::logout();

        // Invalidar completamente la sesión actual (elimina todos los datos guardados)
        $request->session()->invalidate();

        // Regenerar el token CSRF para proteger la siguiente sesión
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    // ══════════════════════════════════════════════════════════════════════════
    // REGISTRO DE NUEVOS USUARIOS
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Muestra el formulario de registro de nuevo usuario.
     * Si ya hay sesión activa, redirige al dashboard.
     */
    public function showRegister()
    {
        // Si ya está autenticado, no mostrar el registro
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.register');
    }

    /**
     * Procesa el formulario de registro de nuevo usuario.
     *
     * El usuario se crea con activo=false (deshabilitado).
     * Los administradores reciben una notificación para revisar y activar la cuenta.
     * Solo se permiten roles: recepcionista y médico (no admin desde el registro público).
     *
     * @param Request $request Datos del formulario de registro
     */
    public function register(Request $request)
    {
        // Validar todos los campos del formulario de registro
        $request->validate([
            'nombre_completo' => 'required|string|max:150',
            'username'        => 'required|string|max:50|unique:usuarios,username',
            'email'           => 'required|email|max:100|unique:usuarios,email',
            // Solo roles no-admin se pueden registrar públicamente
            'rol'             => 'required|in:recepcionista,medico,enfermera,farmaceutico,laboratorista',
            // La contraseña debe confirmarse (campo password_confirmation)
            'password'        => 'required|string|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
        ], [
            'nombre_completo.required' => 'El nombre completo es requerido.',
            'username.required'        => 'El nombre de usuario es requerido.',
            'username.unique'          => 'Ese nombre de usuario ya está en uso.',
            'email.required'           => 'El correo electrónico es requerido.',
            'email.unique'             => 'Ese correo ya está registrado.',
            'rol.required'             => 'Selecciona un rol.',
            'password.required'        => 'La contraseña es requerida.',
            'password.min'             => 'La contraseña debe tener al menos 8 caracteres.',
            'password.regex'           => 'La contraseña debe tener al menos una mayúscula, una minúscula y un número.',
            'password.confirmed'       => 'Las contraseñas no coinciden.',
        ]);

        // Crear el usuario con la cuenta DESHABILITADA
        // El admin debe activarla manualmente desde el módulo de Usuarios
        $usuario = Usuario::create([
            'nombre_completo' => $request->nombre_completo,
            'username'        => $request->username,
            'email'           => $request->email,
            'rol'             => $request->rol,
            'password'        => Hash::make($request->password), // hashear con bcrypt
            'activo'          => false, // IMPORTANTE: deshabilitado hasta que admin lo active
        ]);

        // Notificar a todos los administradores activos sobre la nueva solicitud
        // Para que puedan revisar y activar la cuenta
        $admins = Usuario::where('rol', 'admin')->where('activo', true)->get();
        foreach ($admins as $admin) {
            \App\Models\Notificacion::notificarUsuario(
                usuarioId: $admin->id,
                tipo:      'registro_pendiente',
                titulo:    'Nueva Solicitud de Acceso',
                mensaje:   "{$usuario->nombre_completo} ({$usuario->username}) solicitó acceso como " . ucfirst($usuario->rol),
                icono:     'bx-user-plus',
                color:     'warning',
                url:       '/usuarios' // enlace directo al módulo de usuarios
            );
        }

        // Retornar al formulario con mensaje de éxito
        // El usuario verá el mensaje pero no podrá iniciar sesión hasta ser activado
        return back()->with('success', '¡Solicitud enviada! Un administrador revisará tu cuenta y te habilitará el acceso pronto.');
    }
}
