<?php
    session_start();
    include('../../conecta_db.php');

    if (!isset($_SESSION['usuario_id'])) {
        header("Location: login.php");
        exit;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $titulo = trim($_POST['titulo']);
        $descricao = trim($_POST['descricao']);
        $auditor = trim($_POST['auditor']);
        $itens = $_POST['itens'];
        $usuario_id = $_SESSION['usuario_id'];

        if (empty($titulo) || empty($auditor) || empty($itens) || count(array_filter($itens, fn($i) => trim($i) !== "")) === 0) {
            $_SESSION['error_message'] = "Preencha todos os campos obrigatórios e adicione pelo menos um item.";
            header("Location: criar_checklist.php");
            exit;
        }

        $sql = "INSERT INTO checklists (titulo, descricao, auditor, usuario_id) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $titulo, $descricao, $auditor, $usuario_id);

        if ($stmt->execute()) {
            $checklist_id = $stmt->insert_id;

            $sqlItem = "INSERT INTO checklist_itens (checklist_id, descricao) VALUES (?, ?)";
            $stmtItem = $conn->prepare($sqlItem);

            foreach ($itens as $item) {
                $item = trim($item);
                if ($item !== "") {
                    $stmtItem->bind_param("is", $checklist_id, $item);
                    $stmtItem->execute();
                }
            }

            $_SESSION['success_message'] = "Checklist criado com sucesso!";
        } else {
            $_SESSION['error_message'] = "Erro ao criar checklist: " . $stmt->error;
            header("Location: criar_checklist.php");
            exit;
        }

        $stmt->close();
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <title>Criar Checklist</title>
        <link rel="stylesheet" href="../../assets/style/checklist.css">
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
            h2 { text-align: center; }
            input[type=text], textarea {
                width: 100%;
                padding: 10px;
                margin: 6px 0;
                border: 1px solid #ccc;
                border-radius: 5px;
                box-sizing: border-box;
                font-size: 16px;
            }
            #itens .checklist-item {
                display: flex;
                align-items: center;
                margin-bottom: 5px;
            }
            #itens .checklist-item input {
                flex: 1;
                padding: 10px;
                font-size: 16px;
                border: 1px solid #ccc;
                border-radius: 5px;
                box-sizing: border-box;
            }
            #itens .checklist-item .remove-btn {
                margin-left: 5px;
                padding: 6px 10px;
                background: #e63636;
                color: white;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                font-size: 14px;
            }
            #itens .checklist-item .remove-btn:hover {
                background: #a42b2b;
            }
            button {
                padding: 10px 15px;
                background: #0077cc;
                border: none;
                color: white;
                border-radius: 5px;
                cursor: pointer;
                font-size: 16px;
            }
            button:hover { background: #005fa3; }
            .voltar {
                display: block;
                width: fit-content;
                margin: 20px auto 0 auto;
                text-align: center;
                text-decoration: none;
                color: black;
                background: #bababaff;
                padding: 10px 20px;
                border-radius: 8px;
                transition: background 0.3s, transform 0.2s;
                font-size: 16px;
            }
            .voltar:hover {
                background: #979797ff;
                transform: scale(1.05);
            }
            .add-btn {
                margin-top: 8px;
                background: #28a745;
                font-size: 16px;
            }
            .add-btn:hover { background: #1e7e34; }
        </style>
    </head>
    <body>
        <div class="container">
            <h2>Criar Checklist</h2>
            <form method="POST" action="">
                <label>Título:</label>
                <input type="text" name="titulo">

                <label>Descrição:</label>
                <textarea name="descricao" rows="3"></textarea>

                <label>Auditor Responsável:</label>
                <input type="text" name="auditor">

                <h3>Itens do Checklist</h3>
                <div id="itens">
                    <div class="checklist-item">
                        <input type="text" name="itens[]" placeholder="Descrição do item">
                        <button type="button" class="remove-btn">❌</button>
                    </div>
                </div>
                <button type="button" class="add-btn" onclick="adicionarItem()">+ Adicionar Item</button>
                <br><br>
                <button type="submit">Salvar Checklist</button>
            </form>
            <a class="voltar" href="dashboard.php">⬅ Voltar</a>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="../../script/checklist-validation.js"></script>

        <?php if(isset($_SESSION['success_message'])): ?>
        <script>
            showSuccess("<?= $_SESSION['success_message'] ?>", "criar_checklist.php");
            <?php unset($_SESSION['success_message']); ?>
        </script>
        <?php endif; ?>

        <?php if(isset($_SESSION['error_message'])): ?>
        <script>
            showError("<?= $_SESSION['error_message'] ?>");
            <?php unset($_SESSION['error_message']); ?>
        </script>
        <?php endif; ?>
    </body>
</html>