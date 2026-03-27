<?php

namespace App\Services;

use App\Models\Contato;
use App\Models\Mensagem;
use Illuminate\Support\Facades\Http;

class AtualizarRegistrosService
{
    public static function visualizarMensagens($wa_id)
    {
        $response = Http::post("https://admin.recargahouse.site/api/api/whatsapp/confirmacaoDeLeitura/{$wa_id}");
    }
    public static function atualizarMensagens($whatsapp_id)
    {
        $response = Http::get("https://admin.recargahouse.site/api/api/whatsapp/mensagens/{$whatsapp_id}");

        $mensagens = collect($response->json())
            ->map(function ($mensagem) {
                return [
                    'whatsapp_id' => $mensagem['whatsapp_id'],
                    'from' => $mensagem['from'],
                    'body' => $mensagem['body'],
                    'type' => $mensagem['type'],
                    'enviado' => $mensagem['enviado'],
                    'timestamp' => $mensagem['timestamp'],
                    'whatsapp_context_id' => $mensagem['whatsapp_context_id'],
                ];
            })
            ->toArray();

        Mensagem::query()->upsert(
            $mensagens,
            ['whatsapp_id'], // chave única
            [
                'from',
                'body',
                'type',
                'enviado',
                'timestamp',
                'whatsapp_context_id',
            ] // campos que podem ser atualizados
        );
        return count($mensagens);
    }
    public static function atualizarContatos(){
        $response = Http::get('http://admin.recargahouse.site/api/api/whatsapp/contatos');

        $contatos = collect($response->json())
            ->map(function ($contato){
                return [
                    'wa_id' => $contato['wa_id'],
                    'name'  => $contato['name'],
                    'flow_id'  => $contato['flow_id'],
                    'mensagens_nao_lida'  => $contato['mensagens_nao_lida'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            } )
            ->toArray();

        Contato::query()->upsert(
            $contatos,
            ['wa_id'], // chave única
            ['name', 'updated_at', 'flow_id', 'mensagens_nao_lida'] // campos que podem ser atualizados
        );

    }
}
