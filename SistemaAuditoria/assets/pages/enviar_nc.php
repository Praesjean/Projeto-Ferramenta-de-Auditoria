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

$mensagem_template = "Código de Controle: {$nc['nc_id']}\n";
$mensagem_template .= "Projeto: {$nc['titulo_checklist']}\n";
$mensagem_template .= "Responsável: \n";
$mensagem_template .= "Data de Solicitação: ".date("d/m/Y H:i", strtotime($nc['nc_criado']))."\n";
$mensagem_template .= "Nº de Escalonamentos: \n";
$mensagem_template .= "Data de Resolução: \n";
$mensagem_template .= "RQA Responsável: {$nc['auditor_nome']}\n";
$mensagem_template .= "----------------------------------------\n";
$mensagem_template .= "Classificação:\n";
$mensagem_template .= "Descrição: {$nc['descricao_item']}\n";
$mensagem_template .= "Ação Corretiva Indicada:\n";
$mensagem_template .= "Observações:";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enviar_email'])) {
    $destinatario = trim($_POST['destinatario']);
    $assunto = trim($_POST['assunto']);
    $mensagem_editada = trim($_POST['mensagem']);

    if (!empty($destinatario) && !empty($assunto) && !empty($mensagem_editada)) {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'petmap0328@gmail.com';
            $mail->Password   = 'ylqq oeep crzl nkuu';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

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
        $msg = "⚠️ Preencha todos os campos.";
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
            <input type="text" name="assunto" placeholder="Digite o assunto" value="Solicitação de Resolução de Não Conformidade" required>

            <label>Mensagem</label>
            <textarea name="mensagem"><?php echo htmlspecialchars($mensagem_template); ?></textarea>

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
</body>
</html>