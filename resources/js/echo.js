import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: "pusher",
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true,
    wsHost: import.meta.env.VITE_PUSHER_HOST,
    wsPort: import.meta.env.VITE_PUSHER_PORT,
    wssPort: import.meta.env.VITE_PUSHER_PORT,
    enabledTransports: ["ws", "wss"],
});


window.Echo.channel('MensagemRecebidaWhatsapp')
    .listen('MensagemRecebidaWhatsapp', (e) => {

        let wa_id = document.getElementById('wa_id');
        if (wa_id) {
            wa_id = wa_id.value;
        }

        const contatoId = e.message.from;
        const el = document.getElementById("contato_" + contatoId);

        // 👉 Se for o chat aberto
        if (contatoId == wa_id) {
            renderMensagem(e.message);
        }

        if (el) {

            const campanhas = [
                "aviso_vencimento_net_1",
                "venda_produtos_1",
                "venda_produtos_2",
                "venda_produtos_3",
                "parrou_massa",
                "captar_leads"
            ];

            const body = e.message.body || '';
            const isCampanha = campanhas.includes(body);

            // 👉 Atualiza campanha
            el.dataset.campanha = isCampanha ? "1" : "0";

            if (!e.message?.enviado) {

                // 🔥 DESARQUIVA AUTOMATICAMENTE
                el.dataset.arquivado = "0";

                // 👉 Marca como não lida
                el.dataset.naoLida = "1";

                const span = document.getElementById("wa_id_" + contatoId);
                const spanUltimaMensagem = document.getElementById("ultima_mensagem_wa_id_" + contatoId);

                if (span) {
                    let valor = parseInt(span.textContent) || 0;
                    valor += 1;

                    span.classList.remove('hidden');
                    span.textContent = valor;
                }

                if (spanUltimaMensagem) {
                    spanUltimaMensagem.textContent = body
                        .replace(/[^A-Za-z0-9 ]/g, '')
                        .substring(0, 20);
                }
            }

            // 👉 Remove campanha se não for mais
            if (!isCampanha) {
                el.dataset.campanha = "0";
            }

            // 👉 Move pro topo
            el.parentNode.prepend(el);
        }

        // 🔁 Reaplica filtro atual
        const abaAtual = localStorage.getItem('aba') || 'conversas';
        filtrar(abaAtual);
    });

function renderMensagem(msg) {
    const template = document.getElementById('templateMensagem');
    const clone = template.content.cloneNode(true);

    const li = clone.querySelector('li');
    const box = clone.querySelector('.mensagem-box');
    const body = clone.querySelector('.mensagem-body');
    const time = clone.querySelector('.mensagem-time');

    // 👉 Conteúdo
    if (msg.type === 'image') {
        const url = `/storage/${msg.body}`;
        body.innerHTML = `<a href="${url}" target="_blank">
            <img src="${url}" class="rounded max-h-60">
        </a>`;
    } else {
        body.innerHTML = (msg.body || '').replace(/\n/g, '<br>');
    }

    // 👉 Hora (ajusta conforme seu formato)
    time.innerText = formatarHora(msg.timestamp);

    // 👉 Direção
    if (msg.enviado) {
        li.classList.add('justify-end');
        box.classList.add('bg-green-500', 'text-white', 'rounded-br-none');
    } else {
        li.classList.add('justify-start');
        box.classList.add('bg-gray-200', 'text-gray-900', 'rounded-bl-none');
    }

    document.getElementById('listagemMensagem').appendChild(clone);

    // 👉 Scroll automático (opcional)
    scrollFinalSemPerguntar();
}

function formatarHora(timestamp) {
    if (!timestamp) return '';

    let date;

    // 🧠 Detecta formato automaticamente
    if (typeof timestamp === 'string') {
        // formato: "27/03/2026 14:32:10"
        if (timestamp.includes('/')) {
            const [data, hora] = timestamp.split(' ');
            const [dia, mes, ano] = data.split('/');
            date = new Date(`${ano}-${mes}-${dia}T${hora}`);
        } else {
            // ISO string
            date = new Date(timestamp);
        }

    } else if (typeof timestamp === 'number') {
        // Unix timestamp (segundos ou ms)
        date = timestamp < 9999999999
            ? new Date(timestamp * 1000)
            : new Date(timestamp);

    } else {
        // já é Date ou outro formato
        date = new Date(timestamp);
    }

    if (isNaN(date)) return '';

    const agora = new Date();
    const ontem = new Date();
    ontem.setDate(agora.getDate() - 1);

    const isHoje = date.toDateString() === agora.toDateString();
    const isOntem = date.toDateString() === ontem.toDateString();

    const hora = date.toTimeString().substring(0, 5);

    if (isHoje) {
        return hora;
    } else if (isOntem) {
        return `Ontem ${hora}`;
    } else {
        const dia = String(date.getDate()).padStart(2, '0');
        const mes = String(date.getMonth() + 1).padStart(2, '0');
        return `${dia}/${mes} ${hora}`;
    }
}
