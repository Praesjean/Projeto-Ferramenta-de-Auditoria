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
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f4f7;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .header {
            background: #0077cc;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
        }

        .header .user-info p {
            margin: 2px 0;
            font-weight: normal;
            font-size: 16px;
        }

        .header h1 {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            margin: 0;
            font-size: 24px;
            font-weight: bold;
            text-align: center;
        }

        .header .logout-btn {
            background: #e74c3c;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            transition: background 0.3s, transform 0.2s;
        }

        .header .logout-btn:hover {
            background: #c0392b;
            transform: scale(1.05);
        }

        .main-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding:95px;
        }

        .container, .container-bottom {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
            margin-bottom: 20px;
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

        footer {
            background: #0077cc;
            color: white;
            text-align: center;
            padding: 20px 0;
            position: fixed;
            bottom: 0;
            width: 100%;
            font-size: 16px;
            line-height: 1.5em;
        }
    </style>
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
</body>

<footer>
    &copy; <?php echo date('Y'); ?> Sistema de Auditoria. Todos os direitos reservados.
    <br>
    Desenvolvido por: Arthur Rodrigues, Jean Inácio, João Gabriel e Stefany Carlos.
</footer>

</html>