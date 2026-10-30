<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Ítem/línea de un Gasto Médico (concepto + monto).
 * @version 1.0
 */
class GastoMedicoItem extends Model
{
    protected $table = 'gasto_medico_items';

    protected $fillable = [
        'gasto_medico_id',
        'concepto',
        'monto',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
    ];

    public function gastoMedico()
    {
        return $this->belongsTo(GastoMedico::class, 'gasto_medico_id');
    }
}
