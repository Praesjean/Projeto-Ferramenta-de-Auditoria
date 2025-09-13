<?php
session_start();
include('../../conecta_db.php');

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$usuario_nome = $_SESSION['usuario_nome'];
$usuario_email = $_SESSION['usuario_email'];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Auditoria | Dashboard</title>
    <link rel="stylesheet" href="../../global.css">
    <link href="../../styles/pages/dashboard/dashboard.css" rel="stylesheet">
</head>
<body>
    <div class="header">
        <div class="user-info">
            <p>Nome: <?php echo htmlspecialchars($usuario_nome); ?></p>
            <p>E-mail: <?php echo htmlspecialchars($usuario_email); ?></p>
        </div>

        <h1>Sistema de Auditoria</h1>

        <div>
            <a href="logout.php" class="logout-btn">Sair</a>
        </div>
    </div>

    <div class="main-content">
        <div class="container">
            <div class="card">
                <h3>Criar Checklist</h3>
                <p><a href="criar_checklist.php">Acessar</a></p>
            </div>
            <div class="card">
                <h3>Gerenciar Checklists</h3>
                <p><a href="gerenciar_checklists.php">Acessar</a></p>
            </div>
            <div class="card">
                <h3>Realizar Auditoria</h3>
                <p><a href="realizar_auditoria.php">Acessar</a></p>
            </div>
        </div>

        <div class="container-bottom">
            <div class="card">
                <h3>Histórico de Auditorias</h3>
                <p><a href="historico.php">Acessar</a></p>
            </div>
            <div class="card">
                <h3>Não Conformidades</h3>
                <p><a href="nao_conformidades.php">Acessar</a></p>
            </div>
        </div>
    </div>

    <footer>
        &copy; <?php echo date('Y'); ?> Sistema de Auditoria. Todos os direitos reservados.
        <br>
        Desenvolvido por: Arthur Rodrigues, Jean Inácio, João Gabriel e Stefany Carlos.
    </footer>
</body>
</html>