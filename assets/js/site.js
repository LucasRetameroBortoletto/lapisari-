// =====================================================================
// Lapisari · site.js
// Quatro comportamentos pequenos, sem bibliotecas. O site funciona sem
// este arquivo: ele só deixa algumas telas mais práticas.
// =====================================================================

// ---- Confirmação antes de enviar -------------------------------------
// Qualquer <form data-confirmar="mensagem"> pergunta "tem certeza?" antes
// de enviar (usado nos botões de excluir).
document.addEventListener('submit', function (event) {
    const mensagem = event.target.dataset.confirmar;
    if (mensagem && !window.confirm(mensagem)) {
        event.preventDefault();
    }
});

// ---- Filtros da vitrine ----------------------------------------------
// O filtro é aplicado assim que um select muda, e o botão "Filtrar" some.
document.querySelectorAll('form[data-envio-automatico]').forEach(function (form) {
    const botao = form.querySelector('[data-botao-filtrar]');
    if (botao) {
        botao.hidden = true;
    }
    form.addEventListener('change', function () {
        form.submit();
    });
});

// ---- Prévia da foto ---------------------------------------------------
// <input type="file" data-previa="id-da-img">: mostra a foto escolhida
// antes de enviar o formulário.
document.querySelectorAll('input[type="file"][data-previa]').forEach(function (input) {
    const previa = document.getElementById(input.dataset.previa);
    if (!previa) {
        return;
    }
    const imagemOriginal = previa.src;

    // Caixa "Remover a foto atual" (só na edição), ligada à mesma prévia
    const remover = document.querySelector('[data-remover-previa="' + input.dataset.previa + '"]');

    input.addEventListener('change', function () {
        const arquivo = input.files[0];
        previa.src = arquivo ? URL.createObjectURL(arquivo) : imagemOriginal;
        if (arquivo && remover) {
            remover.checked = false;   // a foto nova substitui a atual
        }
    });

    if (remover) {
        remover.addEventListener('change', function () {
            if (remover.checked) {
                input.value = '';
                previa.src = previa.dataset.padrao;
            } else {
                previa.src = imagemOriginal;
            }
        });
    }

    // "Limpar"/"Desfazer" apaga o arquivo escolhido; a prévia volta junto
    if (input.form) {
        input.form.addEventListener('reset', function () {
            previa.src = imagemOriginal;
        });
    }
});

// ---- Sair ao fechar a aba ----------------------------------------------
// O sessionStorage é uma memória do navegador que pertence à aba e é
// apagada quando ela é fechada. Ao entrar, gravamos uma marca nele. Se uma
// página abre com alguém logado (tem o botão "Sair") mas sem a marca, a aba
// em que a pessoa entrou foi fechada: mandamos para o logout.
// (Não dá para usar o evento de fechar a aba: nesta versão cada clique
// recarrega a página, e o navegador não diferencia uma coisa da outra.)
try {
    // Formulário de login: é o único com o campo de senha "current-password"
    const senhaDoLogin = document.querySelector('input[autocomplete="current-password"]');
    if (senhaDoLogin) {
        senhaDoLogin.form.addEventListener('submit', function () {
            try {
                sessionStorage.setItem('lapisari-aba-logada', '1');
            } catch (erro) {
                // sem sessionStorage: veja o catch lá embaixo
            }
        });
    }

    const botaoSair = document.querySelector('a[href$="login/logout.php"]');
    if (botaoSair && sessionStorage.getItem('lapisari-aba-logada') !== '1') {
        // replace: o botão "voltar" não traz de volta a página logada
        window.location.replace(botaoSair.href);
    }
} catch (erro) {
    // O navegador bloqueou o sessionStorage: a pessoa só não é deslogada
    // ao fechar a aba (o resto do site funciona normalmente)
}
