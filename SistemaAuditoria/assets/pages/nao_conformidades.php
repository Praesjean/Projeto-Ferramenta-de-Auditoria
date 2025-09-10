<?php
session_start();
include('../../conecta_db.php');

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$mensagem = "";

if (isset($_POST['atualizar'])) {
    $nc_id = intval($_POST['nc_id']);
    $novo_status = $_POST['status'];

    $sql = "UPDATE nao_conformidades nc
            JOIN auditorias a ON nc.auditoria_id = a.id
            SET nc.status = ?
            WHERE nc.id = ? AND a.usuario_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $novo_status, $nc_id, $usuario_id);
    if ($stmt->execute()) {
        $mensagem = "✅ Status atualizado!";
    } else {
        $mensagem = "Erro ao atualizar status.";
    }
}

$sql = "SELECT nc.id AS nc_id, nc.descricao, nc.status, nc.criado_em, c.titulo AS checklist, a.id AS auditoria_id
        FROM nao_conformidades nc
        JOIN auditorias a ON nc.auditoria_id = a.id
        JOIN checklists c ON a.checklist_id = c.id
        WHERE a.usuario_id = ?
        ORDER BY nc.criado_em DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$nc_list = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Auditoria | Não Conformidades</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            max-width: 1000px;
            margin: auto;
        }
        h2 {
            text-align: center;
        }
        .mensagem {
            text-align: center;
            color: green;
            margin-bottom: 15px;
            font-size: 16px;
        }
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 15px;
            margin-bottom: 40px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
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
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        select {
            padding: 5px;
            font-size: 16px;
            border-radius: 5px;
        }
        button {
            padding: 6px 12px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s, transform 0.2s;
        }
        button:hover {
            background: #218838;
            transform: scale(1.03);
        }
        .enviar-btn {
            background: #0077cc;
        }
        .enviar-btn:hover {
            background: #005fa3;
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
        .sem-nao-conformidades {
            text-align: center;
            margin: 20px 0;
            font-size: 16px;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Não Conformidades</h2>

        <?php if ($mensagem) echo "<p class='mensagem'>$mensagem</p>"; ?>

        <?php if ($nc_list->num_rows > 0) { ?>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Checklist</th>
                    <th>Auditoria</th>
                    <th>Status</th>
                    <th>Atualizar</th>
                    <th>Ações</th>
                </tr>
                <?php while ($nc = $nc_list->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $nc['nc_id']; ?></td>
                        <td><?php echo htmlspecialchars($nc['checklist']); ?></td>
                        <td><?php echo $nc['auditoria_id']; ?></td>
                        <td><?php echo $nc['status']; ?></td>
                        <td>
                            <form method="POST" action="">
                                <input type="hidden" name="nc_id" value="<?php echo $nc['nc_id']; ?>">
                                <select name="status">
                                    <option value="ABERTA" <?php if($nc['status']=="ABERTA") echo "selected"; ?>>ABERTA</option>
                                    <option value="EM ANDAMENTO" <?php if($nc['status']=="EM ANDAMENTO") echo "selected"; ?>>EM ANDAMENTO</option>
                                    <option value="RESOLVIDA" <?php if($nc['status']=="RESOLVIDA") echo "selected"; ?>>RESOLVIDA</option>
                                </select>
                                <button type="submit" name="atualizar">Salvar</button>
                            </form>
                        </td>
                        <td>
                            <form method="POST" action="enviar_nc.php">
                                <input type="hidden" name="nc_id" value="<?php echo $nc['nc_id']; ?>">
                                <button type="submit" class="enviar-btn">Enviar por e-mail</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        <?php } else { ?>
            <p class="sem-nao-conformidades">Nenhuma não conformidade encontrada.</p>
        <?php } ?>

        <div style="text-align:center;">
            <a class="voltar" href="dashboard.php">⬅ Voltar</a>
        </div>
    </div>
</body>
</html>