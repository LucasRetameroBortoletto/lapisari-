// =====================================================================
// Lapisari · abertura.js
// Carregado pelo splash-inicio.js, só quando a abertura vai aparecer.
//
// Texto de abertura ("A casa da lapiseira" + título + parágrafo), logo
// depois que a assinatura voa para o header:
//   - o sobretítulo aparece com as letras se aproximando (letter-spacing);
//   - as palavras do título sobem uma a uma, saindo de trás de uma máscara,
//     como tipos sendo compostos numa linha (GSAP SplitText);
//   - o parágrafo surge por último.
// "expo.out" sai rápido e pousa devagar, sem quicar.
//
// Sem GSAP (o arquivo não carregou): nada acontece e o texto fica parado.
// =====================================================================
(function () {
'use strict';

// Pode rodar antes de o navegador terminar de ler o HTML (veja o splash.js)
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', iniciar);
} else {
    iniciar();
}

function iniciar() {
    const abertura = document.querySelector('.conceito__abertura');
    if (!abertura || !window.gsap || !window.SplitText) {
        return;
    }
    gsap.registerPlugin(SplitText);

    // Separa em palavras, cada uma dentro de uma máscara. As palavras ficam com
    // a classe "palavra" e as máscaras com "palavra-mask" (folga para p, ç, j
    // no splash.css). O SplitText mantém o texto original num aria-label:
    // leitores de tela continuam lendo a frase inteira.
    function palavras(elemento) {
        return SplitText.create(elemento, { type: 'words', mask: 'words', wordsClass: 'palavra' }).words;
    }

    // Timeline pausada: os textos já ficam no estado inicial (escondidos), por
    // baixo da tela preta, esperando a vez deles
    const entrada = gsap.timeline({ paused: true, defaults: { ease: 'expo.out' } })
        .from(abertura.querySelector('.sobretitulo'), { opacity: 0, letterSpacing: '0.6em', duration: 1.1 })
        .from(palavras(abertura.querySelector('.conceito__titulo')), { yPercent: 110, duration: 0.9, stagger: 0.07 }, 0.15)
        .from(abertura.querySelector('.conceito__texto'), { opacity: 0, y: 12, duration: 0.9 }, 0.85);

    if (document.documentElement.classList.contains('com-splash')) {
        // A abertura ainda está na tela: começa quando ela terminar (o splash.js avisa)
        document.addEventListener('lapisari:splash-terminou', function () {
            entrada.play();
        }, { once: true });
    } else {
        // A abertura já terminou (ou foi cancelada): começa na hora
        entrada.play();
    }
}

})();
