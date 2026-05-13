<?php

namespace App\Models;

/**
 * Alias de compatibilidad para el modelo Usuario.
 *
 * Laravel espera un modelo User en App\Models por defecto.
 * Este archivo simplemente extiende el modelo Usuario del sistema
 * para mantener compatibilidad con cualquier referencia interna del framework.
 *
 * El modelo real con toda la lógica está en: App\Models\Usuario
 *
 * @version 2.5
 * @package App\Models
 */
class User extends Usuario
{
    // Hereda todo de Usuario — no agregar lógica aquí
}
