<?php
session_start();
include('../../conecta_db.php');

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$mensagem = "";
$resultado_final = null;

$sql = "SELECT * FROM checklists WHERE usuario_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$checklists = $stmt->get_result();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['checklist_id'])) {
    $checklist_id = intval($_POST['checklist_id']);
    $respostas = $_POST['respostas'];

    $sim = 0;
    $nao = 0;

    foreach ($respostas as $item_id => $resposta) {
        if ($resposta == "SIM") $sim++;
        if ($resposta == "NAO") $nao++;
    }

    $resultado_final = ($sim + $nao > 0) ? round(($sim / ($sim + $nao)) * 100, 2) : 0;

    $sqlAuditoria = "INSERT INTO auditorias (checklist_id, usuario_id, resultado) VALUES (?, ?, ?)";
    $stmtA = $conn->prepare($sqlAuditoria);
    $stmtA->bind_param("iid", $checklist_id, $usuario_id, $resultado_final);
    $stmtA->execute();
    $auditoria_id = $stmtA->insert_id;

    $sqlResp = "INSERT INTO auditoria_respostas (auditoria_id, item_id, resposta) VALUES (?, ?, ?)";
    $stmtR = $conn->prepare($sqlResp);

    $sqlNC = "INSERT INTO nao_conformidades (auditoria_id, item_id, descricao) VALUES (?, ?, ?)";
    $stmtNC = $conn->prepare($sqlNC);

    foreach ($respostas as $item_id => $resposta) {
        $stmtR->bind_param("iis", $auditoria_id, $item_id, $resposta);
        $stmtR->execute();

        if ($resposta == "NAO") {
            $descricao = "Não conformidade encontrada no item #" . $item_id;
            $stmtNC->bind_param("iis", $auditoria_id, $item_id, $descricao);
            $stmtNC->execute();
        }
    }

    $mensagem = true;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Auditoria | Realizar Auditoria</title>
    <link href="../../styles/pages/realizar_auditoria/realizar_auditoria.css" rel="stylesheet">
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
        <h2>Realizar Auditoria</h2>

        <form method="GET" action="">
            <label>Selecione um Checklist:</label>
            <select id="checklist_id" name="checklist_id" onchange="carregarItens()">
                <option value="">-- Escolha --</option>
                <?php while ($c = $checklists->fetch_assoc()) { ?>
                    <option value="<?php echo $c['id']; ?>" 
                        <?php if (isset($_GET['checklist_id']) && $_GET['checklist_id'] == $c['id']) echo "selected"; ?>>
                        <?php echo htmlspecialchars($c['titulo']); ?>
                    </option>
                <?php } ?>
            </select>
        </form>

        <?php
        if (isset($_GET['checklist_id'])):
            $checklist_id = intval($_GET['checklist_id']);
            $sqlItens = "SELECT * FROM checklist_itens WHERE checklist_id = ?";
            $stmtItens = $conn->prepare($sqlItens);
            $stmtItens->bind_param("i", $checklist_id);
            $stmtItens->execute();
            $itens = $stmtItens->get_result();

            if ($itens->num_rows > 0):
        ?>
                <form method="POST" action="">
                    <input type="hidden" name="checklist_id" value="<?php echo $checklist_id; ?>">
                    <div class="table-wrapper">
                        <table>
                            <tr><th>Item</th><th>Resposta</th></tr>
                            <?php while ($item = $itens->fetch_assoc()): ?>
                                <tr>
                                    <td class="texto"><?php echo htmlspecialchars($item['descricao']); ?></td>
                                    <td>
                                        <select name="respostas[<?php echo $item['id']; ?>]" required>
                                            <option value="">-- Selecione --</option>
                                            <option value="SIM">Sim</option>
                                            <option value="NAO">Não</option>
                                            <option value="NA">Não aplicável</option>
                                        </select>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </table>
                    </div>
                    <button type="submit">Concluir Auditoria</button>
                </form>
        <?php
            else:
                echo "<p>Nenhum item encontrado neste checklist.</p>";
            endif;
        endif;
        ?>

        <a class="voltar" href="dashboard.php">⬅ Voltar</a>
    </div>

    <footer>
        &copy; <?php echo date('Y'); ?> Sistema de Auditoria. Todos os direitos reservados.
        <br>Desenvolvido por: Arthur Rodrigues, Jean Inácio, João Gabriel e Stefany Carlos.
    </footer>
</body>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../../script/perform-an-audit.js"></script>

<?php if (!is_null($resultado_final)): ?>
<script>
Swal.fire({
    icon: 'success',
    title: 'Auditoria concluída!',
    showConfirmButton: true,
    confirmButtonText: 'OK',
    confirmButtonColor: '#28a745'
}).then(() => {
    Swal.fire({
        icon: 'info',
        title: 'Resultado da Auditoria',
        text: 'Resultado: <?php echo $resultado_final; ?>% de aderência.',
        showConfirmButton: true,
        confirmButtonText: 'OK',
        confirmButtonColor: '#28a745'
    });
});
</script>
<?php endif; ?>

</html>