<?php
/**
 * Tela · Relatório de lapiseiras
 *
 * Só leitura: para editar ou excluir, use o ID da tabela nas telas
 * Atualizar e Excluir (barra do admin).
 *
 * Backend: app/select.php
 * Recebe:
 *   $lapiseiras   todas as lapiseiras (na vitrine e fora dela)
 *   $total_ativas quantas estão na vitrine
 *
 * Funções usadas:
 *   plural()           quantidade + palavra no singular ou no plural (views/helpers.php)
 *   imagem_lapiseira() endereço da foto, ou da imagem padrão quando não há foto (views/helpers.php)
 *   e()                escapa o texto antes de mostrar no HTML (views/helpers.php)
 *   formatar_bitola()  "0.5" -> "0.5 mm" (views/helpers.php)
 *   formatar_preco()   129.9 -> "R$ 129,90" (views/helpers.php)
 *   count() (PHP)      quantos itens a lista tem
 *   strtotime() (PHP)  lê a data e hora que vêm do banco ("2026-10-01 14:30:00")
 *   date() (PHP)       escreve a data no formato pedido: 'd/m/Y' -> "01/10/2026"
 */
$titulo = 'Relatório · Lapisari';
?>
<main class="pagina">
    <div class="container">
        <div class="pagina__topo">
            <p class="sobretitulo">Administração</p>
            <h1 class="pagina__titulo">Relatório</h1>
            <p class="pagina__texto">
                <?= plural(count($lapiseiras), 'lapiseira cadastrada', 'lapiseiras cadastradas') ?>,
                <?= $total_ativas ?> na vitrine.
            </p>
        </div>

        <?php if (!$lapiseiras): ?>
            <p class="pagina__texto">Nenhuma lapiseira cadastrada ainda.</p>
        <?php else: ?>
            <div class="tabela-container">
                <table class="tabela">
                    <thead>
                        <tr>
                            <!-- visualmente-oculto: o título da coluna não aparece, mas o leitor de tela o lê -->
                            <th scope="col"><span class="visualmente-oculto">Foto</span></th>
                            <th scope="col">ID</th>
                            <th scope="col">Modelo</th>
                            <th scope="col">Marca</th>
                            <th scope="col">Bitola</th>
                            <th scope="col" class="tabela__numero">Preço</th>
                            <th scope="col">Vitrine</th>
                            <th scope="col">Cadastro</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lapiseiras as $lapiseira): ?>
                            <tr>
                                <td><img src="<?= imagem_lapiseira($lapiseira) ?>" alt="" class="tabela__miniatura" loading="lazy"></td>
                                <td><?= $lapiseira['id'] ?></td>
                                <td><?= e($lapiseira['modelo']) ?></td>
                                <td><?= e($lapiseira['marca']) ?></td>
                                <td><?= formatar_bitola($lapiseira['bitola']) ?></td>
                                <td class="tabela__numero"><?= formatar_preco($lapiseira['preco']) ?></td>
                                <td>
                                    <span class="selo<?= $lapiseira['ativo'] ? ' selo--ativo' : '' ?>">
                                        <?= $lapiseira['ativo'] ? 'Na vitrine' : 'Fora' ?>
                                    </span>
                                </td>
                                <td><?= date('d/m/Y', strtotime($lapiseira['criado_em'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</main>
