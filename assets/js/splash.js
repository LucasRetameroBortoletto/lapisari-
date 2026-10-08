// =====================================================================
// Lapisari · splash.js
// Carregado pelo splash-inicio.js, só quando a abertura vai aparecer.
//
// 0) Montagem: cria a tela da abertura com uma cópia do logotipo do header
//    (assim nenhum PHP precisa mudar) e marca os traços do SVG para poderem
//    ser "escritos".
// 1) Escrita: a assinatura é "escrita à mão" desenhando o traço aos poucos
//    (stroke-dashoffset de 1 até 0; os paths têm pathLength="1").
// 2) Espera: terminada a escrita, aparece o "role para baixo".
// 3) Saída, no primeiro scroll (roda do mouse, teclado, toque ou clique),
//    feita com GSAP mudando o tamanho e os cantos do fundo preto:
//    a) Íris: o preto fecha em círculo sobre a assinatura, que encolhe e
//       some como se fosse sugada para o centro, até sobrar uma bolinha.
//    b) Respiro: a bolinha se achata, tomando impulso.
//    c) Subida: voa até o header esticando no caminho (squash & stretch)
//       e volta a ser redonda ao chegar.
//    d) Abertura: se espalha pelos lados como líquido até virar a barra do
//       header; os cantos só ficam retos quando encostam nas bordas.
//    e) Pouso: a splash dá lugar ao header de verdade (idênticos nesse
//       instante) e o logotipo do header é escrito, fechando a história:
//       a assinatura engolida pela bolinha reaparece no header.
//    Sem o GSAP (o arquivo não carregou), a splash só esmaece.
//
// Roda sempre, mesmo com "reduzir movimento" ligado no sistema; quem
// desliga as animações é o ?sem-animacao (veja o splash-inicio.js).
// =====================================================================

