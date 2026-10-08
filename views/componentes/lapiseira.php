<?php
/**
 * Desenho da lapiseira em SVG, usado no header
 *
 * Antes do include, defina:
 *   $classe_lapiseira classe do lugar onde ela aparece (tamanho e cor vêm do CSS)
 *                     (?? '': se não for definida, a classe fica vazia)
 */
?>
<svg class="lapiseira <?= $classe_lapiseira ?? '' ?>" viewBox="0 0 300 24" fill="none" stroke="currentColor"
     stroke-width="1.1" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
    <!-- Da esquerda para a direita: botão, corpo com o clipe, grip serrilhado, cone, ponteira e grafite -->
    <rect x="4" y="8.5" width="14" height="7" rx="1.5"/>
    <rect x="18" y="7" width="158" height="10" rx="0.5"/>
    <path d="M23 7 V17"/>
    <path d="M30 7 V4 H116 C120 4 122 5.2 122 7"/>
    <path d="M176 7.5 H236 V16.5 H176"/>
    <path d="M181 7.5 V16.5 M186 7.5 V16.5 M191 7.5 V16.5 M196 7.5 V16.5 M201 7.5 V16.5 M206 7.5 V16.5
             M211 7.5 V16.5 M216 7.5 V16.5 M221 7.5 V16.5 M226 7.5 V16.5 M231 7.5 V16.5" stroke-width="0.6"/>
    <path d="M236 7.5 L258 10.5 V13.5 L236 16.5"/>
    <path d="M258 11.2 H276 V12.8 H258"/>
    <path d="M276 12 H282" stroke-width="1.4"/>
</svg>
