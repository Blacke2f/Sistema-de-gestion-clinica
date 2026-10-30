<?php

namespace App\Models;

/**
 * Modelo de Paciente
 *
 * Representa un paciente registrado en la clínica.
 * Almacena datos personales, clínicos y de contacto.
 * Cada paciente puede tener múltiples consultas asociadas.
 *
 * Tabla en BD: pacientes
 * @version 2.5
 * @package App\Models
 */

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Paciente extends Model
{
    use SoftDeletes;

    // Nombre de la tabla en la base de datos
    protected $table = 'pacientes';

    // Campos permitidos para asignación masiva
    protected $fillable = [
        'nombre',           // Primer nombre del paciente
        'apellido',         // Apellido del paciente
        'cedula',           // Número de cédula (único en el sistema)
        'telefono',         // Teléfono de contacto (opcional)
        'email',            // Correo electrónico (opcional)
        'direccion',        // Dirección de residencia (opcional)
        'fecha_nacimiento', // Fecha de nacimiento para calcular edad
        'genero',           // Género: M (Masculino), F (Femenino), Otro
        'tipo_sangre',      // Tipo de sangre: O+, O-, A+, A-, B+, B-, AB+, AB-
        'alergias',         // Alergias conocidas del paciente (texto libre)
    ];

    // Conversión automática de tipos al leer de la BD
    protected $casts = [
        'fecha_nacimiento' => 'date', // Se convierte a objeto Carbon (solo fecha)
    ];

    // ══════════════════════════════════════════════════════════════════════════
    // RELACIONES CON OTROS MODELOS
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Consultas médicas del paciente.
     * Un paciente puede tener muchas consultas a lo largo del tiempo.
     * Al eliminar un paciente, sus consultas se eliminan en cascada (ver migración).
     */
    public function consultas()
    {
        return $this->hasMany(Consulta::class, 'paciente_id');
    }

    /**
     * Citas programadas del paciente.
     */
    public function citas()
    {
        return $this->hasMany(Cita::class, 'paciente_id');
    }

    /**
     * Signos vitales registrados del paciente.
     */
    public function signosVitales()
    {
        return $this->hasMany(SignoVital::class, 'paciente_id');
    }

    /**
     * Recetas emitidas al paciente.
     */
    public function recetas()
    {
        return $this->hasMany(Receta::class, 'paciente_id');
    }

    /**
     * Calcula la edad del paciente en años.
     */
    public function getEdadAttribute(): ?int
    {
        return $this->fecha_nacimiento ? $this->fecha_nacimiento->age : null;
    }

    // ══════════════════════════════════════════════════════════════════════════
    // ACCESSORS (atributos calculados)
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Nombre completo del paciente (nombre + apellido).
     * Uso: $paciente->nombre_completo
     * Ejemplo: "Juan Pérez"
     */
    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombre} {$this->apellido}";
    }

    /**
     * Texto legible del género del paciente.
     * Convierte el código de una letra al texto completo.
     * Uso: $paciente->genero_texto
     * Ejemplo: 'M' → 'Masculino', 'F' → 'Femenino'
     */
    public function getGeneroTextoAttribute(): string
    {
        return match ($this->genero) {
            'M'     => 'Masculino',
            'F'     => 'Femenino',
            default => 'Otro',
        };
    }

    // ══════════════════════════════════════════════════════════════════════════
    // SCOPES (filtros reutilizables)
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Busca pacientes por nombre, apellido o cédula.
     * Usa LIKE para búsqueda parcial (no exacta).
     *
     * @param string $term Término de búsqueda
     * Uso: Paciente::search('Juan')->get()
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('nombre',   'like', "%{$term}%")  // busca en nombre
              ->orWhere('apellido', 'like', "%{$term}%") // o en apellido
              ->orWhereRaw("CONCAT(nombre, ' ', apellido) LIKE ?", ["%{$term}%"])
              ->orWhere('telefono', 'like', "%{$term}%")
              ->orWhere('cedula',   'like', "%{$term}%"); // o en cédula
        });
    }
}