// Tudo dentro de uma função: as variáveis deste arquivo não se misturam
// com as de outros scripts da mesma página.
(function () {
'use strict';

const raiz = document.documentElement;
let splash = null;

// Tempos da escrita (ms)
const DURACAO_TRACO = 2600;   // o nome inteiro
const DURACAO_PINGO = 160;    // cada pingo dos "i"
const PAUSA_ANTES = 250;      // tela preta antes da caneta começar

// Tempos da saída animada (segundos, a unidade do GSAP)
const SAIDA = {
    iris: 1.0,       // o preto fecha em círculo até sobrar a bolinha
    respiro: 0.2,    // a bolinha se achata, tomando impulso
    subida: 0.55,    // voa até o header, esticando no caminho
    abertura: 0.6,   // se espalha e vira a barra do header
    escrita: 0.9,    // o logotipo do header é escrito
};
const RAIO_BOLINHA = 22;      // px

// Este arquivo é carregado por outro script (splash-inicio.js) e pode rodar
// antes de o navegador terminar de ler o HTML: esperamos a página estar
// montada para procurar o header
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', comecar);
} else {
    comecar();
}

function comecar() {
    if (!raiz.classList.contains('com-splash')) {
        return;
    }
    try {
        montarSplash();
        iniciarSplash();
    } catch (erro) {
        // Algo deu errado: melhor a página sem abertura do que presa na tela preta
        console.error(erro);
        raiz.classList.remove('com-splash');
        if (splash) {
            splash.remove();
        }
    }
}

// ---- 0) Montagem --------------------------------------------------------------
// Marca os traços do logotipo para o JS saber o que desenhar: o primeiro path é
// o nome (assinatura__traco) e os outros são os pingos dos "i" (assinatura__pingo).
// pathLength="1" faz o comprimento de cada traço valer 1, então a escrita
// trabalha com porcentagem (0 a 1) sem precisar medir o desenho.
// Não muda nada na aparência do logotipo.
function marcarTracos(svg) {
    svg.querySelectorAll('path').forEach(function (path, indice) {
        path.classList.add(indice === 0 ? 'assinatura__traco' : 'assinatura__pingo');
        path.setAttribute('pathLength', '1');
    });
}

// Cria a tela da abertura (fundo preto, assinatura gigante e "role para baixo")
// e a põe no começo do <body>. A assinatura é uma cópia do logotipo do header.
function montarSplash() {
    const logoHeader = document.querySelector('.cabecalho__assinatura');
    marcarTracos(logoHeader);

    splash = document.createElement('div');
    splash.className = 'splash';
    splash.setAttribute('aria-hidden', 'true');   // decorativa: o nome acessível fica no link do header
    splash.innerHTML =
        '<div class="splash__fundo"></div>' +
        '<div class="splash__palco"></div>' +
        '<div class="splash__indicador">' +
            '<span>Role para baixo</span>' +
            '<span class="splash__linha"></span>' +
        '</div>';

    const assinatura = logoHeader.cloneNode(true);
    assinatura.setAttribute('class', 'assinatura splash__assinatura');
    splash.querySelector('.splash__palco').appendChild(assinatura);

    document.body.prepend(splash);
    raiz.classList.add('splash-montada');   // a cobertura preta provisória (splash.css) já pode sair
}

function iniciarSplash() {
    const assinatura = splash.querySelector('.splash__assinatura');
    const traco = assinatura.querySelector('.assinatura__traco');
    const pingos = Array.from(assinatura.querySelectorAll('.assinatura__pingo'));
    const header = document.querySelector('.cabecalho');

    let escritaPronta = false;
    let saindo = false;
    let quadroEscrita = null;

    // ---- 1) Escrita -------------------------------------------------------
    function definirTraco(elemento, quanto) {
        // Padrão "1 2": o espaço maior que a linha evita sobra visível no fim
        elemento.setAttribute('stroke-dasharray', '1 2');
        elemento.setAttribute('stroke-dashoffset', (1 - quanto).toFixed(4));
    }

    // Caneta de verdade: começa devagar, acelera no meio e desacelera no fim
    function suavizar(t) {
        return t < 0.5 ? 2 * t * t : 1 - Math.pow(-2 * t + 2, 2) / 2;
    }

    function completarEscrita() {
        cancelAnimationFrame(quadroEscrita);
        definirTraco(traco, 1);
        pingos.forEach(function (pingo) {
            definirTraco(pingo, 1);
        });
        escritaPronta = true;
        splash.classList.add('splash--pronta');
    }

    function escrever() {
        definirTraco(traco, 0);
        pingos.forEach(function (pingo) {
            definirTraco(pingo, 0);
        });

        const duracaoTotal = DURACAO_TRACO + pingos.length * DURACAO_PINGO;
        let inicio = null;

        // A cada quadro, calcula quanto tempo passou e quanto do traço mostrar.
        // Primeiro o traço principal; depois cada pingo, na ordem.
        function quadro(agora) {
            if (inicio === null) {
                inicio = agora + PAUSA_ANTES;
            }
            const passado = Math.max(0, agora - inicio);

            definirTraco(traco, suavizar(Math.min(passado / DURACAO_TRACO, 1)));
            pingos.forEach(function (pingo, indice) {
                const inicioPingo = DURACAO_TRACO + indice * DURACAO_PINGO;
                const t = Math.min(Math.max((passado - inicioPingo) / DURACAO_PINGO, 0), 1);
                definirTraco(pingo, t);
            });

            if (passado < duracaoTotal) {
                quadroEscrita = requestAnimationFrame(quadro);
            } else {
                completarEscrita();
            }
        }
        quadroEscrita = requestAnimationFrame(quadro);
    }

    // ---- 3) Saída -----------------------------------------------------------
    function sair() {
        if (saindo) {
            return;
        }
        saindo = true;

        // Se a pessoa rolar antes de a escrita terminar, completamos na hora
        if (!escritaPronta) {
            completarEscrita();
        }
        removerEscutas();
        splash.classList.add('splash--saindo');

        // Sem o GSAP (o arquivo não carregou): saída simples, só com fade
        if (!window.gsap) {
            splash.classList.add('splash--esmaecendo');
            setTimeout(finalizarComFade, 320);
            return;
        }
        sairAnimado();
    }

    function sairAnimado() {
        const largura = splash.clientWidth;
        const altura = splash.clientHeight;
        const alturaHeader = header.offsetHeight;
        const r = RAIO_BOLINHA;

        // A forma do fundo preto, em px. Na íris é um círculo (raio);
        // depois, um retângulo de cantos redondos com centro (cx, cy) e
        // meias-medidas (rx, ry): bolinha, pílula e, no fim, a barra do header.
        // No fim da íris os dois formatos são o mesmo círculo, então a troca
        // de um para o outro não aparece.
        const raioInicial = Math.hypot(largura, altura) / 2 + 1;
        const forma = {
            raio: raioInicial,
            cx: largura / 2,
            cy: altura / 2,
            rx: r,
            ry: r,
        };

        // A forma é o próprio fundo preto. Recebe a distância de cada borda da
        // tela (valores negativos passam da borda) e o arredondamento dos cantos:
        //   posição -> transform: translate (movido pela placa de vídeo, numa
        //              camada própria: não redesenha a página, então a bolinha
        //              subindo não deixa rastros; veja .splash__fundo no CSS)
        //   tamanho -> width e height
        //   cantos  -> border-radius
        // (No projeto completo isto era um clip-path na splash inteira, mas o
        // Chrome/Edge deixava rastros da borda do recorte na página,
        // principalmente com a escala de tela do Windows em 125% ou 150%.)
        //
        // Duas proteções a mais contra os rastros (que dependem da escala de
        // tela do Windows, então não aparecem em todo computador):
        //  1. Pixels da tela: com a escala em 125%, 1px do CSS vale 1,25 pixel
        //     da tela, e uma borda em fração de pixel faz o navegador "esquecer"
        //     de apagar uma fileira de pixels por onde a bolinha passou.
        //     alinhar() arredonda para o pixel real da tela (1 / devicePixelRatio).
        //  2. Redesenho completo: a cada quadro a splash alterna a opacidade
        //     entre 1 e 0,999 (invisível a olho nu). Isso obriga o navegador a
        //     redesenhar a tela inteira, apagando qualquer resto do quadro anterior.
        const fundo = splash.querySelector('.splash__fundo');
        const pixelDaTela = 1 / (window.devicePixelRatio || 1);
        function alinhar(valor) {
            return Math.round(valor / pixelDaTela) * pixelDaTela;
        }
        let quadroPar = false;
        function aplicarForma(topo, direita, base, esquerda, canto) {
            const x = alinhar(esquerda);
            const y = alinhar(topo);
            fundo.style.transform = 'translate(' + x + 'px, ' + y + 'px)';
            fundo.style.width = (alinhar(largura - direita) - x) + 'px';
            fundo.style.height = (alinhar(altura - base) - y) + 'px';
            fundo.style.borderRadius = canto + 'px';
            quadroPar = !quadroPar;
            splash.style.opacity = quadroPar ? '0.999' : '1';
        }

        // A splash (transparente fora do fundo) ainda cobre a tela inteira:
        // durante a saída ela deixa os cliques passarem para a página
        splash.style.pointerEvents = 'none';

        function desenhar() {
            if (linha.time() < SAIDA.iris) {
                // Círculo: um quadrado de lado 2 x raio, com o canto = raio
                aplicarForma(forma.cy - forma.raio, largura - forma.cx - forma.raio,
                             altura - forma.cy - forma.raio, forma.cx - forma.raio, forma.raio);
                // A assinatura encolhe na mesma proporção do círculo: fica
                // sempre inteira dentro dele, sem pedaços sobrando na borda
                assinatura.style.transform = 'scale(' + (forma.raio / raioInicial) + ')';
                return;
            }
            const topo = forma.cy - forma.ry;
            const base = altura - forma.cy - forma.ry;
            const esquerda = forma.cx - forma.rx;
            const direita = largura - forma.cx - forma.rx;
            // Cantos redondos enquanto a forma "flutua"; retos quando ela
            // encosta nas bordas da tela, como líquido enchendo o header
            const encosto = gsap.utils.clamp(0, 1, Math.min(esquerda, direita) / (alturaHeader / 2));
            const canto = Math.min(forma.rx, forma.ry) * encosto;
            aplicarForma(topo, direita, base, esquerda, canto);
        }

        const linha = gsap.timeline({ onUpdate: desenhar, onComplete: pousar });

        // a) Íris
        linha.to(forma, { raio: r, duration: SAIDA.iris, ease: 'power4.inOut' }, 0);
        linha.to(assinatura, { opacity: 0, duration: SAIDA.iris * 0.3, ease: 'power1.in' }, SAIDA.iris * 0.6);

        // b) Respiro
        const inicioRespiro = SAIDA.iris;
        linha.to(forma, { rx: r * 1.3, ry: r * 0.75, cy: altura / 2 + 5, duration: SAIDA.respiro, ease: 'power2.out' }, inicioRespiro);

        // c) Subida: o esticão é maior no meio do voo, quando ela está mais rápida
        const inicioSubida = inicioRespiro + SAIDA.respiro;
        linha.to(forma, { cy: alturaHeader / 2, duration: SAIDA.subida, ease: 'power3.inOut' }, inicioSubida);
        linha.to(forma, { rx: r * 0.72, ry: r * 1.45, duration: SAIDA.subida * 0.45, ease: 'power2.in' }, inicioSubida);
        linha.to(forma, { rx: r, ry: r, duration: SAIDA.subida * 0.55, ease: 'power2.out' }, inicioSubida + SAIDA.subida * 0.45);

        // d) Abertura: começa um instante antes de a subida parar, para o
        //    movimento não "travar" no meio
        const inicioAbertura = inicioSubida + SAIDA.subida - 0.05;
        linha.to(forma, { rx: largura / 2, duration: SAIDA.abertura, ease: 'power3.out' }, inicioAbertura);
        linha.to(forma, { ry: alturaHeader / 2, duration: SAIDA.abertura * 0.6, ease: 'power3.out' }, inicioAbertura);
    }

    // e) Pouso: o fundo preto já é exatamente a barra do header.
    //    Trocamos uma pelo outro e escrevemos o logotipo do header.
    function pousar() {
        const tracoLogo = header.querySelector('.cabecalho__assinatura .assinatura__traco');
        const pingosLogo = Array.from(header.querySelectorAll('.cabecalho__assinatura .assinatura__pingo'));
        const pecasLogo = [tracoLogo].concat(pingosLogo);
        const acoes = header.querySelector('.cabecalho__acoes');
        const lapiseira = header.querySelector('.cabecalho__lapiseira');

        // Estado inicial aplicado antes de o header aparecer (no mesmo quadro):
        // logotipo ainda não escrito, botões e lapiseira transparentes.
        // Só opacity nos dois: eles já usam transform para se centralizar.
        pecasLogo.forEach(function (peca) {
            definirTraco(peca, 0);
        });
        gsap.set([acoes, lapiseira], { opacity: 0 });

        finalizar();

        gsap.timeline({
            onComplete: function () {
                pecasLogo.forEach(function (peca) {
                    peca.removeAttribute('stroke-dasharray');
                    peca.removeAttribute('stroke-dashoffset');
                });
                gsap.set([acoes, lapiseira], { clearProps: 'opacity' });
            },
        })
            .to(tracoLogo, { attr: { 'stroke-dashoffset': 0 }, duration: SAIDA.escrita, ease: 'power2.inOut' }, 0)
            .to(pingosLogo, { attr: { 'stroke-dashoffset': 0 }, duration: 0.12, stagger: 0.08, ease: 'power1.out' })
            .to(lapiseira, { opacity: 1, duration: 0.7, ease: 'power2.out' }, 0.2)
            .to(acoes, { opacity: 1, duration: 0.7, ease: 'power2.out' }, 0.35);
    }

    function finalizarComFade() {
        header.classList.add('cabecalho--revelar');
        finalizar();
    }

    // Libera a página, tira a splash e avisa os outros scripts (o abertura.js
    // usa isto para começar a animação do texto de abertura só agora)
    function finalizar() {
        raiz.classList.remove('com-splash');
        splash.remove();
        document.dispatchEvent(new CustomEvent('lapisari:splash-terminou'));
    }

    // ---- Gatilhos da saída ---------------------------------------------------
    const TECLAS_DE_ROLAR = ['ArrowDown', 'PageDown', 'End', ' ', 'Spacebar', 'Enter'];

    function aoRolarRoda(evento) {
        if (evento.deltaY > 0) {
            sair();
        }
    }

    function aoTeclar(evento) {
        if (TECLAS_DE_ROLAR.includes(evento.key) || evento.key === 'Tab') {
            evento.preventDefault();
            sair();
        }
    }

    let toqueInicialY = null;
    function aoTocar(evento) {
        toqueInicialY = evento.touches[0].clientY;
    }
    function aoArrastar(evento) {
        if (toqueInicialY !== null && toqueInicialY - evento.touches[0].clientY > 10) {
            sair();
        }
    }

    function removerEscutas() {
        window.removeEventListener('wheel', aoRolarRoda);
        window.removeEventListener('keydown', aoTeclar);
        window.removeEventListener('touchstart', aoTocar);
        window.removeEventListener('touchmove', aoArrastar);
        splash.removeEventListener('click', sair);
    }

    window.addEventListener('wheel', aoRolarRoda, { passive: true });
    window.addEventListener('keydown', aoTeclar);
    window.addEventListener('touchstart', aoTocar, { passive: true });
    window.addEventListener('touchmove', aoArrastar, { passive: true });
    splash.addEventListener('click', sair);

    // Segurança: se o navegador restaurar uma posição de rolagem depois do
    // carregamento (reload no meio da página), pulamos direto ao estado final
    window.addEventListener('load', function () {
        if (window.scrollY > 0 && !saindo) {
            saindo = true;
            removerEscutas();
            finalizarComFade();
        }
    });

    escrever();
}

})();
