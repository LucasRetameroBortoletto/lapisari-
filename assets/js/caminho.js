// =====================================================================
// Lapisari · caminho.js
// Carregado pelo splash-inicio.js em toda visita à página inicial.
//
// Seção conceitual em "caminho": um caminho de grafite liga os três pilares,
// desenhado conforme a página rola, com a lapiseira do header na ponta,
// escrevendo. Cada pilar (e o companheiro dele) entra quando a linha chega nele.
//
//   ┌──────── pilar 1 ────┐        marcas
//     rabisco             │
//     bitolas             └──────── pilar 2 ─────────┐
//   ┌────────────────────────────────────────────────┘
//     pilar 3                         bilhete
//
// 0) Montagem: os ícones dos pilares e os "companheiros" (marcas, bitolas e
//    bilhete) são decorativos e só existem neste modo, então este arquivo os
//    cria. Assim o HTML da página (views/paginas/inicio.php) não muda.
// 1) O caminho: calculado a partir da posição real dos pilares, então se
//    ajusta a qualquer largura de tela.
// 2) A rolagem desenha o caminho (ScrollTrigger com scrub, que suaviza: o
//    desenho "persegue" a rolagem em vez de pular junto com ela).
//
// Ferramentas do GSAP usadas (assets/js/libs):
//   ScrollTrigger  liga uma animação à rolagem;
//   SplitText      separa um texto em palavras, cada uma numa máscara;
//   DrawSVG        desenha um traço SVG do começo ao fim.
//
// Sem GSAP (o arquivo não carregou): nada disto roda e os pilares ficam
// como sempre, lado a lado e completos. Com ?sem-animacao no endereço, este
// arquivo nem é carregado (veja o splash-inicio.js).
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

const secaoConceito = document.querySelector('.conceito');
const grupoPilares = document.querySelector('.pilares');
const pilares = Array.from(document.querySelectorAll('.pilares .pilar'));
if (!secaoConceito || !grupoPilares || pilares.length !== 3
    || !window.gsap || !window.ScrollTrigger || !window.SplitText || !window.DrawSVGPlugin) {
    return;
}
gsap.registerPlugin(ScrollTrigger, SplitText, DrawSVGPlugin);

const NS = 'http://www.w3.org/2000/svg';
const RAIO = 28;          // arredondamento das curvas do caminho (px)

// Peso dos trechos na horizontal: andar 1px para o lado consome 0,5px de
// rolagem. Assim os trechos horizontais (longos) não passam rápido demais
// nem demoram demais em relação aos verticais.
const PESO_HORIZONTAL = 0.5;

// A linha começa quando o topo dos pilares chega a 62% da altura da tela e
// termina quando o topo do último pilar chega a 12% (logo abaixo do header).
// Terminar mais perto do topo faz o desenho pedir mais rolagem sem aumentar
// nada na página: o último trecho continua sendo desenhado enquanto o pilar 3
// sobe pela tela.
const INICIO_NA_TELA = 0.62;
const FIM_NA_TELA = 0.12;

// Quanto do trecho [inicio, fim] já passou, de 0 a 1
function trecho(valor, inicio, fim) {
    return Math.min(Math.max((valor - inicio) / (fim - inicio), 0), 1);
}

// Separa em palavras com máscara (SplitText). As máscaras ficam com a classe
// "palavra-mask" (folga para p, ç, j no splash.css). O SplitText mantém o
// texto original num aria-label: leitores de tela leem a frase inteira.
function palavras(elemento) {
    return SplitText.create(elemento, { type: 'words', mask: 'words', wordsClass: 'palavra' }).words;
}


// ---- 0) Montagem ---------------------------------------------------------------
// Ícones (um por pilar, em traço fino). pathLength="1" em cada traço: é por
// ele que o desenho é encontrado mais abaixo (função tracos).
const ICONES = [
    // 01 Curadoria: um diamante
    '<path pathLength="1" d="M8 15 L14 7 H26 L32 15 L20 33 Z"/>' +
    '<path pathLength="1" d="M8 15 H32 M14 7 L17 15 L20 33 L23 15 L26 7"/>',
    // 02 Mecanismo: uma mira de precisão
    '<circle pathLength="1" cx="20" cy="20" r="12"/>' +
    '<circle pathLength="1" cx="20" cy="20" r="4"/>' +
    '<path pathLength="1" d="M20 4 V11 M20 29 V36 M4 20 H11 M29 20 H36"/>',
    // 03 Atendimento: a ponta de uma caneta-tinteiro
    '<path pathLength="1" d="M20 4 L29 17 L24 31 H16 L11 17 Z"/>' +
    '<path pathLength="1" d="M20 4 V21"/>' +
    '<circle pathLength="1" cx="20" cy="23" r="2"/>' +
    '<path pathLength="1" d="M15 36 H25"/>',
];

