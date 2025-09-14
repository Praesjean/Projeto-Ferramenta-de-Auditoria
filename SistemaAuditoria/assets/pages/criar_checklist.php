<?php
ini_set('session.gc_maxlifetime', 604800); 
session_set_cookie_params(604800);

session_start();
include('../../conecta_db.php');

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = trim($_POST['titulo']);
    $descricao = trim($_POST['descricao']);
    $autor_documento = trim($_POST['autor_documento']);
    $auditor = trim($_POST['auditor']);
    $itens = $_POST['itens'];
    $usuario_id = $_SESSION['usuario_id'];

    if (!empty($titulo) && !empty($descricao) && !empty($autor_documento) && !empty($auditor) && !empty($itens)) {
        $sql = "INSERT INTO checklists (titulo, descricao, autor_documento, auditor, usuario_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $titulo, $descricao, $autor_documento, $auditor, $usuario_id);

        if ($stmt->execute()) {
            $checklist_id = $stmt->insert_id;

            $sqlItem = "INSERT INTO checklist_itens (checklist_id, descricao) VALUES (?, ?)";
            $stmtItem = $conn->prepare($sqlItem);

            foreach ($itens as $item) {
                if (!empty(trim($item))) {
                    $stmtItem->bind_param("is", $checklist_id, $item);
                    $stmtItem->execute();
                }
            }

            $mensagem = "Checklist criado!";
        } else {
            $mensagem = "Erro: " . $stmt->error;
        }
    } else {
        $mensagem = "Preencha os campos obrigatórios.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Auditoria | Criar Checklist</title>
    <link href="../../styles/pages/criar_checklist/criar_checklist.css" rel="stylesheet">
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
        <h2>Criar Checklist</h2>
        <form method="POST" action="">
            <label>Título:</label>
            <input type="text" name="titulo">

            <label>Descrição:</label>
            <textarea name="descricao" rows="3"></textarea>

            <label>Autor do Documento:</label>
            <input type="text" name="autor_documento">

            <label>Auditor Responsável:</label>
            <input type="text" name="auditor">

            <h3>Itens do Checklist</h3>
            <div id="itens"></div>

            <div class="btn-container">
                <button type="button" class="add-btn" onclick="adicionarItem()">+ Adicionar Item</button>
                <button type="button" class="add-template-btn" onclick="usarTemplate()">Usar Checklist Pré-definido</button>
                <br><br>
                <button type="submit">Salvar Checklist</button>
            </div>
        </form>

        <a class="voltar" href="dashboard.php">⬅ Voltar</a>
    </div>

    <footer>
        &copy; <?php echo date('Y'); ?> Sistema de Auditoria. Todos os direitos reservados.
        <br>Desenvolvido por: Arthur Rodrigues, Jean Inácio, João Gabriel e Stefany Carlos.
    </footer>
</body>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../../script/create-checklist.js"></script>
<script src="../../script/checklist-validation.js"></script>

<script>
<?php if ($mensagem): ?>
Swal.fire({
    icon: '<?php echo strpos($mensagem, "Erro") !== false ? "error" : "success"; ?>',
    title: '<?php echo $mensagem; ?>',
    showConfirmButton: true,
    confirmButtonText: 'OK',
    confirmButtonColor: '<?php echo strpos($mensagem, "Erro") !== false ? "#dc3545" : "#28a745"; ?>',
    customClass: {
        confirmButton: 'swal2-confirm-custom'
    }
});
<?php endif; ?>
</script>

</html>