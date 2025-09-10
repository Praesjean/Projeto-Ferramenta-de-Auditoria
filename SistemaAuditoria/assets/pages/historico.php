<?php
session_start();
include('../../conecta_db.php');

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$sucesso = isset($_GET['sucesso']) ? intval($_GET['sucesso']) : 0;
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
    <title>Sistema de Auditoria | Histórico de Auditorias</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding-top: 80px;
            padding-bottom: 80px;
            min-height: 100vh;
            box-sizing: border-box;
        }

        .header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            padding: 15px 30px;
            background: #0077cc;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-sizing: border-box;
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

        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            max-width: 900px;
            margin: 50px auto 40px auto;
            box-sizing: border-box;
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

        .excluir-btn {
            display: inline-block;
            padding: 6px 12px;
            background: #e74c3c;
            color: white;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
            margin-left: 5px;
        }

        .excluir-btn:hover {
            background: #c0392b;
            transform: scale(1.05);
        }

        .sem-auditoria {
            text-align: center;
            margin: 20px 0;
            font-size: 16px;
            color: #555;
        }

        footer {
            background: #0077cc;
            color: white;
            text-align: center;
            padding: 20px 0;
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            box-sizing: border-box;
            font-size: 16px;
            line-height: 1.5em;
        }
    </style>
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
                    <td>
                        <a class="detalhes-btn" href="ver_auditoria.php?id=<?php echo $a['id']; ?>">Verificar</a>
                        <button class="excluir-btn" data-id="<?php echo $a['id']; ?>">Excluir</button>
                    </td>
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

    <footer>
        &copy; <?php echo date('Y'); ?> Sistema de Auditoria. Todos os direitos reservados.
        <br>Desenvolvido por: Arthur Rodrigues, Jean Inácio, João Gabriel e Stefany Carlos.
    </footer>
</body>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.querySelectorAll('.excluir-btn').forEach(button => {
    button.addEventListener('click', function() {
        const auditoriaId = this.getAttribute('data-id');

        Swal.fire({
            title: 'Tem certeza?',
            text: "Esta auditoria será excluída permanentemente!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74c3c',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sim, excluir!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'excluir_auditoria.php?id=' + auditoriaId;
            }
        });
    });
});
</script>
<script>
<?php if($sucesso === 1): ?>
Swal.fire({
    icon: 'success',
    title: 'Auditoria excluída!',
    text: 'A auditoria foi removida com sucesso.',
    confirmButtonColor: '#28a745'
});
<?php endif; ?>
</script>

</html>