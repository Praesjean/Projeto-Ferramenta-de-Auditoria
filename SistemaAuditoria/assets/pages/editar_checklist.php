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
if (isset($_SESSION['mensagem_sucesso'])) {
    $mensagem = $_SESSION['mensagem_sucesso'];
    unset($_SESSION['mensagem_sucesso']);
}

if (!isset($_GET['id'])) {
    header("Location: gerenciar_checklists.php");
    exit;
}

$checklist_id = intval($_GET['id']);

$sql = "SELECT * FROM checklists WHERE id = ? AND usuario_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $checklist_id, $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows != 1) {
    die("Checklist não encontrado.");
}

$checklist = $result->fetch_assoc();

$sqlItens = "SELECT * FROM checklist_itens WHERE checklist_id = ?";
$stmtItens = $conn->prepare($sqlItens);
$stmtItens->bind_param("i", $checklist_id);
$stmtItens->execute();
$itens = $stmtItens->get_result();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = $_POST['titulo'];
    $descricao = $_POST['descricao'];
    $autor_documento = $_POST['autor_documento'];
    $auditor = $_POST['auditor'];
    $novos_itens = $_POST['itens'];

    $sqlUpd = "UPDATE checklists SET titulo=?, descricao=?, autor_documento=?, auditor=? WHERE id=? AND usuario_id=?";
    $stmtUpd = $conn->prepare($sqlUpd);
    $stmtUpd->bind_param("ssssii", $titulo, $descricao, $autor_documento, $auditor, $checklist_id, $usuario_id);
    $stmtUpd->execute();

    $sqlDelItens = "DELETE FROM checklist_itens WHERE checklist_id=?";
    $stmtDel = $conn->prepare($sqlDelItens);
    $stmtDel->bind_param("i", $checklist_id);
    $stmtDel->execute();

    $sqlInsItem = "INSERT INTO checklist_itens (checklist_id, descricao) VALUES (?, ?)";
    $stmtIns = $conn->prepare($sqlInsItem);
    foreach ($novos_itens as $item) {
        if (!empty(trim($item))) {
            $stmtIns->bind_param("is", $checklist_id, $item);
            $stmtIns->execute();
        }
    }

    $_SESSION['mensagem_sucesso'] = "Checklist atualizado com sucesso!";
    header("Location: editar_checklist.php?id=" . $checklist_id);
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Auditoria | Editar Checklist</title>
    <link href="../../styles/pages/editar_checklist/editar_checklist.css" rel="stylesheet">
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
        <h2>Editar Checklist</h2>

        <form method="POST" action="">
            <label>Título:</label>
            <input type="text" name="titulo" value="<?php echo htmlspecialchars($checklist['titulo']); ?>">

            <label>Descrição:</label>
            <textarea name="descricao"><?php echo htmlspecialchars($checklist['descricao']); ?></textarea>

            <label>Autor do Documento:</label>
            <input type="text" name="autor_documento" value="<?php echo htmlspecialchars($checklist['autor_documento']); ?>">

            <label>Auditor Responsável:</label>
            <input type="text" name="auditor" value="<?php echo htmlspecialchars($checklist['auditor']); ?>">

            <h3>Itens do Checklist</h3>
            <div id="itens">
                <?php while($item = $itens->fetch_assoc()) { ?>
                    <div style="display: flex; align-items: center; margin-bottom: 5px;">
                        <input type="text" name="itens[]" value="<?php echo htmlspecialchars($item['descricao']); ?>" style="flex: 1; padding: 8px; font-size: 16px;">
                    </div>
                <?php } ?>
            </div>

            <button type="button" class="add-btn" onclick="adicionarItem()">+ Adicionar Item</button>
            <br><br>
            <button type="submit">Salvar Alterações</button>
        </form>

        <a class="voltar" href="gerenciar_checklists.php">⬅ Voltar</a>
    </div>

    <footer>
        &copy; <?php echo date('Y'); ?> Sistema de Auditoria. Todos os direitos reservados.
        <br>Desenvolvido por: Arthur Rodrigues, Jean Inácio, João Gabriel e Stefany Carlos.
    </footer>
</body>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../../script/edit-checklist.js"></script>

<?php if($mensagem): ?>
<script>
Swal.fire({
    icon: 'success',
    title: 'Checklist atualizado!',
    text: '<?php echo $mensagem; ?>',
    confirmButtonColor: '#28a745'
});
</script>
<?php endif; ?>

</html>