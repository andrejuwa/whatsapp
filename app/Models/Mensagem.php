<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mensagem extends Model
{
    protected $table = 'mensagems';

    protected $fillable = [
        'whatsapp_id',
        'from',
        'body',
        'type',
        'enviado',
        'timestamp',
        'whatsapp_context_id',
        'flow_id',
    ];

    protected $casts = [
        'enviado' => 'boolean',
    ];

    // RELACIONAMENTO
    public function contato()
    {
        return $this->belongsTo(Contato::class, 'from', 'wa_id');
    }
}
