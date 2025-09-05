<?php include("conexao.php"); ?>
<!DOCTYPE html>
<html>
<head><title>Nova Auditoria</title></head>
<body>
<h2>Iniciar Nova Auditoria</h2>
<form method="post">
    Artefato avaliado: <input type="text" name="artefato" required>
    <button type="submit">Iniciar</button>
</form>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $artefato = $_POST['artefato'];
    $sql = "INSERT INTO auditorias (artefato) VALUES ('$artefato')";
    $conn->query($sql);
    $auditoria_id = $conn->insert_id;
    header("Location: responder.php?auditoria_id=$auditoria_id");
    exit;
}
?>
</body>
</html>
