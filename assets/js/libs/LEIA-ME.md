# Bibliotecas de terceiros (assets/js/libs)

Copiadas para dentro do projeto (e não carregadas de um CDN) para o site
funcionar mesmo sem internet, por exemplo num XAMPP em rede local.

Só as animações da página inicial usam estas bibliotecas: quem carrega os
arquivos é o assets/js/splash-inicio.js. As outras páginas não as baixam.

| Arquivo | Biblioteca | Versão | Licença | Usada em |
|---|---|---|---|---|
| gsap.min.js | GSAP (núcleo de animação) | 3.15.0 | GSAP Standard License (gratuita, inclusive comercial) | splash.js, abertura.js, caminho.js |
| ScrollTrigger.min.js | GSAP ScrollTrigger (animação pela rolagem) | 3.15.0 | idem | caminho.js (a linha desenhada pelo scroll) |
| SplitText.min.js | GSAP SplitText (texto em palavras) | 3.15.0 | idem | abertura.js e caminho.js (palavras subindo) |
| DrawSVGPlugin.min.js | GSAP DrawSVG (desenhar traços SVG) | 3.15.0 | idem | caminho.js (ícones, bitolas e rabisco) |

Para atualizar: baixe a versão nova (npm pack gsap) e troque o arquivo
correspondente da pasta dist/ do pacote.
