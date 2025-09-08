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
            padding-top: 30px;
            padding-bottom: 30px;
        }

        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }

        .header h2 {
            margin: 5px 0 0 0;
            padding-top: 5px;
            font-size: 20px;
            font-weight: normal;
        }

        .container {
            display: flex;
            justify-content: center;
            gap: 20px;
            padding: 30px;
            margin-top: 30px;
        }

        .container-bottom {
            display: flex;
            justify-content: center;
            gap: 20px;
            padding: 0 30px 30px 30px;
        }

        .card {
            background: white;
            width: 220px;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0px 4px 12px rgba(0,0,0,0.15);
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0px 8px 20px rgba(0,0,0,0.25);
        }

        .card a {
            display: inline-block;
            margin-top: 10px;
            text-decoration: none;
            color: white;
            background: #0077cc;
            padding: 10px 15px;
            border-radius: 8px;
            transition: background 0.3s, transform 0.2s;
            font-size: 16px;
        }

        .card a:hover {
            background: #005fa3;
            transform: scale(1.05);
        }

        .logout {
            text-align: center;
            margin: 30px 0;
        }

        .logout a {
            color: red;
            font-weight: bold;
            text-decoration: none;
            padding: 10px 20px;
            border: 2px solid red;
            border-radius: 8px;
            transition: background 0.3s, color 0.3s, transform 0.2s;
            font-size: 16px;
        }

        .logout a:hover {
            background: red;
            color: white;
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Sistema de Auditoria</h1>
        <h2>Olá, <?php echo htmlspecialchars($usuario_nome); ?>!</h2>
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

    <div class="logout">
        <a href="logout.php">Sair</a>
    </div>
</body>
</html>