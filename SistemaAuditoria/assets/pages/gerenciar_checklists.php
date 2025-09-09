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
        $mensagem = "Checklist excluído!";
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

        .sem-checklist {
            text-align: center;
            margin: 20px 0;
            font-size: 16px;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Gerenciar Checklists</h2>

        <?php if ($resultado->num_rows > 0) { ?>
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
                            <a class="excluir" href="#" onclick="confirmarExclusao(<?php echo $row['id']; ?>)">Excluir</a>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        <?php } else { ?>
            <p class="sem-checklist">Nenhum checklist cadastrado até o momento.</p>
        <?php } ?>

        <a class="voltar" href="dashboard.php">⬅ Voltar</a>
    </div>
</body>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmarExclusao(id) {
    Swal.fire({
        title: 'Tem certeza?',
        text: "Você não poderá reverter essa ação!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sim, excluir!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'gerenciar_checklists.php?excluir=' + id;
        }
    });
}
</script>
<script>
<?php if ($mensagem): ?>
Swal.fire({
    icon: '<?php echo strpos($mensagem, "Erro") !== false ? "error" : "success"; ?>',
    title: '<?php echo $mensagem; ?>',
    showConfirmButton: true,
    confirmButtonText: 'OK',
    confirmButtonColor: '#28a745',
    customClass: {
        confirmButton: 'swal2-confirm-green',
    }
});
<?php endif; ?>
</script>

<style>
.swal2-confirm-green {
    border: none !important;
    box-shadow: none !important;
    font-weight: normal !important;
}
</style>

</html>