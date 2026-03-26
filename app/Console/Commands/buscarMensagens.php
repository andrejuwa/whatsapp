<?php

namespace App\Console\Commands;

use App\Models\Contato;
use App\Models\Mensagem;
use App\Services\AtualizarRegistrosService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

#[Signature('app:buscar-mensagens')]
#[Description('Command description')]
class buscarMensagens extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $ultimaMensagem = Mensagem::query()->latest()->first();

        $total =  AtualizarRegistrosService::atualizarMensagens($ultimaMensagem->whatsapp_id);

        $this->info('Mensagens sincronizados! Total: ' . $total);
    }
}
