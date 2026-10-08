<?php
/**
 * Rodapé do site
 *
 * Incluído pela moldura (views/layout/pagina.php) em todas as páginas,
 * menos no login e no criar conta. O ano do copyright muda sozinho.
 *
 * Funções usadas:
 *   date('Y') (PHP) o ano atual, com 4 dígitos (2026)
 */
?>
<footer class="rodape">
    <div class="container rodape__conteudo">
        <div>
            <p class="rodape__marca">Lapisari</p>
            <p class="rodape__texto">Revenda autorizada de lapiseiras de marcas oficiais.</p>
        </div>
        <p class="rodape__texto">&copy; <?= date('Y') ?> Lapisari. Todos os direitos reservados.</p>
    </div>
</footer>
