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

    $mensagem = "✅ Auditoria concluída! Resultado: $resultado_final% de aderência.";
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Realizar Auditoria</title>
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
            max-width: 800px;
            margin: auto;
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

        select, button {
            width: 100%;
            max-width: 300px;
            padding: 8px;
            margin: 8px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box;
            cursor: pointer;
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            border-radius: 10px;
            overflow: hidden;
            font-size: 16px;
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

        button[type="submit"] {
            display: block;
            margin: 20px auto 0 auto;
            width: 200px;
            padding: 10px 15px;
            border-radius: 8px;
            cursor: pointer;
        }

        button[type="submit"]:hover {
            background: #218838;
            transform: scale(1.03);
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
    <div class="container">
        <h2>Realizar Auditoria</h2>

        <?php if ($mensagem) echo "<p class='mensagem'>$mensagem</p>"; ?>

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
        if (isset($_GET['checklist_id'])) {
            $checklist_id = intval($_GET['checklist_id']);
            $sqlItens = "SELECT * FROM checklist_itens WHERE checklist_id = ?";
            $stmtItens = $conn->prepare($sqlItens);
            $stmtItens->bind_param("i", $checklist_id);
            $stmtItens->execute();
            $itens = $stmtItens->get_result();

            if ($itens->num_rows > 0) {
                echo '<form method="POST" action="">';
                echo '<input type="hidden" name="checklist_id" value="'.$checklist_id.'">';
                echo '<table>';
                echo '<tr><th>Item</th><th>Resposta</th></tr>';
                while ($item = $itens->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td>'.htmlspecialchars($item['descricao']).'</td>';
                    echo '<td>
                            <select name="respostas['.$item['id'].']" required>
                                <option value="">-- Selecione --</option>
                                <option value="SIM">SIM</option>
                                <option value="NAO">NÃO</option>
                                <option value="NA">N/A</option>
                            </select>
                          </td>';
                    echo '</tr>';
                }
                echo '</table>';
                echo '<button type="submit">Concluir Auditoria</button>';
                echo '</form>';
            } else {
                echo "<p>Nenhum item encontrado neste checklist.</p>";
            }
        }
        ?>

        <a class="voltar" href="dashboard.php">⬅ Voltar</a>
    </div>
</body>
</html>