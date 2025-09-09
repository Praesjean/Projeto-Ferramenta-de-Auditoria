<?php
session_start();
include('../../conecta_db.php');

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

$sql = "SELECT a.id, a.realizado_em, a.resultado, c.titulo 
        FROM auditorias a
        JOIN checklists c ON a.checklist_id = c.id
        WHERE a.usuario_id = ?
        ORDER BY a.realizado_em DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$auditorias = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Histórico de Auditorias</title>
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
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            max-width: 900px;
            margin: auto;
        }
        h2 {
            text-align: center;
        }
        table {
            width: 100%;
            margin-top: 15px;
            border-collapse: collapse;
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
        tr:nth-child(even) {
            background: #f9f9f9;
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
            font-size: 16px;
            transition: background 0.3s, transform 0.2s;
        }
        .voltar:hover {
            background: #979797ff;
            transform: scale(1.05);
        }
        .detalhes-btn {
            display: inline-block;
            padding: 6px 12px;
            background: #28a745;
            color: white;
            border-radius: 6px;
            text-decoration: none;
            font-size: 16px;
            transition: background 0.3s, transform 0.2s;
        }
        .detalhes-btn:hover {
            background: #218838;
            transform: scale(1.05);
        }

        .sem-auditoria {
            text-align: center;
            margin: 20px 0;
            font-size: 16px;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Histórico de Auditorias</h2>

        <?php if ($auditorias->num_rows > 0) { ?>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Checklist</th>
                    <th>Data</th>
                    <th>Resultado (%)</th>
                    <th>Detalhes</th>
                </tr>
                <?php 
                $contador = 1;
                while ($a = $auditorias->fetch_assoc()) { 
                ?>
                <tr>
                    <td><?php echo $contador; ?></td>
                    <td><?php echo htmlspecialchars($a['titulo']); ?></td>
                    <td><?php echo date("d/m/Y H:i", strtotime($a['realizado_em'])); ?></td>
                    <td><?php echo $a['resultado']; ?>%</td>
                    <td><a class="detalhes-btn" href="ver_auditoria.php?id=<?php echo $a['id']; ?>">Verificar</a></td>
                </tr>
                <?php 
                    $contador++;
                } ?>
            </table>
        <?php } else { ?>
            <p class="sem-auditoria">Nenhuma auditoria realizada até o momento.</p>
        <?php } ?>

        <a class="voltar" href="dashboard.php">⬅ Voltar</a>
    </div>
</body>
</html>