<?php
session_start();
include('../../conecta_db.php');

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$usuario_nome = $_SESSION['usuario_nome'];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f4f7;
            margin: 0;
            padding: 0;
        }
        .header {
            background: #0077cc;
            color: white;
            padding: 15px;
            text-align: center;
        }
        .container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            padding: 30px;
        }
        .card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 2px 8px rgba(0,0,0,0.2);
            text-align: center;
            transition: transform 0.2s;
        }
        .card:hover {
            transform: scale(1.05);
        }
        .card a {
            text-decoration: none;
            color: #0077cc;
            font-weight: bold;
        }
        .logout {
            text-align: center;
            margin-top: 20px;
        }
        .logout a {
            color: red;
            font-weight: bold;
            text-decoration: none;
        }
        .logout a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Bem-vindo, <?php echo htmlspecialchars($usuario_nome); ?>!</h2>
    </div>

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
        <div class="card">
            <h3>Histórico de Auditorias</h3>
            <p><a href="historico.php">Acessar</a></p>
        </div>
        <div class="card">
            <h3>Não Conformidades</h3>
            <p><a href="nao_conformidades.php">Acessar</a></p>
        </div>
    </div>

    <div class="logout">
        <a href="logout.php">Sair</a>
    </div>
</body>
</html>