// Bitolas: uma linha por bitola, com a espessura proporcional à real (bitola x 5)
const BITOLAS = ['0.3', '0.5', '0.7', '0.9', '1.3', '2.0'].map(function (bitola) {
    return '<div class="detalhe-bitola">' +
               '<span class="detalhe-bitola__valor">' + bitola + ' mm</span>' +
               '<svg viewBox="0 0 400 12" preserveAspectRatio="none" focusable="false">' +
                   '<path pathLength="1" d="M4 6 H396" stroke-width="' + (Number(bitola) * 5) + '"/>' +
               '</svg>' +
           '</div>';
}).join('');

// Companheiros: o de cada pilar fica logo depois dele, na mesma linha da escada
const COMPANHEIROS = [
    // ao lado do pilar 1: marcas da casa
    '<div class="detalhe detalhe--marcas" aria-hidden="true">' +
        '<p class="detalhe__rotulo">Na casa</p>' +
        '<ul class="detalhe-marcas"><li>Staedtler</li><li>Pentel</li><li>Rotring</li><li>uni</li></ul>' +
    '</div>',
    // ao lado do pilar 2: as bitolas
    '<div class="detalhe detalhe--bitolas" aria-hidden="true">' +
        '<p class="detalhe__rotulo">Bitolas</p>' + BITOLAS +
    '</div>',
    // ao lado do pilar 3: bilhete escrito à mão (fonte do logotipo)
    '<div class="detalhe detalhe--nota" aria-hidden="true">' +
        '<p class="detalhe-nota"><span>O traço certo</span><span>começa na escolha certa.</span></p>' +
        '<p class="detalhe__rotulo">Equipe Lapisari</p>' +
    '</div>',
];

pilares.forEach(function (pilar, i) {
    const icone = document.createElementNS(NS, 'svg');
    icone.setAttribute('class', 'pilar__icone');
    icone.setAttribute('viewBox', '0 0 40 40');
    icone.setAttribute('fill', 'none');
    icone.setAttribute('stroke', 'currentColor');
    icone.setAttribute('stroke-width', '1');
    icone.setAttribute('stroke-linecap', 'round');
    icone.setAttribute('stroke-linejoin', 'round');
    icone.setAttribute('aria-hidden', 'true');
    icone.setAttribute('focusable', 'false');
    icone.innerHTML = ICONES[i];
    pilar.prepend(icone);

    pilar.insertAdjacentHTML('afterend', COMPANHEIROS[i]);
});

secaoConceito.classList.add('conceito--caminho');

const detalhes = pilares.map(function (pilar) {
    return pilar.nextElementSibling;
});

// O SVG do caminho cobre o grupo de pilares inteiro (camada por trás do texto)
const svgCaminho = document.createElementNS(NS, 'svg');
svgCaminho.setAttribute('class', 'caminho-grafite');
svgCaminho.setAttribute('aria-hidden', 'true');
svgCaminho.setAttribute('focusable', 'false');
const linha = document.createElementNS(NS, 'path');
linha.setAttribute('class', 'caminho-grafite__linha');
svgCaminho.appendChild(linha);
grupoPilares.prepend(svgCaminho);

