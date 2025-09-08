<?php
session_start();
include '../../conecta_db.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_POST['nc_id'])) {
    die("Não foi informado qual NC enviar.");
}

$nc_id = intval($_POST['nc_id']);

$sql = "SELECT nc.id, nc.descricao, nc.criado_em, nc.status, 
               a.resultado, a.realizado_em, c.titulo AS projeto, 
               u.nome AS responsavel, c.auditor AS rqa_responsavel
        FROM nao_conformidades nc
        JOIN auditorias a ON nc.auditoria_id = a.id
        JOIN checklists c ON a.checklist_id = c.id
        JOIN usuarios u ON a.usuario_id = u.id
        WHERE nc.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $nc_id);
$stmt->execute();
$nc = $stmt->get_result()->fetch_assoc();

if (!$nc) die("Não conformidade não encontrada.");

$to = "destinatario@empresa.com";
$subject = "Solicitação de Resolução de Não Conformidade";
$message = "
Solicitação de Resolução de Não Conformidade
Código de Controle: {$nc['id']} - {$nc['descricao']}
Projeto: {$nc['projeto']}
Responsável: {$nc['responsavel']}
Data de Solicitação: ".date("d/m/Y", strtotime($nc['criado_em']))."
Prazo de Resolução: ".date("d/m/Y", strtotime($nc['criado_em']." +3 days"))."
Data da Solução: 
RQA Responsável: {$nc['rqa_responsavel']}
Você tem 24 horas úteis para contestação
Descrição:
{$nc['descricao']}
Classificação
Média-Simples | 3 Dias
Ação Corretiva Indicada
CM | Corrigir itens conforme padrão
Histórico de Escalonamento
Superior Responsável
Prazo para Resolução
";

$headers = "From: sistema@auditoria.com\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

if(mail($to, $subject, $message, $headers)) {
    echo "✅ E-mail enviado com sucesso!";
} else {
    echo "❌ Falha ao enviar e-mail.";
}

echo "<br><a href='nao_conformidades.php'>⬅ Voltar</a>";
?>