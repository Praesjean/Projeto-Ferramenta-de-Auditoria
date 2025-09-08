<?php
session_start();
include('../../conecta_db.php');


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
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 15px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0px 2px 8px rgba(0,0,0,0.1);
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

        th:first-child {
            border-top-left-radius: 12px;
        }

        th:last-child {
            border-top-right-radius: 12px;
        }

        tr:last-child td:first-child {
            border-bottom-left-radius: 12px;
        }

        tr:last-child td:last-child {
            border-bottom-right-radius: 12px;
        }
        a {
            text-decoration: none;
            padding: 6px 10px;
            border-radius: 5px;
            font-size: 16px;
        }
        .editar {
            background: #ffc107;
            color: black;
        }
        .excluir {
            background: #dc3545;
            color: white;
        }
        a.editar:hover {
            background: #e0a800;
            transform: scale(1.05);
            transition: 0.2s;
            font-size: 16px;
        }

        a.excluir:hover {
            background: #c82333;
            transform: scale(1.05);
            transition: 0.2s;
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
        .mensagem {
            text-align: center;
            margin: 10px 0;
            color: green;
            font-size: 16px;
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

        <a class="voltar" href="dashboard.php">⬅ Voltar</a>
    </div>
</body>
</html>