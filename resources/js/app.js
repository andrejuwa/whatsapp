window._lastScrollCancel = 0;

window.enviarTemplate = async function (
    btn,
    nome,
    texto = null,
    media_id = null,
    contact_wa_id = null,
    tipo = null,
    ativo = null
) {
    let wa_id = document.getElementById('wa_id').value;

    let body = {};

        body.mensagem = texto;
    if (tipo === 'texto') {
    } else if (tipo === 'contacts') {
        body.contact_id = contact_wa_id;
    } else {
        body.media_id = media_id;
    }

    // 👉 salva estado original
    const textoOriginal = btn.innerHTML;

    // 👉 ativa loading
    btn.disabled = true;
    btn.innerHTML = 'Enviando...';
    btn.classList.remove('bg-indigo-600');
    btn.classList.add('bg-blue-50');

    try {
        const response = await fetch(
            `http://admin.recargahouse.site/api/api/whatsapp/enviarMensagem/${wa_id}/${tipo}`,
            {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                },
                body: JSON.stringify(body)
            }
        );

        const data = await response.json();
        console.log(data);

    } catch (error) {
        console.error('Erro ao enviar:', error);
    } finally {
        // 👉 volta ao normal independente de sucesso ou erro
        btn.disabled = false;
        btn.innerHTML = textoOriginal;

        btn.classList.add('bg-indigo-600');
        btn.classList.remove('bg-blue-50');
    }
};

window.gerarDesconto = async function (produtoId) {
    let wa_id = document.getElementById('wa_id').value;
    const url ='https://admin.recargahouse.site';
    const response = await fetch(
        url+`/api/api/whatsapp/gerarDesconto/${wa_id}/${produtoId}`,
        {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            }
        }
    );
};
window.openSideBar = function () {
    const feed = document.querySelector('#feed');
    const sidebar = document.querySelector('#sidebar');
    feed.style.display = "none"

    sidebar.classList.remove(
        'hidden'
    )
    sidebar.classList.add(
        'fixed',
        'inset-y-0',
        'z-50',
        'flex',
        'w-72',
        'flex-col',
        'w-full'
    );

};
window.arquivar = function () {
    const ul = document.querySelector('#listagemMensagem');
    const ultimoLi = ul.lastElementChild;

    if (ultimoLi) {
        const liId = ultimoLi.id;
        console.log(liId);

        // redireciona para um link, por exemplo passando o id
        window.location.href = `/arquivar/${liId}`;
    }
};
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
    textarea.addEventListener('focus', () => {
        const outraDiv = document.getElementById('actions');
        outraDiv.classList.add('hidden');
    });
    textarea.addEventListener('blur', () => {
        const outraDiv = document.getElementById('actions');
        outraDiv.classList.remove('hidden');
    });

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


if (btnEnviar) {
    btnEnviar.addEventListener('click', async function () {
        const texto = textarea.value.trim();
        if (!texto) return;

        // limpa input IMEDIATO (UX boa)
        textarea.value = '';
        btnEnviar.style.display = 'none';
        autoResize(textarea);
        let wa_id = document.getElementById('wa_id').value;
        try {
            const response = await fetch('https://admin.recargahouse.site/api/api/whatsapp/enviarMensagem/'+wa_id, {
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

}
if (textarea){

    textarea.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault(); // evita quebra de linha

            const texto = this.value.trim();
            if (!texto) return;

            btnEnviar.click(); // reaproveita tua lógica de envio
        }
    });
}

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */

import './echo';

window.atualizarFlow = async function (wa_id, status) {
    try {
        const response = await fetch(`https://admin.recargahouse.site/api/api/whatsapp/flow/${wa_id}/${status}`, {
            method: 'POST',
        });
        console.log(response)
        alert('atualizado')

    } catch (error) {
        console.error('Erro ao atualizar flow:', error);
    }
};


window.echoMensagem = function (e) {
    const msg = e.message;

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
        wrapper.setAttribute('id', msg.message_id);
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
    scrollFinal();
};

document.addEventListener("DOMContentLoaded", function() {
    scrollFinalSemPerguntar();

    const isMobile = window.matchMedia('(max-width: 768px)').matches;
    const isHome = window.location.pathname === '/';

    if (isMobile && isHome) {
        openSideBar();
    }


});