// ---- Rabisco de "teste do grafite" embaixo do pilar 1 ----------------------
// Como quando se testa uma caneta nova: uma sequência de laçadas ("llll").
// A forma é uma trocoide (a curva que um ponto de uma roda descreve quando
// ela rola): x = R·t − r·sen(t), y = −r·cos(t). Com r maior que R, cada
// volta forma uma laçada. A altura das laçadas varia um pouco (duas ondas
// lentas somadas), para parecer feito à mão sem perder a regularidade.
// Ele é desenhado junto com a descida da linha até o pilar 2.
const rabisco = (function () {
    const LACADAS = 6;
    const R = 13;               // avanço para a direita a cada volta (x 2π)
    const PASSOS = 420;         // quantidade de pontos do desenho
    const pontosRabisco = [];
    for (let p = 0; p <= PASSOS; p++) {
        const t = (p / PASSOS) * LACADAS * 2 * Math.PI;
        const r = 24 + 6 * Math.sin(t / 3.3) + 3 * Math.sin(t / 1.7);
        const x = R * t - r * Math.sin(t);
        const y = 50 - r * Math.cos(t) + 5 * Math.sin(t / 4.1);
        pontosRabisco.push(x.toFixed(1) + ' ' + y.toFixed(1));
    }
    const largura = Math.ceil(R * LACADAS * 2 * Math.PI + 40);

    const svg = document.createElementNS(NS, 'svg');
    svg.setAttribute('class', 'rabisco-grafite');
    svg.setAttribute('viewBox', '-30 10 ' + largura + ' 82');
    svg.setAttribute('aria-hidden', 'true');
    svg.setAttribute('focusable', 'false');
    const tracoRabisco = document.createElementNS(NS, 'path');
    tracoRabisco.setAttribute('d', 'M ' + pontosRabisco.join(' L '));
    svg.appendChild(tracoRabisco);
    pilares[0].appendChild(svg);    // último item do pilar 1 (linha própria no grid)
    return tracoRabisco;
})();
let trechoRabisco = [0, 1];         // comprimentos da linha em que o rabisco é desenhado

// ---- A lapiseira que escreve: um clone da lapiseira do header ----------------
// Ela acompanha a direção da linha: inclinada 24° escrevendo para a direita,
// um pouco mais "em pé" nas descidas e, no último trecho (para a esquerda),
// espelhada, como se virasse na mão. A inclinação e o espelhamento usam
// gsap.quickTo: cada mudança é animada até o novo valor, então a lapiseira
// gira suave nas curvas em vez de "estalar" de um ângulo para o outro.
let lapiseira = null;
let girarPara = null;
let espelharPara = null;
const lapiseiraHeader = document.querySelector('.cabecalho__lapiseira');
if (lapiseiraHeader) {
    lapiseira = lapiseiraHeader.cloneNode(true);
    lapiseira.setAttribute('class', 'lapiseira caminho-grafite__lapiseira');
    grupoPilares.appendChild(lapiseira);
    gsap.set(lapiseira, { rotation: 24 });
    girarPara = gsap.quickTo(lapiseira, 'rotation', { duration: 0.5, ease: 'power3.out' });
    espelharPara = gsap.quickTo(lapiseira, 'scaleX', { duration: 0.45, ease: 'power2.inOut' });
}


// ---- 1) Geometria do caminho (refeita a cada refresh) ---------------------------
let comprimentoTotal = 1;
let amostras = [];          // pontos ao longo da linha: { comprimento, peso }
let pesoTotal = 1;
let chegadas = [];          // comprimento em que a linha chega em cada pilar

