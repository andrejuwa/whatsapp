<?php

use Livewire\Component;

new class extends Component
{
    public $contatos;
    public $contatoSelecionado;

    public function mount($waId = null)
    {
        if (!empty($waId)){
            $this->contatoSelecionado = \App\Models\Contato::query()->where('wa_id', $waId)->first();
            \App\Services\AtualizarRegistrosService::visualizarMensagens($this->contatoSelecionado->wa_id);
        }
        \App\Services\AtualizarRegistrosService::atualizarContatos();

        $ultimaMensagem = \App\Models\Mensagem::query()->latest()->first();

        $total = \App\Services\AtualizarRegistrosService::atualizarMensagens($ultimaMensagem->whatsapp_id);

        $this->carregarMensagens();
    }

    public function carregarMensagens()
    {
        $this->contatos = \App\Services\ContatosService::getUltimosContatos();
        $this->dispatch("post-updated");
    }
};
?>

<div>


    <!-- Static sidebar for desktop -->
    <div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-[32%] lg:flex-col dark:bg-gray-900" id="sidebar">
        <!-- Sidebar component, swap this element with another sidebar if you like -->
        <div class="flex grow flex-col gap-y-5 overflow-y-auto border-r border-gray-200 bg-white px-6 dark:border-white/10 dark:bg-black/10">
            <div class="flex h-16 shrink-0 items-center">
                <button
                    wire:click="carregarMensagens"
                    wire:loading.attr="disabled"
                    class="ml-auto text-xs px-3 py-1 rounded-full bg-blue-100 text-blue-700 font-medium hover:bg-blue-200">

                    <span wire:loading.remove>Atualizar</span>
                    <span wire:loading>Atualizando...</span>

                </button>
                <img src="https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=indigo&shade=600" alt="Your Company" class="h-8 w-auto dark:hidden" />
                <img src="https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=indigo&shade=500" alt="Your Company" class="h-8 w-auto not-dark:hidden" />
            </div>
            <nav class="flex flex-1 flex-col">
                <ul role="list" class="flex flex-1 flex-col gap-y-7">
                    <li>
                        <ul role="list" class="flex flex-1 flex-col gap-y-7">
                            <div class="flex gap-2 mb-3 overflow-x-auto">

                                <button onclick="filtrar('conversas')"
                                        class="aba whitespace-nowrap text-xs px-3 py-1 rounded-full bg-green-100 text-green-700 font-medium">
                                    Conversas
                                </button>

                                <button onclick="filtrar('nao_lidos')"
                                        class="aba whitespace-nowrap text-xs px-3 py-1 rounded-full bg-gray-100 text-gray-700 font-medium">
                                    Não lidas
                                </button>

                                <button onclick="filtrar('arquivados')"
                                        class="aba whitespace-nowrap text-xs px-3 py-1 rounded-full bg-gray-100 text-gray-700 font-medium">
                                    Arquivadas
                                </button>

                                <button onclick="filtrar('campanhas')"
                                        class="aba whitespace-nowrap text-xs px-3 py-1 rounded-full bg-gray-100 text-gray-700 font-medium">
                                    Campanhas
                                </button>

                                <button onclick="filtrar('todos')"
                                        class="aba whitespace-nowrap text-xs px-3 py-1 rounded-full bg-gray-100 text-gray-700 font-medium">
                                    Todos
                                </button>

                            </div>
                            <li>
                                <ul role="list" class="-mx-2 space-y-1">
                                    @foreach($this->contatos as $contato)


                                        @php
                                            $bg = "bg-gray-50 dark:bg-white/5";
                                            $hidden = "";
                                            if (!empty($this->contatoSelecionado) && $this->contatoSelecionado->id == $contato->id){
                                                $bg = "bg-green-500 dark:bg-white/5";
                                            }

                                            $isCampanha =
                                                $contato->ultimaMensagem->body == "aviso_vencimento_net_1" ||
                                                $contato->ultimaMensagem->body == "venda_produtos_1" ||
                                                $contato->ultimaMensagem->body == "venda_produtos_2" ||
                                                $contato->ultimaMensagem->body == "venda_produtos_3" ||
                                                $contato->ultimaMensagem->body == "parrou_massa";
                                        @endphp
                                        <li
                                            onclick="window.location.href='/contato/{{$contato->wa_id}}'"
                                            class="contato group gap-x-3 rounded-md {{$bg}} p-2 text-sm font-semibold dark:text-white hover:bg-green-200"

                                            data-nao-lida="{{ $contato->mensagens_nao_lida > 0 ? '1' : '0' }}"
                                            data-arquivado="{{ $contato->ultimaMensagem->mensagem_arquivada == 1 ? '1' : '0' }}"
                                            data-campanha="{{ $isCampanha ? '1' : '0' }}"
                                            id="contato_{{$contato->wa_id}}">

                                            <div class="flex flex-col">

                                                <!-- Linha 1 -->
                                                <div class="flex justify-between items-center">
                                                    <span>
                                                        {{ $contato->name ?? $contato->wa_id }}
                                                    </span>

                                                    <span id="wa_id_{{ $contato->wa_id }}" class="bg-green-500 text-white text-xs px-2 py-0.5 rounded-full {{ $contato->mensagens_nao_lida == 0 ? 'hidden' : '' }}">
                                                        {{ $contato->mensagens_nao_lida }}
                                                    </span>
                                                </div>

                                                <!-- Linha 2 -->
                                                <div id="ultima_mensagem_wa_id_{{ $contato->wa_id }}"
                                                     class="text-gray-500 text-sm truncate">
                                                    {{ \Illuminate\Support\Str::limit(preg_replace('/[^A-Za-z0-9 ]/', '', $contato->ultimaMensagem->body), 40) }}
                                                </div>

                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>

                        </ul>
                    </li>
                    <li class="-mx-6 mt-auto">
                        <a href="#" class="flex items-center gap-x-4 px-6 py-3 text-sm/6 font-semibold text-gray-900 hover:bg-gray-50 dark:text-white dark:hover:bg-white/5">
                            <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="" class="size-8 rounded-full bg-gray-50 outline -outline-offset-1 outline-black/5 dark:bg-gray-800 dark:outline-white/10" />
                            <span class="sr-only">Your profile</span>
                            <span aria-hidden="true">André Wagner</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>

    <div class="sticky top-0 z-40 flex items-center gap-x-6 bg-white px-4 py-4 shadow-xs sm:px-6 lg:hidden dark:bg-gray-900 dark:shadow-none dark:after:pointer-events-none dark:after:absolute dark:after:inset-0 dark:after:border-b dark:after:border-white/10 dark:after:bg-black/10">
        <button type="button" onclick="openSideBar()" class="-m-2.5 p-2.5 text-gray-700 hover:text-gray-900 lg:hidden dark:text-gray-400 dark:hover:text-white">
            <span class="sr-only">Open sidebar</span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6">
                <path d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </button>
        <div class="flex-1 text-sm/6 font-semibold text-gray-900 dark:text-white">RecargaHouse Whatsapp</div>
        <a href="#">
            <span class="sr-only">Your profile</span>
            <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="" class="size-8 rounded-full bg-gray-50 outline -outline-offset-1 outline-black/5 dark:bg-gray-800 dark:outline-white/10" />
        </a>
    </div>

    <main class="py-10 lg:pl-72">
        <div class="px-4 sm:px-6 lg:px-8" id="feed">

            @if(!empty($this->contatoSelecionado))
                <livewire:feed :contatoSelecionado="$contatoSelecionado" />
            @endif
        </div>
    </main>



</div>
