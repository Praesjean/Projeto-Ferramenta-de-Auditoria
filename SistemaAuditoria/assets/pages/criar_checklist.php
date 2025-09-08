<?php
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
    $auditor = trim($_POST['auditor']);
    $itens = $_POST['itens'];
    $usuario_id = $_SESSION['usuario_id'];

    if (!empty($titulo) && !empty($auditor) && !empty($itens)) {
        $sql = "INSERT INTO checklists (titulo, descricao, auditor, usuario_id) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $titulo, $descricao, $auditor, $usuario_id);

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

            $mensagem = "✅ Checklist criado com sucesso!";
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
    <title>Criar Checklist</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 2px 8px rgba(0,0,0,0.2);
            max-width: 600px;
            margin: auto;
        }
        h2 {
            text-align: center;
        }
        input[type=text], textarea {
            width: 100%;
            padding: 10px;
            margin: 6px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .itens input {
            margin-bottom: 5px;
        }
        button {
            padding: 10px 15px;
            background: #0077cc;
            border: none;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background: #005fa3;
        }
        .mensagem {
            margin-top: 10px;
            color: green;
            text-align: center;
        }
        .voltar {
            display: block;
            margin-top: 15px;
            text-align: center;
        }
        .add-btn {
            margin-top: 8px;
            background: #28a745;
        }
        .add-btn:hover {
            background: #1e7e34;
        }
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
        <h2>Criar Checklist</h2>
        <form method="POST" action="">
            <label>Título:</label>
            <input type="text" name="titulo" required>

            <label>Descrição:</label>
            <textarea name="descricao" rows="3"></textarea>

            <label>Auditor Responsável:</label>
            <input type="text" name="auditor" required>

            <h3>Itens do Checklist</h3>
            <div id="itens">
                <input type="text" name="itens[]" placeholder="Descrição do item" required>
            </div>
            <button type="button" class="add-btn" onclick="adicionarItem()">+ Adicionar Item</button>

            <br><br>
            <button type="submit">Salvar Checklist</button>
        </form>

        <div class="mensagem"><?php echo $mensagem; ?></div>

        <a class="voltar" href="dashboard.php">⬅ Voltar ao Dashboard</a>
    </div>
</body>
</html>