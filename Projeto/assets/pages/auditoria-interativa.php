<?php include("../../conecta_db.php"); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Auditoria Interativa</title>
    <link rel="stylesheet" href="../../styles/global/global.css">
    <link rel="stylesheet" href="../../styles/pages/auditoria-interativa.css">
    <script src="../../scripts/pages/global.js" defer></script>
    <script src="../../scripts/pages/auditoria-interativa.js" defer></script>
</head>
<body>
<div class="container">
<h2>Checklist Interativo</h2>

<?php
$artefato = $_POST['artefato'] ?? '';
$conn->query("INSERT INTO auditorias (artefato) VALUES ('$artefato')");
$auditoria_id = $conn->insert_id;

$checklist = $conn->query("SELECT * FROM checklist");
?>

<form method="post" action="auditoria-visual.php">
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

    <button type="submit">Continuar</button>
</form>
</div>
</body>
</html>