// Monta o "d" do path a partir da posição dos pilares:
// topo do 1 (esquerda -> direita), desce, topo do 2 (-> direita),
// desce, topo do 3 (direita -> esquerda). Cantos arredondados (RAIO).
function montarCaminho() {
    const base = grupoPilares.getBoundingClientRect();
    const caixas = pilares.map(function (pilar) {
        const r = pilar.getBoundingClientRect();
        return { esq: r.left - base.left, dir: r.right - base.left, topo: r.top - base.top };
    });

    svgCaminho.setAttribute('viewBox', '0 0 ' + base.width + ' ' + base.height);
    svgCaminho.setAttribute('width', base.width);
    svgCaminho.setAttribute('height', base.height);

    // Pontos por onde a linha passa (os cantos da "escada"):
    // começa no canto esquerdo do pilar 1 e vai até o direito; em cada
    // pilar seguinte, desce reto até o topo dele e segue para a borda
    // direita (ou, no último, volta até a borda esquerda)
    const pontos = [[caixas[0].esq, caixas[0].topo], [caixas[0].dir, caixas[0].topo]];
    for (let i = 1; i < caixas.length; i++) {
        const x = pontos[pontos.length - 1][0];
        const ultimo = i === caixas.length - 1;
        pontos.push([x, caixas[i].topo]);
        pontos.push([ultimo ? caixas[i].esq : caixas[i].dir, caixas[i].topo]);
    }

    // Transforma os pontos em um path com curvas nos cantos
    let d = 'M ' + pontos[0][0] + ' ' + pontos[0][1];
    for (let i = 1; i < pontos.length; i++) {
        const [x, y] = pontos[i];
        const proximo = pontos[i + 1];
        if (!proximo) {
            d += ' L ' + x + ' ' + y;
            break;
        }
        const [xa, ya] = pontos[i - 1];
        const [xp, yp] = proximo;
        // recua RAIO antes do canto e retoma RAIO depois, com uma curva no meio
        const r1 = Math.min(RAIO, Math.hypot(x - xa, y - ya) / 2);
        const r2 = Math.min(RAIO, Math.hypot(xp - x, yp - y) / 2);
        const antes = [x - Math.sign(x - xa) * r1, y - Math.sign(y - ya) * r1];
        const depois = [x + Math.sign(xp - x) * r2, y + Math.sign(yp - y) * r2];
        d += ' L ' + antes[0] + ' ' + antes[1] + ' Q ' + x + ' ' + y + ' ' + depois[0] + ' ' + depois[1];
    }
    linha.setAttribute('d', d);

    // Amostras ao longo da linha: para cada pedaço, quanto de rolagem ele
    // "custa" (peso). Vertical custa o próprio tamanho; horizontal, metade.
    comprimentoTotal = linha.getTotalLength() || 1;
    amostras = [{ comprimento: 0, peso: 0 }];
    let anterior = linha.getPointAtLength(0);
    let peso = 0;
    const PASSO = 8;
    for (let c = PASSO; c <= comprimentoTotal + PASSO; c += PASSO) {
        const comprimento = Math.min(c, comprimentoTotal);
        const ponto = linha.getPointAtLength(comprimento);
        peso += Math.abs(ponto.y - anterior.y) + Math.abs(ponto.x - anterior.x) * PESO_HORIZONTAL;
        amostras.push({ comprimento: comprimento, peso: peso });
        anterior = ponto;
    }
    pesoTotal = peso || 1;

    // Onde a linha chega em cada pilar: o início do trecho sobre ele
    // (+ 60px, para ele aparecer quando a ponta já estiver passando por cima)
    chegadas = caixas.map(function (caixa, i) {
        if (i === 0) {
            return 60;
        }
        let achado = comprimentoTotal;
        for (let a = 0; a < amostras.length; a++) {
            const ponto = linha.getPointAtLength(amostras[a].comprimento);
            if (ponto.y >= caixa.topo - 0.5) {
                achado = amostras[a].comprimento;
                break;
            }
        }
        return achado + 60;
    });

    // Rabisco: começa quando a linha sai do topo do pilar 1 (primeiro ponto
    // abaixo dele) e termina quando ela chega no topo do pilar 2
    let inicioDescida = 0;
    for (let a = 0; a < amostras.length; a++) {
        if (linha.getPointAtLength(amostras[a].comprimento).y > caixas[0].topo + 1) {
            inicioDescida = amostras[a].comprimento;
            break;
        }
    }
    trechoRabisco = [inicioDescida, Math.max(chegadas[1] - 60, inicioDescida + 1)];
}

// Converte o "peso" (rolagem) em comprimento de linha, usando as amostras
function comprimentoPeloPeso(pesoAlvo) {
    for (let a = 1; a < amostras.length; a++) {
        if (amostras[a].peso >= pesoAlvo) {
            const antes = amostras[a - 1];
            const depois = amostras[a];
            const t = trecho(pesoAlvo, antes.peso, depois.peso);
            return antes.comprimento + (depois.comprimento - antes.comprimento) * t;
        }
    }
    return comprimentoTotal;
}

