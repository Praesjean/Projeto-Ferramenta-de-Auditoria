<?php include("../../conecta_db.php"); ?>
<!DOCTYPE html>
<html>
    <head>
        <title>Auditoria Interativa</title>
        <link rel="stylesheet" href="../../styles/global/global.css">
        <link rel="stylesheet" href="../../styles/pages/auditoria-interativa.css">
        <script src="../../scripts/pages/global.js" defer></script>
    </head>
    <body>
        <div class="container">
            <h2>Checklist Interativo</h2>

            <?php
                $artefato = $_POST['artefato'] ?? '';
                if ($artefato != '') {
                    $conn->query("INSERT INTO auditorias (artefato) VALUES ('$artefato')");
                    $auditoria_id = $conn->insert_id;
                }
                $checklist = $conn->query("SELECT * FROM checklist");
            ?>

            <form method="post" action="auditoria-visual.php">
                <input type="hidden" name="auditoria_id" value="<?= $auditoria_id ?>">
                <table class="checklist-table">
                    <thead>
                        <tr>
                            <th>Pergunta</th>
                            <th>Sim</th>
                            <th>Não</th>
                            <th>N/A</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($item = $checklist->fetch_assoc()){ ?>
                            <tr class="checklist-item">
                                <td><?= $item['pergunta'] ?></td>
                                <td><input type="radio" name="resposta[<?= $item['id'] ?>]" value="Sim" required></td>
                                <td><input type="radio" name="resposta[<?= $item['id'] ?>]" value="Nao"></td>
                                <td><input type="radio" name="resposta[<?= $item['id'] ?>]" value="NA"></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <h3>Não Conformidades</h3>
                <div id="nc-list">
                </div>
                <button type="submit">Continuar</button>
            </form>
        <script src="../../scripts/pages/auditoria-interativa.js"></script>
        </div>
    </body>
</html>
