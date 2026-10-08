<?php
/**
 * Campos do formulário de lapiseira
 *
 * Usado no cadastro (admin/cadastrar.php) e na edição (admin/atualizar.php),
 * para não repetir o mesmo HTML nos dois. Antes do include, defina:
 *   $valores      valores dos campos (modelo, marca, bitola, preco, ativo)
 *   $bitolas      opções do select de bitola
 *   $imagem_atual (só na edição) caminho da foto já cadastrada
 *
 * Funções usadas (views/helpers.php e includes/config.php):
 *   e()               escapa o texto antes de pôr no value="" dos campos
 *   formatar_bitola() "0.5" -> "0.5 mm", o texto das opções de bitola
 *   url()             monta o endereço da foto e da imagem padrão
 */
$imagem_atual = $imagem_atual ?? null;   // ??: no cadastro ela não existe, então vira null
?>
<div class="formulario__linha">
    <div class="campo">
        <label for="modelo">Modelo</label>
        <input type="text" name="modelo" id="modelo" class="campo__controle" maxlength="120" required
               value="<?= e($valores['modelo']) ?>">
    </div>

    <div class="campo">
        <label for="marca">Marca</label>
        <input type="text" name="marca" id="marca" class="campo__controle" maxlength="60" required
               value="<?= e($valores['marca']) ?>">
    </div>
</div>

<div class="formulario__linha">
    <div class="campo">
        <label for="bitola">Bitola</label>
        <select name="bitola" id="bitola" class="campo__controle" required>
            <option value="">Selecione</option>
            <?php foreach ($bitolas as $opcao): ?>
                <!-- "selected" deixa marcada a bitola que já estava escolhida -->
                <option value="<?= $opcao ?>" <?= $opcao == $valores['bitola'] ? 'selected' : '' ?>><?= formatar_bitola($opcao) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="campo">
        <label for="preco">Preço (R$)</label>
        <input type="number" name="preco" id="preco" class="campo__controle" min="0" step="any" required
               value="<?= e($valores['preco']) ?>">
    </div>
</div>

<div class="formulario__linha">
    <div class="campo-foto">
        <!-- Prévia da foto: o site.js troca esta imagem assim que um arquivo é escolhido.
             Na edição, data-padrao guarda a imagem padrão para o "Remover a foto atual". -->
        <?php if ($imagem_atual): ?>
            <img src="<?= url($imagem_atual) ?>" alt="" class="campo-foto__previa" id="previa-imagem"
                 data-padrao="<?= url('assets/img/produto-sem-foto.svg') ?>">
        <?php else: ?>
            <img src="<?= url('assets/img/produto-sem-foto.svg') ?>" alt="" class="campo-foto__previa" id="previa-imagem">
        <?php endif; ?>

        <div class="campo">
            <label for="imagem">Foto</label>
            <input type="file" name="imagem" id="imagem" class="campo__controle"
                   accept="image/jpeg,image/png,image/webp" data-previa="previa-imagem"
                   aria-describedby="imagem-ajuda">
            <!-- Por que 1000 x 1000 px: na vitrine a foto aparece num quadrado de
                 ~300px de largura, e telas de alta densidade (2x) pedem ~600px reais.
                 O fundo da vitrine é cinza-claro (#ebe9e3) e a foto aparece inteira,
                 sem cortes: fundo transparente ou desse tom se integra ao card. -->
            <p class="campo__ajuda" id="imagem-ajuda">
                Recomendado: <strong>1000 × 1000 px</strong> (quadrada, mínimo 600 × 600),
                lapiseira centralizada, fundo transparente (PNG/WEBP) ou cinza-claro.
                JPG, PNG ou WEBP, até 2 MB.
                <?php if ($imagem_atual): ?>Deixe vazio para manter a foto atual.<?php endif; ?>
            </p>

            <?php if ($imagem_atual): ?>
                <!-- A lapiseira volta a usar a imagem padrão.
                     Se uma foto nova for escolhida, ela tem prioridade sobre esta opção. -->
                <label class="opcao campo-foto__remover">
                    <input type="checkbox" name="remover_imagem" value="1" data-remover-previa="previa-imagem">
                    Remover a foto atual
                </label>
            <?php endif; ?>
        </div>
    </div>

    <fieldset class="campo">
        <legend class="campo__rotulo">Disponível na vitrine</legend>
        <div class="opcoes">
            <label class="opcao">
                <input type="radio" name="ativo" value="true" required <?= $valores['ativo'] == 'true' ? 'checked' : '' ?>> Sim
            </label>
            <label class="opcao">
                <input type="radio" name="ativo" value="false" <?= $valores['ativo'] == 'false' ? 'checked' : '' ?>> Não
            </label>
        </div>
    </fieldset>
</div>
