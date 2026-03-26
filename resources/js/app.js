window._lastScrollCancel = 0;

window.scrollFinalSemPerguntar = function (element = null) {
    window.scrollTo({
        top: document.body.scrollHeight,
        behavior: 'smooth'
    });
};

window.scrollFinal = function (element = null) {
    const now = Date.now();
    const TEN_SECONDS = 10000;
    const tolerance = 300;

    if (now - window._lastScrollCancel < TEN_SECONDS) {
        return;
    }

    let isAtBottom = false;

    if (element) {
        isAtBottom = element.scrollHeight - element.scrollTop <= element.clientHeight + tolerance;
    } else {
        isAtBottom = window.innerHeight + window.scrollY >= document.body.scrollHeight - tolerance;
    }

    if (!isAtBottom) {
        if (!confirm('Você está longe do final. Deseja ir até o final agora?')) {
            window._lastScrollCancel = now;
            return;
        }
    }

    if (element) {
        element.scrollTop = element.scrollHeight;
    } else {
        window.scrollTo({
            top: document.body.scrollHeight,
            behavior: 'smooth'
        });
    }
};

const btn = document.getElementById('btnScrollBottom');
const form = document.querySelector('.fixed.bottom-0'); // seu form fixo
const tolerance = 300;

function ajustarBotao() {
    if (!btn || !form) return;

    const alturaForm = form.offsetHeight;
    btn.style.bottom = (alturaForm + 20) + 'px'; // sempre acima do form
}

function checkScrollButton(element = null) {
    if (!btn) return;

    let isAtBottom = false;

    if (element) {
        isAtBottom = element.scrollHeight - element.scrollTop <= element.clientHeight + tolerance;
    } else {
        isAtBottom = window.innerHeight + window.scrollY >= document.body.scrollHeight - tolerance;
    }

    btn.style.display = isAtBottom ? 'none' : 'block';
}

// eventos
window.addEventListener('scroll', () => checkScrollButton());
window.addEventListener('load', () => {
    checkScrollButton();
    ajustarBotao();
});
window.addEventListener('resize', ajustarBotao);



// =======================
// TEXTAREA AUTO RESIZE
// =======================

const textarea = document.getElementById('comentario');

const MIN_ROWS = 5;
const MAX_ROWS = 15;

function autoResize(el) {
    if (!el) return;

    el.rows = MIN_ROWS;

    const style = window.getComputedStyle(el);

    const lineHeight = parseInt(style.lineHeight);
    const padding =
        parseInt(style.paddingTop) +
        parseInt(style.paddingBottom);

    const rows = Math.floor((el.scrollHeight - padding) / lineHeight);

    if (rows >= MAX_ROWS) {
        el.rows = MAX_ROWS;
        el.style.overflowY = 'auto';
    } else {
        el.rows = Math.max(rows, MIN_ROWS);
        el.style.overflowY = 'hidden';
    }
}

if (textarea) {
    textarea.addEventListener('input', function () {
        autoResize(this);
    });

    // inicial
    autoResize(textarea);
}


const btnEnviar = document.getElementById('btnEnviar');

if (textarea && btnEnviar) {
    textarea.addEventListener('input', function () {
        autoResize(this);

        const hasText = this.value.trim().length > 0;
        btnEnviar.style.display = hasText ? 'block' : 'none';
    });
}
const template = document.getElementById('templateMensagem');

btnEnviar.addEventListener('click', async function () {
    const texto = textarea.value.trim();
    if (!texto) return;

    // limpa input IMEDIATO (UX boa)
    textarea.value = '';
    btnEnviar.style.display = 'none';
    autoResize(textarea);

    try {
        const response = await fetch('https://admin.recargahouse.site/api/api/whatsapp/enviarMensagem/555596869456', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            },
            body: JSON.stringify({
                mensagem: texto
            })
        });

        if (!response.ok) {
            throw new Error('Erro na API');
        }

        // ❗ NÃO faz mais nada aqui
        // quem renderiza é o Echo

    } catch (error) {
        console.error(error);

        // 👇 aqui você pode opcionalmente avisar o usuário
        alert('Erro ao enviar mensagem');
    }
});
textarea.addEventListener('keydown', function (e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault(); // evita quebra de linha

        const texto = this.value.trim();
        if (!texto) return;

        btnEnviar.click(); // reaproveita tua lógica de envio
    }
});

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */

import './echo';


window.Echo.channel('MensagemRecebidaWhatsapp')
    .listen('MensagemRecebidaWhatsapp', (e) => {
        const msg = e.message;
        console.log(msg)

        const ul = document.querySelector('#listagemMensagem');
        const template = document.getElementById('templateMensagem');

        if (!ul || !template) return;

        // 🚫 evita duplicação
        if (document.querySelector(`[data-id="${msg.message_id}"]`)) {
            return;
        }

        // clona template
        const clone = template.content.cloneNode(true);

        const mensagemBody = clone.querySelector('.mensagem-body');
        const mensagemTime = clone.querySelector('.mensagem-time');
        const wrapper = clone.querySelector('li'); // ou div principal

        // seta id único
        if (wrapper) {
            wrapper.setAttribute('data-id', msg.message_id);
        }

        // conteúdo
        if (msg.body_formatado) {
            mensagemBody.innerHTML = msg.body_formatado;
        } else {
            mensagemBody.textContent = msg.body;
        }

        // data
        const data = new Date(msg.timestamp * 1000);
        mensagemTime.textContent = data.toLocaleString();

        // 👇 REGRA PRINCIPAL AQUI
        const isEnviado = msg.enviado === true || msg.enviado === 1 || msg.enviado === '1';

        if (isEnviado) {
            mensagemBody.classList.add('bg-green-100');
        } else {
            mensagemBody.classList.add('bg-gray-200');
        }

        // adiciona no DOM
        ul.appendChild(clone);

        // scroll automático
        scrollFinalSemPerguntar();
    });
