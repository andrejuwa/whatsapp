<?php

use Livewire\Component;

new class extends Component
{
    public $mensagens;

    public function mount($contatoSelecionado)
    {
        \App\Services\AtualizarRegistrosService::atualizarContatos();

        $ultimaMensagem = \App\Models\Mensagem::query()->latest()->first();
        $total =  \App\Services\AtualizarRegistrosService::atualizarMensagens($ultimaMensagem->whatsapp_id);

        if ($total > 1){
            $ultimaMensagem = \App\Models\Mensagem::query()->latest()->first();
            $total =  \App\Services\AtualizarRegistrosService::atualizarMensagens($ultimaMensagem->whatsapp_id);
        }

        $this->mensagens = \App\Models\Mensagem::query()->where('from', $contatoSelecionado->wa_id)->orderBy('timestamp', 'asc')->get();


    }
};
?>

<div>
    <!-- Include this script tag or install `@tailwindplus/elements` via npm: -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script> -->
    <ul role="list" class="space-y-6">
        @foreach($this->mensagens as $mensagem)

            <li class="relative flex gap-x-4">
                <div class="absolute top-0 -bottom-6 left-0 flex w-6 justify-center">
                    <div class="w-px bg-gray-200 dark:bg-white/15"></div>
                </div>
                <div class="relative flex size-6 flex-none items-center justify-center bg-white dark:bg-gray-900">
                    <div class="size-1.5 rounded-full bg-gray-100 ring ring-gray-300 dark:bg-white/10 dark:ring-white/20"></div>
                </div>
                @if($mensagem->enviado)
                    <p class="flex-auto py-0.5 text-xs/5 text-gray-500 dark:text-gray-400 bg-green-100">
                        {!! $mensagem->body !!}
                    </p>
                    <time datetime="2023-01-23T10:32" class="flex-none py-0.5 text-xs/5 text-gray-500 dark:text-gray-400">{{ $mensagem->timestamp }}</time>
                @else
                    <time datetime="2023-01-23T10:32" class="flex-none py-0.5 text-xs/5 text-gray-500 dark:text-gray-400">{{ $mensagem->timestamp }}</time>
                    <p class="flex-auto py-0.5 text-xs/5 text-gray-500 dark:text-gray-400">
                        {!! $mensagem->body !!}
                    </p>
                @endif

            </li>
        @endforeach
            <li class="relative flex gap-x-4">
                <div class="absolute top-0 -bottom-6 left-0 flex w-6 justify-center">
                    <div class="w-px bg-gray-200 dark:bg-white/15"></div>
                </div>
                <div class="relative flex size-6 flex-none items-center justify-center bg-white dark:bg-gray-900">
                    <div class="size-1.5 rounded-full bg-gray-100 ring ring-gray-300 dark:bg-white/10 dark:ring-white/20"></div>
                </div>
                <p class="flex-auto py-0.5 text-xs/5 text-gray-500 dark:text-gray-400"><span class="font-medium text-gray-900 dark:text-white">Chelsea Hagon</span> created the invoice.</p>
                <time datetime="2023-01-23T10:32" class="flex-none py-0.5 text-xs/5 text-gray-500 dark:text-gray-400">7d ago</time>
            </li>
    </ul>

    <!-- New comment form -->
    <div class="mt-6 flex gap-x-3">
        <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="" class="size-6 flex-none rounded-full bg-gray-50 outline -outline-offset-1 outline-black/5 dark:bg-gray-800 dark:outline-white/10" />
        <form action="#" class="relative flex-auto">
            <div class="overflow-hidden rounded-lg pb-12 outline-1 -outline-offset-1 outline-gray-300 focus-within:outline-2 focus-within:-outline-offset-2 focus-within:outline-indigo-600 dark:bg-white/5 dark:outline-white/10 dark:focus-within:outline-indigo-500">
                <label for="comment" class="sr-only">Add your comment</label>
                <textarea id="comment" name="comment" rows="2" placeholder="Add your comment..." class="block w-full resize-none bg-transparent px-3 py-1.5 text-base text-gray-900 placeholder:text-gray-400 focus:outline-none sm:text-sm/6 dark:text-white dark:placeholder:text-gray-500"></textarea>
            </div>

            <div class="absolute inset-x-0 bottom-0 flex justify-between py-2 pr-2 pl-3">
                <div class="flex items-center space-x-5">
                    <div class="flex items-center">
                        <button type="button" class="-m-2.5 flex size-10 items-center justify-center rounded-full text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-white">
                            <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true" class="size-5">
                                <path d="M15.621 4.379a3 3 0 0 0-4.242 0l-7 7a3 3 0 0 0 4.241 4.243h.001l.497-.5a.75.75 0 0 1 1.064 1.057l-.498.501-.002.002a4.5 4.5 0 0 1-6.364-6.364l7-7a4.5 4.5 0 0 1 6.368 6.36l-3.455 3.553A2.625 2.625 0 1 1 9.52 9.52l3.45-3.451a.75.75 0 1 1 1.061 1.06l-3.45 3.451a1.125 1.125 0 0 0 1.587 1.595l3.454-3.553a3 3 0 0 0 0-4.242Z" clip-rule="evenodd" fill-rule="evenodd" />
                            </svg>
                            <span class="sr-only">Attach a file</span>
                        </button>
                    </div>
                </div>
                <button type="submit" class="rounded-md bg-white px-2.5 py-1.5 text-sm font-semibold text-gray-900 shadow-xs inset-ring inset-ring-gray-300 hover:bg-gray-50 dark:bg-white/10 dark:text-white dark:shadow-none dark:inset-ring-white/5 dark:hover:bg-white/20">Comment</button>
            </div>
        </form>
    </div>


</div>
