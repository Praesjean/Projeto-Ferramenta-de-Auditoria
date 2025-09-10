<?php
session_start();
include('../../conecta_db.php');

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

if (!isset($_GET['id'])) {
    header("Location: historico.php");
    exit;
}

$auditoria_id = intval($_GET['id']);

$conn->begin_transaction();

try {
    $stmt = $conn->prepare("DELETE FROM nao_conformidades WHERE auditoria_id=?");
    $stmt->bind_param("i", $auditoria_id);
    $stmt->execute();

    $stmt = $conn->prepare("DELETE FROM auditoria_respostas WHERE auditoria_id=?");
    $stmt->bind_param("i", $auditoria_id);
    $stmt->execute();

    $stmt = $conn->prepare("DELETE FROM auditorias WHERE id=? AND usuario_id=?");
    $stmt->bind_param("ii", $auditoria_id, $usuario_id);
    $stmt->execute();

    $conn->commit();
    header("Location: historico.php?sucesso=1");
} catch (Exception $e) {
    $conn->rollback();
    die("Erro ao excluir auditoria: " . $e->getMessage());
}
?>