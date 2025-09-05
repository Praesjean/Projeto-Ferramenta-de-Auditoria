<?php include("conexao.php"); ?>
<!DOCTYPE html>
<html>
<head><title>Resultado</title></head>
<body>
<h2>Resultado da Auditoria</h2>
<?php
$auditoria_id = $_GET['auditoria_id'];
$respostas = $conn->query("SELECT resposta FROM respostas WHERE auditoria_id=$auditoria_id");

$total = 0; $sim = 0;
while ($r = $respostas->fetch_assoc()) {
    if ($r['resposta'] != "NA") {
        $total++;
        if ($r['resposta'] == "Sim") $sim++;
    }
}
$aderencia = ($total > 0) ? round(($sim / $total) * 100, 2) : 0;

echo "Aderência: <b>$aderencia%</b><br><br>";

echo "<h3>Não Conformidades</h3>";
$ncs = $conn->query("SELECT * FROM nao_conformidades WHERE auditoria_id=$auditoria_id");
while ($nc = $ncs->fetch_assoc()) {
    echo "NC #{$nc['id']}: {$nc['descricao']} - Status: {$nc['status']}<br>";
    echo "<a href='nc.php?id={$nc['id']}'>Gerenciar</a><br><br>";
}
?>
</body>
</html>
