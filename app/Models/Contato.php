<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contato extends Model
{
    protected $table = 'contatos';

    protected $fillable = [
        'wa_id',
        'name',
    ];

    // RELACIONAMENTO
    public function mensagens()
    {
        return $this->hasMany(Mensagem::class, 'from', 'wa_id');
    }
}
