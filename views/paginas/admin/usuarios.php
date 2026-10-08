<?php
/**
 * Tela · Usuários: uma linha por conta, com o papel (cliente/admin)
 *
 * Backend: app/usuarios.php
 * Recebe:
 *   $usuarios todas as contas (id, email, papel)
 *   $mensagem aviso de sucesso ('' se não houver)
 *   $erro     aviso de erro ('' se não houver)
 *   $meu_id   id do admin logado
 *
 * Funções usadas:
 *   e()          escapa o texto antes de mostrar no HTML (views/helpers.php)
 *   trim() (PHP) tira os espaços das pontas do papel antes de comparar
 */
$titulo = 'Usuários · Lapisari';
$estilos = ['forms.css'];
?>
<main class="pagina">
    <div class="container container--estreito">
        <div class="pagina__topo">
            <p class="sobretitulo">Administração</p>
            <h1 class="pagina__titulo">Usuários</h1>
            <p class="pagina__texto">Escolha o papel de cada conta. Administradores veem a barra de administração e podem cadastrar, editar e excluir lapiseiras.</p>
        </div>

        <?php if ($mensagem != ''): ?>
            <p class="alerta alerta--sucesso"><?= e($mensagem) ?></p>
        <?php endif; ?>
        <?php if ($erro != ''): ?>
            <p class="alerta alerta--erro"><?= e($erro) ?></p>
        <?php endif; ?>

        <div class="tabela-container">
            <table class="tabela">
                <thead>
                    <tr>
                        <th scope="col">E-mail</th>
                        <th scope="col">Papel</th>
                        <!-- visualmente-oculto: o título da coluna não aparece, mas o leitor de tela o lê -->
                        <th scope="col"><span class="visualmente-oculto">Ação</span></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td>
                                <?= e($usuario['email']) ?>
                                <?php if ($usuario['id'] == $meu_id): ?>
                                    <span class="selo">Você</span>
                                <?php endif; ?>
                            </td>
                            <!-- Um formulário por linha: envia o id do usuário e o papel escolhido.
                                 trim(): aceita o papel mesmo com espaços sobrando no banco. -->
                            <td colspan="2">
                                <form action="" method="post" class="usuarios__form">
                                    <input type="hidden" name="id" value="<?= $usuario['id'] ?>">
                                    <label class="visualmente-oculto" for="papel-<?= $usuario['id'] ?>">Papel de <?= e($usuario['email']) ?></label>
                                    <select name="papel" id="papel-<?= $usuario['id'] ?>" class="campo__controle">
                                        <option value="cliente" <?= trim($usuario['papel']) == 'cliente' ? 'selected' : '' ?>>Cliente</option>
                                        <option value="admin" <?= trim($usuario['papel']) == 'admin' ? 'selected' : '' ?>>Administrador</option>
                                    </select>
                                    <input type="submit" value="Salvar" class="botao botao--contorno botao--pequeno">
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
