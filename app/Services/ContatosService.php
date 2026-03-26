<?php

namespace App\Services;

use App\Models\Contato;
use App\Models\Mensagem;
use Illuminate\Support\Facades\Http;

class ContatosService
{
    public static function getUltimosContatos()
    {
        // Subquery: última mensagem por contato
        $lastMessageTimestamps = \App\Models\Contato::select(
            'contatos.id',
            \Illuminate\Support\Facades\DB::raw("MAX(STR_TO_DATE(mensagems.timestamp, '%d/%m/%Y %H:%i')) as last_message_timestamp")
        )
            ->join('mensagems', 'contatos.wa_id', '=', 'mensagems.from')
            ->groupBy('contatos.id')
            ->toBase();

        return \App\Models\Contato::query()
            ->with(['mensagens' => function ($query) {
                $query->orderBy('timestamp', 'desc')->limit(50);
            }])
            ->whereHas('mensagens')
            ->joinSub($lastMessageTimestamps, 'last_message_timestamps', function ($join) {
                $join->on('contatos.id', '=', 'last_message_timestamps.id');
            })
            ->orderByDesc('last_message_timestamps.last_message_timestamp')
            ->select('contatos.*')
            ->get();
    }

}
