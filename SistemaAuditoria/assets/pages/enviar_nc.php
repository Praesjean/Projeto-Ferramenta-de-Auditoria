<?php
session_start();
include('../../conecta_db.php');

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$usuario_nome = $_SESSION['usuario_nome'];
$msg = "";

require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';
require 'phpmailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_GET['nc_id'])) {
    die("Não foi especificada a não conformidade.");
}

$nc_id = intval($_GET['nc_id']);

$sql = "SELECT 
            nc.id AS nc_id,
            nc.descricao AS nc_descricao,
            nc.criado_em AS nc_criado,
            ar.descricao_item AS descricao_item,
            a.titulo_checklist AS titulo_checklist,
            u_responsavel.nome AS responsavel_nome,
            u_responsavel.email AS responsavel_email,
            a.auditor_responsavel AS auditor_nome
        FROM nao_conformidades nc
        JOIN auditorias a ON nc.auditoria_id = a.id
        JOIN auditoria_respostas ar ON nc.item_id = ar.item_id AND a.id = ar.auditoria_id
        JOIN usuarios u_responsavel ON a.usuario_id = u_responsavel.id
        WHERE nc.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $nc_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Não conformidade não encontrada.");
}

$nc = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enviar_email'])) {
    $destinatario = trim($_POST['destinatario']);
    $assunto = trim($_POST['assunto']);

    $mensagem_editada =
        "Código de Controle: {$nc['nc_id']}\n".
        "Projeto: {$nc['titulo_checklist']}\n".
        "Responsável: {$_POST['responsavel']}\n".
        "Data de Solicitação: ".date("d/m/Y H:i", strtotime($nc['nc_criado']))."\n".
        "Nº de Escalonamentos: {$_POST['escalonamentos']}\n".
        "Data de Resolução: {$_POST['data_resolucao']}\n".
        "RQA Responsável: {$nc['auditor_nome']}\n".
        "----------------------------------------\n".
        "Classificação: {$_POST['classificacao']}\n".
        "Prazo: {$_POST['prazo']}\n".
        "Descrição: {$nc['descricao_item']}\n".
        "Ação Corretiva Indicada: {$_POST['acao_corretiva']}\n".
        "Observações: {$_POST['observacoes']}";

    if (!empty($destinatario) && !empty($assunto)) {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'petmap0328@gmail.com';
            $mail->Password   = 'ylqq oeep crzl nkuu';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'base64';

            $mail->setFrom('naoconformidades@sistema.com.br', "$usuario_nome (Sistema de Auditoria)");
            $mail->addAddress($destinatario);

            $mensagem_html = nl2br(htmlspecialchars($mensagem_editada));

            $mail->isHTML(true);
            $mail->Subject = $assunto;
            $mail->Body    = "<div style='font-family:Arial,sans-serif; line-height:1.5;'>$mensagem_html</div>";
            $mail->AltBody = strip_tags($mensagem_editada);

            $mail->send();
            $msg = "✅ E-mail enviado para $destinatario!";
        } catch (Exception $e) {
            $msg = "❌ Erro ao enviar e-mail: {$mail->ErrorInfo}";
        }
    } else {
        $msg = "⚠️ Preencha todos os campos obrigatórios.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Auditoria | Enviar E-mail</title>
    <link href="../../styles/pages/enviar_nc/enviar_nc.css" rel="stylesheet">
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
        <h2>Enviar E-mail</h2>

        <?php if ($msg) echo "<p class='msg'>$msg</p>"; ?>

        <form method="POST">
            <label>Destinatário</label>
            <input type="email" name="destinatario" placeholder="exemplo@gmail.com" required>

            <label>Assunto</label>
            <input type="text" name="assunto" 
                   value="Solicitação de Resolução de Não Conformidade" required>

            <hr>

            <label>Código de Controle</label>
            <input type="text" value="<?php echo $nc['nc_id']; ?>" readonly>

            <label>Projeto</label>
            <input type="text" value="<?php echo htmlspecialchars($nc['titulo_checklist']); ?>" readonly>

            <label>Responsável</label>
            <input type="text" name="responsavel" placeholder="Digite o responsável" required>

            <label>Data de Solicitação</label>
            <input type="text" value="<?php echo date('d/m/Y H:i', strtotime($nc['nc_criado'])); ?>" readonly>

            <label>Nº de Escalonamentos</label>
            <input type="text" name="escalonamentos" placeholder="Digite o número">

            <label>Data de Resolução</label>
            <input type="text" name="data_resolucao" placeholder="Digite a data de resolução">

            <label>RQA Responsável</label>
            <input type="text" value="<?php echo htmlspecialchars($nc['auditor_nome']); ?>" readonly>

            <hr>

            <label>Classificação</label>
            <select name="classificacao" id="classificacao" required>
                <option value="">Selecione...</option>
                <option value="Baixa">Baixa</option>
                <option value="Média">Média</option>
                <option value="Alta">Alta</option>
            </select>

            <label>Prazo (dias)</label>
            <input type="text" name="prazo" id="prazo">

            <label>Descrição</label>
            <input type="text" value="<?php echo htmlspecialchars($nc['descricao_item']); ?>" readonly>

            <label>Ação Corretiva Indicada</label>
            <textarea name="acao_corretiva" placeholder="Digite a ação corretiva" required></textarea>

            <label>Observações</label>
            <textarea name="observacoes" placeholder="Digite observações"></textarea>

            <div class="btn-container">
                <button type="submit" name="enviar_email">Enviar</button>
            </div>
        </form>

        <a class="voltar" href="nao_conformidades.php">⬅ Voltar</a>
    </div>

    <footer>
        &copy; <?php echo date('Y'); ?> Sistema de Auditoria. Todos os direitos reservados.
        <br>Desenvolvido por: Arthur Rodrigues, Jean Inácio, João Gabriel e Stefany Carlos.
    </footer>

    <script>
    document.getElementById('classificacao').addEventListener('change', function () {
        let prazoInput = document.getElementById('prazo');
        switch (this.value) {
            case 'Baixa':
                prazoInput.value = "5 dias";
                break;
            case 'Média':
                prazoInput.value = "3 dias";
                break;
            case 'Alta':
                prazoInput.value = "2 dias";
                break;
            default:
                prazoInput.value = "";
        }
    });
    </script>
</body>
</html>