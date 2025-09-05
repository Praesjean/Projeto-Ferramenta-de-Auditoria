<?php include("../../conecta_db.php"); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Auditoria Visual</title>
    <link rel="stylesheet" href="../../styles/global/global.css">
    <link rel="stylesheet" href="../../styles/pages/auditoria-visual.css">
    <script src="../../scripts/pages/global.js" defer></script>
    <script src="../../scripts/pages/auditoria-visual.js" defer></script>
</head>
<body>
<div class="container">
<h2>Checklist Visual</h2>
<h3>Percentual de aderência: <span id="aderencia">0%</span></h3>

<?php
$auditoria_id = $_POST['auditoria_id'] ?? 0;
$checklist = $conn->query("SELECT * FROM checklist");
?>

<form method="post" action="auditoria-alertas.php">
<input type="hidden" name="auditoria_id" value="<?= $auditoria_id ?>">
<?php while($item = $checklist->fetch_assoc()){ ?>
<div class="checklist-item">
<p><b><?= $item['pergunta'] ?></b></p>
<label><input type="radio" name="resposta[<?= $item['id'] ?>]" value="Sim" required onclick="atualizarNC(<?= $item['id'] ?>,'Sim')"> Sim</label>
<label><input type="radio" name="resposta[<?= $item['id'] ?>]" value="Nao" onclick="atualizarNC(<?= $item['id'] ?>,'Nao')"> Não</label>
<label><input type="radio" name="resposta[<?= $item['id'] ?>]" value="NA" onclick="atualizarNC(<?= $item['id'] ?>,'NA')"> N/A</label>
</div>
<?php } ?>

<h3>Não Conformidades:</h3>
<div id="nc-list"></div>

<button type="submit">Finalizar</button>
</form>
</div>
</body>
</html>
