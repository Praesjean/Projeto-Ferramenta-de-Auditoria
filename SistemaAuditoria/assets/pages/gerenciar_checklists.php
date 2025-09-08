<?php
session_start();
include 'conecta_db.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$mensagem = "";

if (isset($_GET['excluir'])) {
    $id = intval($_GET['excluir']);
    $sql = "DELETE FROM checklists WHERE id = ? AND usuario_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id, $usuario_id);
    if ($stmt->execute()) {
        $mensagem = "✅ Checklist excluído com sucesso!";
    } else {
        $mensagem = "Erro ao excluir checklist.";
    }
}

$sql = "SELECT * FROM checklists WHERE usuario_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$resultado = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Checklists</title>
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
        }
        h2 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        th {
            background: #0077cc;
            color: white;
        }
        a {
            text-decoration: none;
            padding: 6px 10px;
            border-radius: 5px;
            font-size: 14px;
        }
        .editar {
            background: #ffc107;
            color: black;
        }
        .excluir {
            background: #dc3545;
            color: white;
        }
        .voltar {
            display: block;
            text-align: center;
            margin-top: 20px;
        }
        .mensagem {
            text-align: center;
            margin: 10px 0;
            color: green;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Gerenciar Checklists</h2>

        <?php if ($mensagem) echo "<p class='mensagem'>$mensagem</p>"; ?>

        <table>
            <tr>
                <th>Título</th>
                <th>Auditor</th>
                <th>Criado em</th>
                <th>Ações</th>
            </tr>
            <?php while ($row = $resultado->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['titulo']); ?></td>
                    <td><?php echo htmlspecialchars($row['auditor']); ?></td>
                    <td><?php echo $row['criado_em']; ?></td>
                    <td>
                        <a class="editar" href="editar_checklist.php?id=<?php echo $row['id']; ?>">Editar</a>
                        <a class="excluir" href="gerenciar_checklists.php?excluir=<?php echo $row['id']; ?>" onclick="return confirm('Tem certeza que deseja excluir este checklist?')">Excluir</a>
                    </td>
                </tr>
            <?php } ?>
        </table>

        <a class="voltar" href="dashboard.php">⬅ Voltar ao Dashboard</a>
    </div>
</body>
</html>