<?php

use Livewire\Component;

new class extends Component {
    public $mensagens;
    public $contatoSelecionado;

    public function mount($contatoSelecionado)
    {
        $this->visualizar($contatoSelecionado);
        $this->contatoSelecionado = $contatoSelecionado;
        \App\Services\AtualizarRegistrosService::atualizarContatos();

        $ultimaMensagem = \App\Models\Mensagem::query()->latest()->first();

        $total = \App\Services\AtualizarRegistrosService::atualizarMensagens($ultimaMensagem->whatsapp_id);

        $mensagens = \App\Models\Mensagem::query()
            ->limit(50)
            ->where('from', $contatoSelecionado->wa_id)
            ->orderBy('timestamp', 'desc')
            ->get();


        $this->mensagens = $mensagens->sortBy('timestamp');

    }

    public function visualizar($contatoSelecionado): void
    {
        if ($contatoSelecionado->mensagens_nao_lida > 0){
            \App\Services\AtualizarRegistrosService::visualizarMensagens($contatoSelecionado->wa_id);
        }
    }
};
?>

<div>
    <!-- Include this script tag or install `@tailwindplus/elements` via npm: -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script> -->
    <ul role="list" class="space-y-6" id="listagemMensagem">
        @foreach($this->mensagens as $mensagem)
            <li class="relative flex gap-x-4" id="{{$mensagem->whatsapp_id}}">
                <input type="hidden" name="whatsapp_id">
                <div class="absolute top-0 -bottom-6 left-0 flex w-6 justify-center">
                    <div class="w-px bg-gray-200 dark:bg-white/15"></div>
                </div>
                <div class="relative flex size-6 flex-none items-center justify-center bg-white dark:bg-gray-900">
                    <div
                        class="size-1.5 rounded-full bg-gray-100 ring ring-gray-300 dark:bg-white/10 dark:ring-white/20"></div>
                </div>
                @if($mensagem->enviado)
                    <p class="flex-auto py-0.5 text-xs/5 text-gray-500 dark:text-gray-400 bg-green-100">
                        {!! nl2br(e($mensagem->body)) !!}
                    </p>
                    <time datetime="2023-01-23T10:32"
                          class="flex-none py-0.5 text-xs/5 text-gray-500 dark:text-gray-400">{{ $mensagem->timestamp }}</time>
                @else
                    <time datetime="2023-01-23T10:32" class="flex-none py-0.5 text-xs/5 text-gray-500 dark:text-gray-400">
                        {{ $mensagem->timestamp }}
                    </time>
                    @if($mensagem->type == "text")
                        <p class="flex-auto py-0.5 text-xs/5 text-gray-500 dark:text-gray-400">
                            {!! nl2br(e($mensagem->body)) !!}
                        </p>
                    @elseif($mensagem->type == "image")
                        @php
                        $imagem = "https://recargahouse.site/storage/" . $mensagem->body;
                        @endphp
                        <a href="{{ $imagem }}" class="btn bg-green-600 py-1 px-1 rounded" target="_blank" rel="noopener noreferrer">Ver Imagem</a>
                    @elseif($mensagem->type == "interactive")
                        <p class="flex-auto py-0.5 text-xs/5 text-gray-500 dark:text-gray-400">
                            {!! json_decode($mensagem->body)->id !!}
                        </p>
                    @else
                        <p class="flex-auto py-0.5 text-xs/5 text-gray-500 dark:text-gray-400">
                            {!! nl2br(e($mensagem->body)) !!} - {{ $mensagem->type }}
                        </p>
                    @endif
                @endif

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
                    onclick="arquivar()"
                >
                    Arquivar
                </button>
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
</div>
