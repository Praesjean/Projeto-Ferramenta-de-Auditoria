<?php
session_start();
include('../../conecta_db.php');

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$mensagem = "";

if (isset($_GET['excluir'])) {
    $id = intval($_GET['excluir']);
    $sql = "DELETE FROM checklists WHERE id = ? AND usuario_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id, $usuario_id);
    if ($stmt->execute()) {
        $mensagem = "Checklist excluído!";
    } else {
        $mensagem = "Erro ao excluir checklist.";
    }
}

$sql = "SELECT * FROM checklists WHERE usuario_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$resultado = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Auditoria | Gerenciar Checklists</title>
    <link href="../../styles/pages/gerenciar_checklists/gerenciar_checklists.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header class="header">
        <div class="user-info">
            <p>Nome: <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?></p>
            <p>E-mail: <?php echo htmlspecialchars($_SESSION['usuario_email']); ?></p>
        </div>

        <h1>Sistema de Auditoria</h1>

        <div>
            <a href="logout.php" class="logout-btn">Sair</a>
        </div>
    </header>

    <div class="container">
        <h2>Gerenciar Checklists</h2>

        <?php if ($resultado->num_rows > 0) { ?>
            <div class="table-wrapper">
                <table>
                    <tr>
                        <th>Título</th>
                        <th>Auditor</th>
                        <th>Criado em</th>
                        <th>Ações</th>
                    </tr>
                    <?php while ($row = $resultado->fetch_assoc()) { ?>
                        <tr>
                            <td class="texto"><?php echo htmlspecialchars($row['titulo']); ?></td>
                            <td class="texto"><?php echo htmlspecialchars($row['auditor']); ?></td>
                            <td class="texto"><?php echo date("d/m/Y H:i:s", strtotime($row['criado_em'])); ?></td>
                            <td class="acoes">
                                <a class="detalhes-btn" title="Editar checklist" href="editar_checklist.php?id=<?php echo $row['id']; ?>">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <button class="excluir-btn" title="Excluir checklist" onclick="confirmarExclusao(<?php echo $row['id']; ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        <?php } else { ?>
            <p class="sem-checklist">Nenhum checklist cadastrado até o momento.</p>
        <?php } ?>

        <a class="voltar" href="dashboard.php">⬅ Voltar</a>
    </div>

    <footer>
        &copy; <?php echo date('Y'); ?> Sistema de Auditoria. Todos os direitos reservados.
        <br>Desenvolvido por: Arthur Rodrigues, Jean Inácio, João Gabriel e Stefany Carlos.
    </footer>
</body>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../../script/manage-checklists.js"></script>

<script>
<?php if ($mensagem): ?>
Swal.fire({
    icon: '<?php echo strpos($mensagem, "Erro") !== false ? "error" : "success"; ?>',
    title: '<?php echo $mensagem; ?>',
    showConfirmButton: true,
    confirmButtonText: 'OK',
    confirmButtonColor: '#28a745',
    customClass: {
        confirmButton: 'swal2-confirm-green',
    }
});
<?php endif; ?>
</script>

</html>