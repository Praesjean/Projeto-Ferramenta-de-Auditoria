<?php
ini_set('session.gc_maxlifetime', 604800); 
session_set_cookie_params(604800);

session_start();
include('../../conecta_db.php');

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$usuario_nome = $_SESSION['usuario_nome'];

if (!isset($_GET['id'])) {
    header("Location: historico.php");
    exit;
}

$auditoria_id = intval($_GET['id']);

$sqlAuditoria = "SELECT id, checklist_id, resultado, realizado_em, 
                        titulo_checklist AS titulo, descricao_checklist AS checklist_desc,
                        autor_documento, auditor_responsavel
                 FROM auditorias
                 WHERE id = ? AND usuario_id = ?";
$stmtAud = $conn->prepare($sqlAuditoria);
$stmtAud->bind_param("ii", $auditoria_id, $usuario_id);
$stmtAud->execute();
$auditoria = $stmtAud->get_result()->fetch_assoc();

if (!$auditoria) {
    die("Auditoria não encontrada.");
}

$contador = 1;

$sqlRespostas = "SELECT ar.item_id, ar.descricao_item, ar.resposta, 
                        nc.status AS nc_status
                 FROM auditoria_respostas ar
                 LEFT JOIN nao_conformidades nc 
                    ON nc.auditoria_id = ar.auditoria_id AND nc.item_id = ar.item_id
                 WHERE ar.auditoria_id = ?
                 ORDER BY ar.item_id";
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <header class="header">
        <div class="user-info">
            <p>Nome: <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?></p>
            <p>E-mail: <?php echo htmlspecialchars($_SESSION['usuario_email']); ?></p>
        </div>

        <h1>Sistema de Auditoria</h1>

        <div>
            <a href="logout.php" class="logout-btn" title="Sair">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </header>

    <div class="container">
        <h2>Auditoria: <?php echo htmlspecialchars($auditoria['titulo']); ?></h2>
        <p><strong>Descrição do checklist:</strong> <?php echo htmlspecialchars($auditoria['checklist_desc']); ?></p>
        <p><strong>Autor do checklist:</strong> <?php echo htmlspecialchars($usuario_nome); ?></p>
        <p><strong>Autor do artefato avaliado:</strong> <?php echo htmlspecialchars($auditoria['autor_documento']); ?></p>
        <p><strong>Auditor:</strong> <?php echo htmlspecialchars($auditoria['auditor_responsavel']); ?></p>
        <p><strong>Data da auditoria:</strong> <?php echo date("d/m/Y H:i:s", strtotime($auditoria['realizado_em'])); ?></p>
        <p><strong>Resultado:</strong> <?php echo $auditoria['resultado']; ?>%</p>

        <h3>Respostas por Item</h3>
        <div class="table-wrapper">
            <table>
                <tr>
                    <th>Nº do Item</th>
                    <th>Resposta</th>
                    <th>Descrição</th>
                    <th>Status da NC</th>
                </tr>

                <?php 
                $temNC = false;
                while($r = $respostas->fetch_assoc()) {
                    $numero = $contador++;

                    if (!empty(trim($r['nc_status'] ?? ''))) {
                        $temNC = true;
                    }
                ?>
                    <tr>
                        <td><?php echo $numero; ?></td>
                        <td>
                            <?php
                            switch (strtoupper($r['resposta'])) {
                                case 'SIM':
                                    echo 'Sim';
                                    break;
                                case 'NAO':
                                    echo 'Não';
                                    break;
                                case 'NA':
                                    echo 'Não aplicável';
                                    break;
                                default:
                                    echo htmlspecialchars($r['resposta']);
                            }
                            ?>
                        </td>

                        <td><?php echo htmlspecialchars($r['descricao_item']); ?></td>

                        <td>
                            <?php
                            $status = strtoupper(trim($r['nc_status'] ?? ''));
                            switch ($status) {
                                case 'ABERTA':
                                    echo 'Aberta';
                                    break;
                                case 'EM ANDAMENTO':
                                    echo 'Em andamento';
                                    break;
                                case 'RESOLVIDA':
                                    echo 'Resolvida';
                                    break;
                                default:
                                    echo htmlspecialchars($r['nc_status']);
                            }
                            ?>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>

        <?php if (!$temNC) { ?>
            <p class="sem-nc">⚠️Nenhuma não conformidade encontrada.</p>
        <?php } ?>

        <a class="voltar" href="historico.php">⬅ Voltar</a>
    </div>

    <footer>
        &copy; <?php echo date('Y'); ?> Sistema de Auditoria. Todos os direitos reservados.
        <br>Desenvolvido por: Arthur Rodrigues, Jean Inácio, João Gabriel e Stefany Carlos.
    </footer>
</body>
</html>