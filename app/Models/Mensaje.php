<?php

namespace App\Models;

/**
 * Modelo de Mensaje Interno
 * Representa un mensaje entre usuarios del sistema.
 * @version 2.5
 */

use Illuminate\Database\Eloquent\Model;

class Mensaje extends Model
{
    protected $table = 'mensajes';

    public $timestamps = false;

    protected $fillable = [
        'remitente_id',
        'destinatario_id',
        'asunto',
        'contenido',
        'leido',
        'es_masivo',
    ];

    protected $casts = [
        'leido'      => 'boolean',
        'es_masivo'  => 'boolean',
        'created_at' => 'datetime',
    ];

    // Relationships
    public function remitente()
    {
        return $this->belongsTo(Usuario::class, 'remitente_id');
    }

    public function destinatario()
    {
        return $this->belongsTo(Usuario::class, 'destinatario_id');
    }

    // Scopes
    public function scopeNoLeidos($query)
    {
        return $query->where('leido', false);
    }
}
