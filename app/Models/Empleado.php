<?php

namespace App\Models;

/**
 * Modelo de Empleado (Nómina)
 * Representa al personal médico y administrativo de la clínica.
 * @version 2.5
 */

use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    protected $table = 'nomina';

    protected $fillable = [
        'nombre',
        'apellido',
        'cedula',
        'cargo',
        'departamento',
        'salario',
        'fecha_ingreso',
        'telefono',
        'email',
        'estado',
    ];

    protected $casts = [
        'salario'       => 'decimal:2',
        'fecha_ingreso' => 'date',
    ];

    // Relationships
    public function consultas()
    {
        return $this->hasMany(Consulta::class, 'empleado_id');
    }

    // Accessors
    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombre} {$this->apellido}";
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopeMedicos($query)
    {
        $cargos = [
            'Médico General', 'Pediatra', 'Cardiólogo', 'Cirujano',
            'Ginecólogo', 'Obstetra', 'Neurólogo', 'Traumatólogo',
            'Oftalmólogo', 'Otorrinolaringólogo', 'Dermatólogo', 'Urólogo',
            'Psiquiatra', 'Anestesiólogo', 'Radiólogo', 'Patólogo',
            'Oncólogo', 'Endocrinólogo', 'Gastroenterólogo', 'Nefrólogo',
            'Neumólogo', 'Reumatólogo', 'Hematólogo', 'Infectólogo', 'Internista',
        ];

        return $query->where('estado', 'activo')
                     ->where(function ($q) use ($cargos) {
                         foreach ($cargos as $cargo) {
                             $q->orWhere('cargo', 'like', "%{$cargo}%");
                         }
                     });
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('nombre', 'like', "%{$term}%")
              ->orWhere('apellido', 'like', "%{$term}%")
              ->orWhere('cedula', 'like', "%{$term}%");
        });
    }
}
