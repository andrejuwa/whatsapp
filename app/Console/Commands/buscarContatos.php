<?php

namespace App\Console\Commands;

use App\Models\Contato;
use App\Services\AtualizarRegistrosService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;


#[Signature('app:buscar-contatos')]
#[Description('Command description')]
class buscarContatos extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        AtualizarRegistrosService::atualizarContatos();

        $this->info('Contatos sincronizados!');
    }
}