// ---- Entrada de cada pilar e do companheiro (timelines pausadas) --------------
// Cada timeline é tocada quando a linha chega no pilar e tocada ao contrário
// (duas vezes mais rápido) se a pessoa rola de volta.
// Os traços desenhados com DrawSVG não podem ter pathLength="1": o DrawSVG
// mede o comprimento real. Por isso o atributo sai aqui.
function tracos(elemento, seletor) {
    return Array.from(elemento.querySelectorAll(seletor)).map(function (tracoSvg) {
        tracoSvg.removeAttribute('pathLength');
        return tracoSvg;
    });
}

const entradas = pilares.map(function (pilar, i) {
    const tl = gsap.timeline({ paused: true, defaults: { ease: 'expo.out' } })
        .from(palavras(pilar.querySelector('.pilar__numero')), { yPercent: 110, duration: 0.9 })
        .from(tracos(pilar, '.pilar__icone [pathLength]'),
            { drawSVG: 0, duration: 1.1, ease: 'power2.inOut', stagger: 0.12 }, 0.15)
        .from(palavras(pilar.querySelector('.pilar__titulo')), { yPercent: 110, duration: 0.9, stagger: 0.08 }, 0.2)
        .from(pilar.querySelector('.pilar__texto'), { opacity: 0, y: 16, duration: 0.8 }, 0.5);

    const detalhe = detalhes[i];
    const rotulos = detalhe.querySelectorAll('.detalhe__rotulo');
    if (rotulos.length) {
        tl.from(rotulos, { opacity: 0, duration: 0.7, ease: 'power1.out' }, 0);
    }

    if (detalhe.classList.contains('detalhe--marcas')) {
        // Marcas sobem pela máscara, uma depois da outra
        const itens = Array.from(detalhe.querySelectorAll('.detalhe-marcas li')).map(function (item) {
            return palavras(item);
        });
        tl.from(itens, { yPercent: 110, duration: 1, stagger: 0.12 }, 0.25);
    } else if (detalhe.classList.contains('detalhe--bitolas')) {
        // Bitolas: cada traço é desenhado da esquerda para a direita,
        // do mais fino ao mais grosso, com o valor aparecendo junto
        tl.from(tracos(detalhe, '[pathLength]'), { drawSVG: 0, duration: 0.9, ease: 'power2.inOut', stagger: 0.14 }, 0.3)
          .from(detalhe.querySelectorAll('.detalhe-bitola__valor'), { opacity: 0, duration: 0.6, stagger: 0.14, ease: 'power1.out' }, 0.3);
    } else if (detalhe.classList.contains('detalhe--nota')) {
        // Bilhete: cada linha é "escrita" da esquerda para a direita (o
        // recorte abre como a caneta andando), uma depois da outra
        tl.fromTo(detalhe.querySelectorAll('.detalhe-nota span'),
            { clipPath: 'inset(-20% 100% -20% 0%)' },
            { clipPath: 'inset(-20% -5% -20% 0%)', duration: 1.3, ease: 'sine.inOut', stagger: 1.25 }, 0.2);
    }
    return tl;
});

const visiveis = pilares.map(function () {
    return false;
});

function mostrarPilar(i, visivel) {
    if (visiveis[i] === visivel) {
        return;
    }
    visiveis[i] = visivel;
    if (visivel) {
        entradas[i].timeScale(1).play();
    } else {
        entradas[i].timeScale(2).reverse();
    }
}

