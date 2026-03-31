<?php

use Livewire\Component;

new class extends Component {
    public $mensagens;
    public $contatoSelecionado;
    public $templates;

    public function mount($contatoSelecionado)
    {
        $this->contatoSelecionado = $contatoSelecionado;
        $this->templates = \App\Models\Template::query()->where('ativo', true)->get();


        $mensagens = \App\Models\Mensagem::query()
            ->limit(50)
            ->where('from', $contatoSelecionado->wa_id)
            ->orderBy('timestamp', 'desc')
            ->get();


        $this->mensagens = $mensagens->sortBy('timestamp');

    }
};
?>

<div>
    <!-- Include this script tag or install `@tailwindplus/elements` via npm: -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script> -->
    <ul role="list" class="space-y-6" id="listagemMensagem">
        @foreach($this->mensagens as $mensagem)
            <li class="flex {{ $mensagem->enviado ? 'justify-end' : 'justify-start' }} mb-2" id="{{ $mensagem->whatsapp_id }}">

                <div class="max-w-[70%] break-all">

                    <div class="px-3 py-2 rounded-lg text-sm
            {{ $mensagem->enviado
                ? 'bg-green-500 text-white rounded-br-none'
                : 'bg-gray-200 text-gray-900 rounded-bl-none' }}">

                        @if($mensagem->type == "text")
                            {!! nl2br(e($mensagem->body)) !!}

                        @elseif($mensagem->type == "image")
                            @php
                                $imagem = "https://recargahouse.site/storage/" . $mensagem->body;
                            @endphp
                            <a href="{{ $imagem }}" target="_blank">
                                <img src="{{ $imagem }}" class="rounded max-h-60">
                            </a>

                        @elseif($mensagem->type == "interactive")
                            {{ json_decode($mensagem->body)->id ?? '' }}

                        @else
                            {!! nl2br(e($mensagem->body)) !!}
                        @endif
                    </div>

                    @php
                        $data = \Carbon\Carbon::createFromFormat('d/m/Y H:i:s', $mensagem->timestamp);
                    @endphp

                    <div class="text-[10px] text-gray-400 mt-1 {{ $mensagem->enviado ? 'text-right' : 'text-left' }}">
                        @if($data->isToday())
                            {{ $data->format('H:i') }}
                        @elseif($data->isYesterday())
                            Ontem {{ $data->format('H:i') }}
                        @else
                            {{ $data->format('d/m H:i') }}
                        @endif
                    </div>

                </div>

            </li>
        @endforeach

    </ul>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>

    <button
        onclick="scrollFinalSemPerguntar()"
        id="btnScrollBottom"
        style="display: none"
        class="fixed bottom-32 right-6 z-50 bg-indigo-600 hover:bg-indigo-700 text-white p-3 rounded-full shadow-lg transition-all duration-300"
    >
        ↓
    </button>

    <!-- New comment form -->
    <div
        class="fixed bottom-0 left-0 lg:left-72 right-0 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-white/10 z-50">
        <div class="px-4 py-3 flex gap-x-3">
            <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?..."
                 class="size-6 flex-none rounded-full"/>

            <form action="#" class="relative flex-auto">
                <div
                    class="overflow-hidden rounded-lg outline-1 outline-gray-300 focus-within:outline-2 focus-within:outline-indigo-600 dark:bg-white/5">
                    <textarea
                        id="comentario"
                        rows="5"
                        placeholder="Add your comment..."
                        class="block w-full resize-none bg-transparent px-3 py-1.5 text-sm text-gray-900 dark:text-white focus:outline-none"
                    ></textarea>
                </div>
                <button
                    type="button"
                    id="btnEnviar"
                    style="display: none"
                    class="mt-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg"
                >
                    Enviar
                </button>

                <div id="actions">
                    <button
                        type="button"
                        class="mt-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg"
                        onclick="atualizarFlow({{ $this->contatoSelecionado->wa_id }}, 'manual')"
                    >
                        Manual
                    </button>

                    <button
                        type="button"
                        class="mt-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg"
                        onclick="atualizarFlow({{ $this->contatoSelecionado->wa_id }}, '0')"
                    >
                        Automatizar
                    </button>


                    <button
                        type="button"
                        class="mt-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg"
                        onclick="atualizarFlow({{ $this->contatoSelecionado->wa_id }}, '1.2')"
                    >
                        Enviar Contato
                    </button>


                    <button
                        type="button"
                        class="mt-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg"
                        onclick="arquivar()"
                    >
                        Arquivar
                    </button>
                    <button type="button" command="show-modal" commandfor="dialog" class="rounded-md bg-gray-950/5 px-2.5 py-1.5 text-sm font-semibold text-gray-900 hover:bg-gray-950/10 dark:bg-white/10 dark:text-white dark:inset-ring dark:inset-ring-white/5 dark:hover:bg-white/20">Open dialog</button>
                </div>
            </form>
        </div>
    </div>
    <template id="templateMensagem">
        <li class="relative flex gap-x-4">
            <div class="absolute top-0 -bottom-6 left-0 flex w-6 justify-center">
                <div class="w-px dark:bg-white/15"></div>
            </div>
            <div class="relative flex size-6 flex-none items-center justify-center bg-white dark:bg-gray-900">
                <div
                    class="size-1.5 rounded-full bg-gray-100 ring ring-gray-300 dark:bg-white/10 dark:ring-white/20"></div>
            </div>

            <p class="mensagem-body flex-auto py-0.5 text-xs/5 text-gray-500 dark:text-gray-400 bg-blue-100"></p>

            <time class="mensagem-time flex-none py-0.5 text-xs/5 text-gray-500 dark:text-gray-400"></time>
        </li>
    </template>
    <input type="hidden" id="wa_id" value="{{ $this->contatoSelecionado->wa_id }}">
    <!-- Include this script tag or install `@tailwindplus/elements` via npm: -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script> -->

    <el-dialog>
        <dialog id="dialog" aria-labelledby="dialog-title" class="fixed inset-0 size-auto max-h-none max-w-none overflow-y-auto bg-transparent backdrop:bg-transparent">
            <el-dialog-backdrop class="fixed inset-0 bg-gray-500/75 transition-opacity data-closed:opacity-0 data-enter:duration-300 data-enter:ease-out data-leave:duration-200 data-leave:ease-in dark:bg-gray-900/50"></el-dialog-backdrop>

            <div tabindex="0" class="flex min-h-full items-end justify-center p-4 text-center focus:outline-none sm:items-center sm:p-0">
                <el-dialog-panel class="relative transform overflow-hidden rounded-lg bg-white px-4 pt-5 pb-4 text-left shadow-xl transition-all data-closed:translate-y-4 data-closed:opacity-0 data-enter:duration-300 data-enter:ease-out data-leave:duration-200 data-leave:ease-in sm:my-8 sm:w-full sm:max-w-sm sm:p-6 data-closed:sm:translate-y-0 data-closed:sm:scale-95 dark:bg-gray-800 dark:outline dark:-outline-offset-1 dark:outline-white/10">
                    @foreach($this->templates as $template)
                        <div class="mt-5 sm:mt-6">
                            <button
                                type="button"
                                onclick="enviarTemplate(this,
                            @if(!empty($template->nome)) '{{ $template->nome }}' @else '' @endif,
                            @if(!empty($template->texto)) '{{ $template->texto }}' @else '' @endif,
                            @if(!empty($template->media_id)) '{{ $template->media_id }}' @else '' @endif,
                            @if(!empty($template->wa_id)) '{{ $template->wa_id }}' @else '' @endif,
                            @if(!empty($template->tipo)) '{{ $template->tipo }}' @else '' @endif,
                            @if(!empty($template->ativo)) '{{ $template->ativo }}' @else '' @endif
                        )"
                                class="inline-flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white"
                            >
                                {{ $template->nome }}
                            </button>
                        </div>
                    @endforeach
                        <div class="mt-5 sm:mt-6">
                            <button type="button" onclick="gerarDesconto('9da0baa6-7c63-43ef-be87-bfaee003cd99')" class="inline-flex w-full justify-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-green-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600">Gerar Desconto</button>
                        </div>
                        <div class="mt-5 sm:mt-6">
                            <button type="button" command="close" commandfor="dialog" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-red-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600">Cancelar</button>
                        </div>
                </el-dialog-panel>
            </div>
        </dialog>
    </el-dialog>

</div>
