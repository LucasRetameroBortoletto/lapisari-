<?php
/**
 * Logotipo: "Lapisari" em estilo assinatura, desenhado em SVG
 *
 * Usado no header e no cartão do login. Antes do include, defina:
 *   $classe_assinatura classe do lugar onde ele aparece (tamanho e cor vêm do CSS)
 *                      (?? '': se não for definida, a classe fica vazia)
 */
?>
<svg class="assinatura <?= $classe_assinatura ?? '' ?>" viewBox="30 14 372 172" fill="none" stroke="currentColor"
     stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
    <path d="M 62 112 C 84 100, 116 70, 126 46 C 132 30, 114 22, 106 40
        C 98 60, 96 96, 84 120 C 76 136, 56 142, 46 134 C 40 126, 54 118, 70 122 C 86 127, 100 132, 116 130
        C 126 127, 132 114, 138 102 C 126 94, 110 108, 116 122 C 122 134, 138 120, 144 102 C 142 116, 140 128, 150 128
        C 158 126, 164 112, 168 100 L 158 178 C 160 150, 166 116, 184 102 C 198 92, 206 112, 194 124
        C 184 132, 172 128, 170 122 C 182 128, 196 128, 206 124
        C 212 118, 216 108, 220 100 C 216 112, 214 126, 226 128
        C 234 126, 240 112, 244 100 C 250 108, 256 114, 252 123 C 248 132, 234 130, 232 125 C 240 130, 254 130, 264 126
        C 272 121, 278 110, 284 102 C 272 94, 258 108, 264 122 C 270 134, 286 120, 290 102 C 288 116, 286 128, 296 128
        C 304 124, 308 112, 311 100 C 313 104, 317 106, 321 102 C 324 99, 327 98, 330 99 C 326 110, 324 122, 334 128
        C 340 124, 344 112, 348 100 C 344 112, 342 126, 354 128 C 368 130, 382 118, 392 106"/>
    <!-- Pingos dos dois "i" -->
    <path d="M 221 84 l 1.5 -1.5"/>
    <path d="M 349 84 l 1.5 -1.5"/>
</svg>
