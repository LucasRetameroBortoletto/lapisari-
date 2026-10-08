// =====================================================================
// Lapisari · splash-inicio.js
// Roda no <head> da página inicial, ANTES de a página ser desenhada (por
// isso sem "defer"; quem o pede é o views/paginas/inicio.php).
//
// 1) Decide se a abertura animada vai aparecer, marcando <html class="com-splash">.
//    Se essa decisão ficasse para depois, a vitrine piscaria na tela por um
//    instante antes de a abertura cobri-la.
// 2) Carrega as animações da página inicial e as bibliotecas (assets/js/libs):
//      sempre:          caminho.js (o caminho de grafite que liga os pilares)
//      com a abertura:  splash.js (a assinatura escrita que vira o header)
//                       abertura.js (o texto de abertura)
//
// As animações rodam sempre, mesmo com "reduzir movimento" ligado no sistema.
// Para desligar: abra o site com ?sem-animacao no endereço
// (ex.: index.php?sem-animacao). Fica desligado nesta aba até ela ser
// fechada, ou até abrir com ?com-animacao.
// Sem JavaScript, nada acontece: a página abre direto e sem animações.
// =====================================================================
(function () {
    var raiz = document.documentElement;

    // ---- 0) Animações desligadas? -----------------------------------------------
    // A escolha fica guardada na aba (sessionStorage) porque cada clique
    // recarrega a página, e o ?sem-animacao se perderia no primeiro link.
    var parametros = new URLSearchParams(window.location.search);
    var semAnimacao = parametros.has('sem-animacao');
    try {
        if (parametros.has('sem-animacao')) {
            sessionStorage.setItem('lapisari-sem-animacao', '1');
        }
        if (parametros.has('com-animacao')) {
            sessionStorage.removeItem('lapisari-sem-animacao');
        }
        semAnimacao = sessionStorage.getItem('lapisari-sem-animacao') === '1';
    } catch (erro) {
        // sem sessionStorage (navegação privada): vale só o endereço desta página
    }
    if (semAnimacao) {
        return;   // nada é carregado: a página fica parada, como sem JavaScript
    }

    // Guarda a posição de rolagem ao sair da página: no F5 no meio da
    // página, a abertura é pulada (veja abaixo)
    window.addEventListener('pagehide', function () {
        try {
            sessionStorage.setItem('lapisari-posicao', String(Math.round(window.scrollY)));
        } catch (erro) {
            // navegação privada pode bloquear o sessionStorage; seguimos sem ele
        }
    });

    // ---- 1) A abertura aparece nesta visita? ----------------------------------
    function mostrarAbertura() {
        // Veio de outra página da própria loja (ex.: clicou no logotipo estando
        // no carrinho): a abertura é a "porta de entrada" do site, então só
        // aparece para quem chega de fora ou digita o endereço. Nesta versão
        // cada clique recarrega a página, então sem esta regra ela tocaria a
        // cada volta à inicial.
        if (document.referrer && new URL(document.referrer).origin === window.location.origin) {
            return false;
        }

        // Link para um ponto da página (ex.: index.php#vitrine depois de
        // adicionar ao carrinho ou de entrar): vai direto ao ponto
        if (window.location.hash) {
            return false;
        }

        // Recarregou (F5) ou voltou pelo navegador no meio da página: o
        // navegador vai restaurar a posição, então pulamos a abertura
        var navegacao = (performance.getEntriesByType && performance.getEntriesByType('navigation')[0]) || {};
        var voltando = navegacao.type === 'reload' || navegacao.type === 'back_forward';
        var posicaoSalva = 0;
        try {
            posicaoSalva = Number(sessionStorage.getItem('lapisari-posicao')) || 0;
        } catch (erro) {
            // sem sessionStorage: a abertura só não é pulada no F5
        }
        return !(voltando && posicaoSalva > 0);
    }

    var comAbertura = mostrarAbertura();
    if (comAbertura) {
        raiz.classList.add('com-splash');
    }

    // ---- 2) Carrega o resto -----------------------------------------------------
    // A pasta vem do endereço deste próprio arquivo (assets/js/), então
    // funciona com qualquer BASE_URL.
    // async = false: os arquivos baixam ao mesmo tempo, mas rodam na ordem da
    // lista (as bibliotecas antes de quem as usa).
    var arquivos = ['libs/gsap.min.js', 'libs/ScrollTrigger.min.js', 'libs/SplitText.min.js', 'libs/DrawSVGPlugin.min.js'];
    if (comAbertura) {
        arquivos.push('splash.js', 'abertura.js');
    }
    arquivos.push('caminho.js');

    var pasta = document.currentScript.src.replace(/[^\/]*$/, '');
    arquivos.forEach(function (arquivo) {
        var script = document.createElement('script');
        script.src = pasta + arquivo;
        script.async = false;
        if (arquivo === 'splash.js') {
            // Sem o splash.js ninguém tiraria a tela preta: libera a página
            script.onerror = function () {
                raiz.classList.remove('com-splash');
            };
        }
        document.head.appendChild(script);
    });
})();
