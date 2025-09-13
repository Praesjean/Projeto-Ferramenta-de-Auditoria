<?php
session_start();
include('../../conecta_db.php');

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

if (!isset($_GET['id'])) {
    header("Location: historico.php");
    exit;
}

$auditoria_id = intval($_GET['id']);

$sql = "SELECT a.id, a.resultado, a.realizado_em, c.titulo, c.descricao AS checklist_desc, c.auditor
        FROM auditorias a
        JOIN checklists c ON a.checklist_id = c.id
        WHERE a.id = ? AND a.usuario_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $auditoria_id, $usuario_id);
$stmt->execute();
$auditoria = $stmt->get_result()->fetch_assoc();

if (!$auditoria) {
    die("Auditoria não encontrada.");
}

$sqlRespostas = "SELECT ai.descricao AS item, ar.resposta, nc.descricao AS nc_desc, nc.status AS nc_status
                 FROM auditoria_respostas ar
                 JOIN checklist_itens ai ON ar.item_id = ai.id
                 LEFT JOIN nao_conformidades nc ON nc.auditoria_id = ar.auditoria_id AND nc.item_id = ai.id
                 WHERE ar.auditoria_id = ?";
$stmtRes = $conn->prepare($sqlRespostas);
$stmtRes->bind_param("i", $auditoria_id);
$stmtRes->execute();
$respostas = $stmtRes->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Auditoria | Detalhes da Auditoria</title>
    <link href="../../styles/pages/ver_auditoria/ver_auditoria.css" rel="stylesheet">
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
        <h2>Auditoria: <?php echo htmlspecialchars($auditoria['titulo']); ?></h2>
        <p><strong>Descrição do Checklist:</strong> <?php echo htmlspecialchars($auditoria['checklist_desc']); ?></p>
        <p><strong>Auditor:</strong> <?php echo htmlspecialchars($auditoria['auditor']); ?></p>
        <p><strong>Data:</strong> <?php echo date("d/m/Y H:i", strtotime($auditoria['realizado_em'])); ?></p>
        <p><strong>Resultado:</strong> <?php echo $auditoria['resultado']; ?>%</p>

        <h3>Respostas por Item</h3>
        <table>
            <tr>
                <th>Item</th>
                <th>Resposta</th>
                <th>Não Conformidade</th>
                <th>Status NC</th>
            </tr>
            <?php 
            $temNC = false;
            while($r = $respostas->fetch_assoc()) { 
                if (!empty($r['nc_desc'])) {
                    $temNC = true;
                }
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($r['item']); ?></td>
                    <td><?php echo $r['resposta']; ?></td>
                    <td><?php echo htmlspecialchars($r['nc_desc']); ?></td>
                    <td><?php echo $r['nc_status']; ?></td>
                </tr>
            <?php } ?>
        </table>

        <?php if (!$temNC) { ?>
            <p style="text-align:center; margin-top:15px; font-size:16px; color:#555;">Nenhuma não conformidade encontrada.</p>
        <?php } ?>

        <a class="voltar" href="historico.php">⬅ Voltar</a>
    </div>

    <footer>
        &copy; <?php echo date('Y'); ?> Sistema de Auditoria. Todos os direitos reservados.
        <br>Desenvolvido por: Arthur Rodrigues, Jean Inácio, João Gabriel e Stefany Carlos.
    </footer>
</body>
</html>