// ---- Um quadro do desenho, para um progresso da rolagem (0 a 1) -------------
function desenharQuadro(progresso) {
    const comprimento = comprimentoPeloPeso(progresso * pesoTotal);
    const quanto = comprimento / comprimentoTotal;

    linha.setAttribute('stroke-dasharray', comprimentoTotal + ' ' + (comprimentoTotal * 2));
    linha.setAttribute('stroke-dashoffset', (comprimentoTotal - comprimento).toFixed(2));
    linha.style.visibility = comprimento > 0 ? 'visible' : 'hidden';

    // Rabisco: DrawSVG na porcentagem que corresponde a este trecho da linha
    const rabiscado = trecho(comprimento, trechoRabisco[0], trechoRabisco[1]);
    gsap.set(rabisco, { drawSVG: (rabiscado * 100) + '%', visibility: rabiscado > 0 ? 'visible' : 'hidden' });

    pilares.forEach(function (pilar, i) {
        mostrarPilar(i, comprimento >= chegadas[i]);
    });

    // Lapiseira: ponta do grafite sobre o fim do traço; aparece no começo
    // e some quando a linha termina
    if (lapiseira) {
        const visivel = trecho(quanto, 0, 0.04) * (1 - trecho(quanto, 0.985, 1));
        gsap.set(lapiseira, { opacity: visivel });
        if (visivel > 0) {
            const ponto = linha.getPointAtLength(comprimento);
            const atras = linha.getPointAtLength(Math.max(0, comprimento - 8));
            gsap.set(lapiseira, { x: ponto.x, y: ponto.y });

            // Direção da linha neste ponto (em graus). Para a direita: 0°;
            // descendo: 90°. Indo para a esquerda, a lapiseira espelha e o
            // ângulo é medido "do lado de lá".
            const dx = ponto.x - atras.x;
            const dy = ponto.y - atras.y;
            const indoParaEsquerda = dx < -0.5;
            const angulo = Math.atan2(dy, indoParaEsquerda ? -dx : dx) * 180 / Math.PI;
            // 24° de inclinação base + 30% da direção da linha (numa descida
            // ela fica mais "em pé", sem girar junto por inteiro)
            const inclinacao = 24 + gsap.utils.clamp(-90, 90, angulo) * 0.3;
            girarPara(indoParaEsquerda ? -inclinacao : inclinacao);
            espelharPara(indoParaEsquerda ? -1 : 1);
        }
    }
}


// ---- 2) Rolagem -> desenho (ScrollTrigger) ------------------------------------
// "estado.progresso" vai de 0 a 1 conforme a rolagem; scrub: 0.6 faz ele
// chegar ao valor da rolagem em ~0,6 s (a suavização). A cada mudança,
// desenharQuadro() redesenha tudo.
const estado = { progresso: 0 };

montarCaminho();

ScrollTrigger.create({
    trigger: grupoPilares,
    // Posições de rolagem (em px) em que a linha começa e termina.
    // O começo nunca é antes de 0: em telas mais baixas, o topo dos pilares
    // já aparece acima dos 62% ao abrir a página, e sem esse limite a linha
    // começaria sozinha. Assim, no topo da página nada é desenhado.
    start: function () {
        const topo = grupoPilares.getBoundingClientRect().top + window.scrollY;
        return Math.max(topo - window.innerHeight * INICIO_NA_TELA, 0);
    },
    end: function (self) {
        const ultimo = pilares[pilares.length - 1].getBoundingClientRect().top + window.scrollY;
        return Math.max(ultimo - window.innerHeight * FIM_NA_TELA, self.start + 1);
    },
    animation: gsap.to(estado, {
        progresso: 1,
        ease: 'none',
        onUpdate: function () {
            desenharQuadro(estado.progresso);
        },
    }),
    scrub: 0.6,
    invalidateOnRefresh: true,
    // Tamanhos mudaram (resize, fontes carregadas): refaz o caminho
    onRefresh: function () {
        montarCaminho();
        desenharQuadro(estado.progresso);
    },
});

desenharQuadro(0);

// Recalcula quando algo muda o tamanho das coisas sem "resize" da janela:
// as fontes carregando (mudam a altura dos textos) e o fim da abertura
// (a barra de rolagem volta e a página fica um pouco mais estreita)
if (document.fonts) {
    document.fonts.ready.then(recalcular);
}
document.addEventListener('lapisari:splash-terminou', recalcular);

// Link para um ponto da página (ex.: index.php#vitrine): o navegador já
// pulou para lá, mas o modo caminho deixou a seção conceitual mais alta e
// empurrou a vitrine para baixo. Recalculamos e voltamos a enquadrar o ponto,
// a não ser que a pessoa já tenha começado a rolar.
let mexeu = false;
['wheel', 'touchmove', 'keydown', 'mousedown'].forEach(function (tipo) {
    window.addEventListener(tipo, function () {
        mexeu = true;
    }, { once: true, passive: true });
});

function recalcular() {
    ScrollTrigger.refresh();
    const alvo = window.location.hash && document.getElementById(window.location.hash.slice(1));
    if (alvo && !mexeu) {
        alvo.scrollIntoView();   // respeita o scroll-margin-top (o header fixo)
    }
}
recalcular();

}

})();
