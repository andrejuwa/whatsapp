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
            wa_id = wa_id.value
        }

        if (e.message.from == wa_id) {
            echoMensagem(e)
        }else{
            const el = document.getElementById("contato_" + e.message.from);

            if (el) {
                el.style = "";
            }
            if (!e.message?.enviado) {

                const span = document.getElementById("wa_id_"+e.message.from);
                const spanUltimaMensagem = document.getElementById("ultima_mensagem_wa_id_"+e.message.from);


                let valor = parseInt(span.textContent) || 0; // se estiver vazio, vira 0

                valor += 1;
                span.classList.remove('hidden');
                span.textContent = valor;
                spanUltimaMensagem.textContent = e.message.body.replace(/[^A-Za-z0-9 ]/g, '') // remove caracteres especiais
                    .substring(0, 20);
            }
        }

    });
