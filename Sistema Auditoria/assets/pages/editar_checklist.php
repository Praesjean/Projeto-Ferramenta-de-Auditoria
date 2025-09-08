<?php
session_start();
include 'conecta_db.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$mensagem = "";

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
    $auditor = $_POST['auditor'];
    $novos_itens = $_POST['itens'];

    $sqlUpd = "UPDATE checklists SET titulo=?, descricao=?, auditor=? WHERE id=? AND usuario_id=?";
    $stmtUpd = $conn->prepare($sqlUpd);
    $stmtUpd->bind_param("sssii", $titulo, $descricao, $auditor, $checklist_id, $usuario_id);
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

    $mensagem = "✅ Checklist atualizado com sucesso!";
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Checklist</title>
    <style>
        body { font-family: Arial; background: #f4f6f8; padding: 20px; }
        .container { background: white; padding: 20px; border-radius: 8px; max-width: 600px; margin:auto; box-shadow:0 2px 8px rgba(0,0,0,0.2); }
        input, textarea { width:100%; padding:10px; margin:5px 0; border:1px solid #ccc; border-radius:5px; }
        button { padding:10px 15px; background:#0077cc; color:white; border:none; border-radius:5px; cursor:pointer; }
        button:hover { background:#005fa3; }
        .mensagem { color:green; text-align:center; margin-bottom:10px; }
        h2 { text-align:center; }
        .add-btn { background:#28a745; margin-top:5px; }
        .add-btn:hover { background:#218838; }
        .voltar { display:block; margin-top:15px; text-align:center; }
    </style>
    <script>
        function adicionarItem() {
            let div = document.createElement("div");
            div.innerHTML = '<input type="text" name="itens[]" placeholder="Descrição do item" required>';
            document.getElementById("itens").appendChild(div);
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Editar Checklist</h2>

        <?php if($mensagem) echo "<p class='mensagem'>$mensagem</p>"; ?>

        <form method="POST" action="">
            <label>Título:</label>
            <input type="text" name="titulo" value="<?php echo htmlspecialchars($checklist['titulo']); ?>" required>

            <label>Descrição:</label>
            <textarea name="descricao"><?php echo htmlspecialchars($checklist['descricao']); ?></textarea>

            <label>Auditor:</label>
            <input type="text" name="auditor" value="<?php echo htmlspecialchars($checklist['auditor']); ?>" required>

            <h3>Itens</h3>
            <div id="itens">
                <?php while($item = $itens->fetch_assoc()) { ?>
                    <input type="text" name="itens[]" value="<?php echo htmlspecialchars($item['descricao']); ?>" required>
                <?php } ?>
            </div>

            <button type="button" class="add-btn" onclick="adicionarItem()">+ Adicionar Item</button>
            <br><br>
            <button type="submit">Salvar Alterações</button>
        </form>

        <a class="voltar" href="gerenciar_checklists.php">⬅ Voltar</a>
    </div>
</body>
</html>