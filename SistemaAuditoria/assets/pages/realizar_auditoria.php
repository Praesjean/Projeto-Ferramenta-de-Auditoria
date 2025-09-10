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
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding-top: 80px;
            padding-bottom: 80px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            box-sizing: border-box;
        }

        .header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            padding: 15px 30px;
            background: #0077cc;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-sizing: border-box;
        }

        .header .user-info p {
            margin: 2px 0;
            font-weight: normal;
            font-size: 16px;
        }

        .header h1 {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            margin: 0;
            font-size: 24px;
            font-weight: bold;
            text-align: center;
        }

        .header .logout-btn {
            background: #e74c3c;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            transition: background 0.3s, transform 0.2s;
        }

        .header .logout-btn:hover {
            background: #c0392b;
            transform: scale(1.05);
        }

        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 2px 8px rgba(0,0,0,0.2);
            min-width: 600px;
            max-width: 600px;
            margin: 50px auto 40px auto;
            box-sizing: border-box;
        }

        h2 {
            text-align: center;
        }

        .mensagem {
            text-align: center;
            margin: 10px 0;
            color: green;
            font-size: 16px;
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

        label, select, button {
            font-size: 16px;
        }

        select {
            width: 100%;
            max-width: 100%;
            padding: 8px;
            margin: 8px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box;
            cursor: pointer;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        button {
            background: #28a745;
            color: white;
            border: none;
            transition: background 0.3s, transform 0.2s;
        }

        button:hover {
            background: #218838;
            transform: scale(1.03);
        }

        .table-wrapper {
            overflow-x: auto;
            margin-top: 15px;
        }

        table {
            width: 100%;
            max-width: 100%;
            border-collapse: collapse;
            font-size: 16px;
            border-radius: 10px;
            overflow: hidden;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
            font-size: 16px;
        }

        th {
            background: #0077cc;
            color: white;
        }

        td.texto {
            word-break: break-word;
            overflow-wrap: break-word;
            max-width: 300px;
        }

        td.acoes {
            white-space: nowrap;
        }

        button[type="submit"] {
            display: block;
            margin: 20px auto 0 auto;
            width: 200px;
            padding: 10px 15px;
            border-radius: 8px;
            cursor: pointer;
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
    <script>
        function carregarItens() {
            let checklist_id = document.getElementById("checklist_id").value;
            if (checklist_id) {
                window.location.href = "realizar_auditoria.php?checklist_id=" + checklist_id;
            }
        }
    </script>
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
                                            <option value="SIM">SIM</option>
                                            <option value="NAO">NÃO</option>
                                            <option value="NA">N/A</option>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
</body>
</html>
