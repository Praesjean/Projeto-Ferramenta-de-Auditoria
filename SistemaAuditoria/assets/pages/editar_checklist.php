<?php
session_start();
include('../../conecta_db.php');

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

    $mensagem = "Checklist atualizado com sucesso!";
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Checklist</title>
    <style>
        body { font-family: Arial; background: #f4f6f8; margin:0; padding-top:80px; padding-bottom:80px; min-height:100vh; box-sizing:border-box; }

        .header {
            position: fixed;
            top:0;
            left:0;
            width:100%;
            z-index:1000;
            padding:15px 30px;
            background:#0077cc;
            color:white;
            display:flex;
            justify-content:space-between;
            align-items:center;
            box-sizing:border-box;
        }

        .header .user-info p {
            margin:2px 0;
            font-weight: normal;
            font-size:16px;
        }

        .header h1 {
            position:absolute;
            left:50%;
            transform:translateX(-50%);
            margin:0;
            font-size:24px;
            font-weight:bold;
            text-align:center;
        }

        .header .logout-btn {
            background: #e74c3c;
            color:white;
            padding:8px 16px;
            text-decoration:none;
            border-radius:6px;
            font-weight:bold;
            transition: background 0.3s, transform 0.2s;
        }

        .header .logout-btn:hover {
            background:#c0392b;
            transform: scale(1.05);
        }

        .container { 
            background: white; 
            padding: 20px; 
            border-radius: 8px; 
            max-width: 600px; 
            margin: 30px auto 40px auto;
            box-shadow:0 2px 8px rgba(0,0,0,0.2); 
            box-sizing: border-box;
        }

        input, textarea { 
            width: 100%; 
            padding: 10px; 
            margin:5px 0; 
            border:1px solid #ccc; 
            border-radius:5px; 
            box-sizing: border-box;
            font-size: 16px;
        }

        button { 
            padding:10px 15px; 
            background:#0077cc; 
            color:white; 
            border:none; 
            border-radius:5px; 
            cursor:pointer; 
            transition: 0.2s;
            font-size: 16px;
        }

        button:hover { 
            background:#005fa3; 
        }

        .add-btn { 
            background:#28a745; 
            margin-top:5px; 
            font-size: 16px;
        }

        .add-btn:hover { 
            background:#218838; 
        }

        .mensagem { 
            color:green; 
            text-align:center; 
            margin-bottom:10px;
            font-size: 16px;
        }

        h2 { 
            text-align:center; 
        }

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

        footer {
            background: #0077cc;
            color: white;
            text-align: center;
            padding: 20px 0;
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            box-sizing: border-box;
            font-size: 16px;
            line-height: 1.5em;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="user-info">
            <p>Nome: <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?></p>
            <p>E-mail: <?php echo htmlspecialchars($_SESSION['usuario_email']); ?></p>
        </div>
        <h1>Sistema de Auditoria</h1>
        <div><a href="logout.php" class="logout-btn">Sair</a></div>
    </header>

    <div class="container">
        <h2>Editar Checklist</h2>

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

    <footer>
        &copy; <?php echo date('Y'); ?> Sistema de Auditoria. Todos os direitos reservados.
        <br>Desenvolvido por: Arthur Rodrigues, Jean Inácio, João Gabriel e Stefany Carlos.
    </footer>

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
</body>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function adicionarItem() {
    let div = document.createElement("div");
    div.innerHTML = '<input type="text" name="itens[]" placeholder="Descrição do item" required>';
    document.getElementById("itens").appendChild(div);
}

document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'Tem certeza?',
            text: "Deseja salvar as alterações deste checklist?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sim, salvar!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                let formData = new FormData(form);

                fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Checklist atualizado!',
                        text: 'Suas alterações foram salvas com sucesso!',
                        confirmButtonColor: '#28a745'
                    });
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro!',
                        text: 'Não foi possível salvar as alterações.',
                        confirmButtonColor: '#d33'
                    });
                });
            }
        });
    });
});
</script>

</html>