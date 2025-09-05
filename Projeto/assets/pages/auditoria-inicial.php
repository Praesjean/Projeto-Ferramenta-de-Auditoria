<?php include("../../conecta_db.php"); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Iniciar Auditoria</title>
    <link rel="stylesheet" href="../../styles/global/global.css">
    <link rel="stylesheet" href="../../styles/pages/auditoria-inicial.css">
    <script src="../../scripts/pages/auditoria-inicial.js" defer></script>
</head>
<body>
<div class="container">
    <h2>Iniciar Nova Auditoria</h2>
    <form method="post" action="auditoria-interativa.php">
        Artefato avaliado: <input type="text" name="artefato" required>
        <button type="submit">Iniciar Auditoria</button>
    </form>
</div>
</body>
</html>
