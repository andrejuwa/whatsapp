<?php

namespace App\Services;

use App\Models\Contato;
use App\Models\Mensagem;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ContatosService
{
    public static function getUltimosContatos()
    {
        $hojeMenos24h = Carbon::now()->subDay()->format('Y-m-d H:i:s');

        // Subquery: última mensagem por contato nas últimas 24h
        $lastMessageTimestamps = Contato::select(
            'contatos.id',
            DB::raw("MAX(STR_TO_DATE(mensagems.timestamp, '%d/%m/%Y %H:%i')) as last_message_timestamp")
        )
            ->join('mensagems', 'contatos.wa_id', '=', 'mensagems.from')
            ->groupBy('contatos.id')
            ->havingRaw("MAX(STR_TO_DATE(mensagems.timestamp, '%d/%m/%Y %H:%i')) >= ?", [$hojeMenos24h])
            ->toBase();

        return Contato::query()
            ->with(['mensagens' => function ($query) {
                $query->orderBy('timestamp', 'desc')->limit(50);
            }])
            ->whereHas('mensagens', function ($query) use ($hojeMenos24h) {
                $query->whereRaw("STR_TO_DATE(timestamp, '%d/%m/%Y %H:%i') >= ?", [$hojeMenos24h]);
            })
            ->joinSub($lastMessageTimestamps, 'last_message_timestamps', function ($join) {
                $join->on('contatos.id', '=', 'last_message_timestamps.id');
            })
            ->orderByDesc('last_message_timestamps.last_message_timestamp')
            ->select('contatos.*')
            ->limit(10)
            ->get();
    }
}
