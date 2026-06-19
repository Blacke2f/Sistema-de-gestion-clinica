<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Ítem de Receta Médica (medicamento prescrito)
 * @version 1.0
 */
class RecetaItem extends Model
{
    protected $table = 'receta_items';

    protected $fillable = [
        'receta_id',
        'medicamento_id',
        'nombre_medicamento',
        'presentacion',
        'dosis',
        'frecuencia',
        'duracion',
        'cantidad',
        'indicaciones',
    ];

    public function receta()
    {
        return $this->belongsTo(Receta::class, 'receta_id');
    }

    public function medicamento()
    {
        return $this->belongsTo(Medicamento::class, 'medicamento_id');
    }
}
