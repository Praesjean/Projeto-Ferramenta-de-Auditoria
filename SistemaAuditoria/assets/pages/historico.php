<?php
ini_set('session.gc_maxlifetime', 604800); 
session_set_cookie_params(604800);

session_start();
include('../../conecta_db.php');

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$sucesso = isset($_GET['sucesso']) ? intval($_GET['sucesso']) : 0;
$usuario_id = $_SESSION['usuario_id'];

$sql = "SELECT a.id, a.realizado_em, a.resultado, c.titulo 
        FROM auditorias a
        JOIN checklists c ON a.checklist_id = c.id
        WHERE a.usuario_id = ?
        ORDER BY a.realizado_em ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$auditorias = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Auditoria | Histórico de Auditorias</title>
    <link href="../../styles/pages/historico/historico.css" rel="stylesheet">
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
        <h2>Histórico de Auditorias</h2>

        <?php if ($auditorias->num_rows > 0) { ?>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Checklist</th>
                    <th>Data</th>
                    <th>Resultado (%)</th>
                    <th>Detalhes</th>
                </tr>
                <?php 
                $contador = 1;
                while ($a = $auditorias->fetch_assoc()) { 
                ?>
                <tr>
                    <td><?php echo $contador; ?></td>
                    <td><?php echo htmlspecialchars($a['titulo']); ?></td>
                    <td><?php echo date("d/m/Y H:i:s", strtotime($a['realizado_em'])); ?></td>
                    <td><?php echo $a['resultado']; ?>%</td>
                    <td>
                        <a class="detalhes-btn" href="ver_auditoria.php?id=<?php echo $a['id']; ?>" title="Ver detalhes">
                            <i class="fas fa-eye"></i>
                        </a>
                        <button class="excluir-btn" data-id="<?php echo $a['id']; ?>" title="Excluir auditoria">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>
                <?php 
                    $contador++;
                } ?>
            </table>
        <?php } else { ?>
            <p class="sem-auditoria">Nenhuma auditoria realizada até o momento.</p>
        <?php } ?>

        <a class="voltar" href="dashboard.php">⬅ Voltar</a>
    </div>

    <footer>
        &copy; <?php echo date('Y'); ?> Sistema de Auditoria. Todos os direitos reservados.
        <br>Desenvolvido por: Arthur Rodrigues, Jean Inácio, João Gabriel e Stefany Carlos.
    </footer>
</body>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../../script/history.js"></script>

<script>
<?php if($sucesso === 1): ?>
Swal.fire({
    icon: 'success',
    title: 'Auditoria excluída!',
    text: 'A auditoria foi removida com sucesso.',
    confirmButtonColor: '#28a745'
});
<?php endif; ?>
</script>

</html>