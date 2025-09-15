<?php
ini_set('session.gc_maxlifetime', 604800); 
session_set_cookie_params(604800);

session_start();
include('../../conecta_db.php');

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$mensagem = "";

if (isset($_POST['atualizar'])) {
    $nc_id = intval($_POST['nc_id']);
    $novo_status = $_POST['status'];

    $sql = "UPDATE nao_conformidades nc
            JOIN auditorias a ON nc.auditoria_id = a.id
            SET nc.status = ?
            WHERE nc.id = ? AND a.usuario_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $novo_status, $nc_id, $usuario_id);
    if ($stmt->execute()) {
        $mensagem = "✅ Status atualizado!";
    } else {
        $mensagem = "Erro ao atualizar status.";
    }
}

$sql = "SELECT nc.id AS nc_id, nc.descricao, nc.status, nc.criado_em, 
            a.titulo_checklist AS checklist, a.id AS auditoria_id, ar.descricao_item
        FROM nao_conformidades nc
        JOIN auditorias a ON nc.auditoria_id = a.id
        JOIN auditoria_respostas ar ON ar.auditoria_id = nc.auditoria_id AND ar.item_id = nc.item_id
        WHERE a.usuario_id = ?
        ORDER BY nc.criado_em ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$nc_list = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Auditoria | Não Conformidades</title>
    <link href="../../styles/pages/nao_conformidades/nao_conformidades.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <header class="header">
        <div class="user-info">
            <p>Nome: <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?></p>
            <p>E-mail: <?php echo htmlspecialchars($_SESSION['usuario_email']); ?></p>
        </div>

        <h1>Sistema de Auditoria</h1>

        <div>
            <a href="logout.php" class="logout-btn" title="Sair">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </header>

    <div class="container">
        <h2>Não Conformidades</h2>
        
        <?php if ($nc_list->num_rows > 0) { ?>
            <div class="table-wrapper">
                <table>
                    <tr>
                        <th>Nº da Auditoria</th>
                        <th>Checklist</th>
                        <th>Item</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                    <?php while ($nc = $nc_list->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $nc['auditoria_id']; ?></td>
                            <td><?php echo htmlspecialchars($nc['checklist']); ?></td>
                            <td><?php echo htmlspecialchars($nc['descricao_item']); ?></td>
                            <td>
                                <form method="POST" action="">
                                    <input type="hidden" name="nc_id" value="<?php echo $nc['nc_id']; ?>">
                                    <select name="status" <?php echo in_array($nc['status'], ['RESOLVIDA', 'NAO RESOLVIDA']) ? 'disabled' : ''; ?>>
                                        <option value="ABERTA" <?php if($nc['status']=="ABERTA") echo "selected"; ?>>Aberta</option>
                                        <option value="EM ANDAMENTO" <?php if($nc['status']=="EM ANDAMENTO") echo "selected"; ?>>Em andamento</option>
                                        <option value="RESOLVIDA" <?php if($nc['status']=="RESOLVIDA") echo "selected"; ?>>Resolvida</option>
                                        <option value="NAO RESOLVIDA" <?php if($nc['status']=="NAO RESOLVIDA") echo "selected"; ?>>Não resolvida</option>
                                    </select>
                                    <?php if (!in_array($nc['status'], ['RESOLVIDA', 'NAO RESOLVIDA'])) { ?>
                                        <button type="submit" name="atualizar" title="Salvar status" class="save-button">
                                            <i class="fas fa-save"></i>
                                        </button>
                                    <?php } ?>
                                </form>
                            </td>
                            <td>
                                <?php if ($nc['status'] !== 'RESOLVIDA') { ?>
                                    <a href="enviar_nc.php?nc_id=<?php echo $nc['nc_id']; ?>" class="enviar-btn" title="Enviar por e-mail">
                                        <i class="fas fa-envelope"></i>
                                    </a>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        <?php } else { ?>
            <p class="sem-nao-conformidades">Nenhuma não conformidade encontrada.</p>
        <?php } ?>

        <div style="text-align:center;">
            <a class="voltar" href="dashboard.php">⬅ Voltar</a>
        </div>
    </div>

    <footer>
        &copy; <?php echo date('Y'); ?> Sistema de Auditoria. Todos os direitos reservados.
        <br>Desenvolvido por: Arthur Rodrigues, Jean Inácio, João Gabriel e Stefany Carlos.
    </footer>
</body>
</html>