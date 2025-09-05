<?php include("conexao.php"); ?>
<!DOCTYPE html>
<html>
<head><title>Checklist</title></head>
<body>
<h2>Checklist da Auditoria</h2>
<form method="post">
<?php
$auditoria_id = $_GET['auditoria_id'];
$result = $conn->query("SELECT * FROM checklist");

while ($row = $result->fetch_assoc()) {
    echo "<p>{$row['pergunta']}</p>";
    echo "<input type='radio' name='resposta[{$row['id']}]' value='Sim'> Sim ";
    echo "<input type='radio' name='resposta[{$row['id']}]' value='Nao'> Não ";
    echo "<input type='radio' name='resposta[{$row['id']}]' value='NA'> N/A <br>";
}
?>
<br>
<button type="submit">Finalizar Auditoria</button>
</form>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    foreach ($_POST['resposta'] as $checklist_id => $resposta) {
        $conn->query("INSERT INTO respostas (auditoria_id, checklist_id, resposta)
                      VALUES ($auditoria_id, $checklist_id, '$resposta')");

        if ($resposta == "Nao") {
            $descricao = "Não conformidade no item $checklist_id";
            $conn->query("INSERT INTO nao_conformidades (auditoria_id, checklist_id, descricao)
                          VALUES ($auditoria_id, $checklist_id, '$descricao')");
        }
    }
    header("Location: resultado.php?auditoria_id=$auditoria_id");
    exit;
}
?>
</body>
</html>
