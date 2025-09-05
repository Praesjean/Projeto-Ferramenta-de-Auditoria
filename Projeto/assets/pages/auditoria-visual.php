<?php include("../../conecta_db.php"); ?>
<!DOCTYPE html>
<html>
    <head>
        <title>Auditoria Visual</title>
        <link rel="stylesheet" href="../../styles/global/global.css">
        <link rel="stylesheet" href="../../styles/pages/auditoria-visual.css">
        <script src="../../scripts/pages/global.js" defer></script>
    </head>
    <body>
        <div class="container">
            <h2>Checklist Final</h2>
            <h3>Percentual de aderência:</h3>
            <div id="progress" class="progress-circle">0%</div>

            <?php
                $auditoria_id = $_POST['auditoria_id'] ?? 0;
                $checklist = $conn->query("SELECT * FROM checklist");
            ?>

            <table class="checklist-table">
                <thead>
                    <tr>
                        <th>Pergunta</th>
                        <th>Resposta</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($item = $checklist->fetch_assoc()){ ?>
                        <tr>
                            <td><?= $item['pergunta'] ?></td>
                            <td><?= $_POST['resposta'][$item['id']] ?? '-' ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <h3>Não Conformidades</h3>
            <div id="nc-list">
                <?php
                if(isset($_POST['resposta'])){
                    foreach($_POST['resposta'] as $id_checklist => $resposta){
                        if($resposta == 'Nao'){
                            echo "<div class='nc-item'>Não conformidade no item {$id_checklist}</div>";
                        }
                    }
                }
                ?>
            </div>
            <script src="../../scripts/pages/auditoria-visual.js"></script>
        </div>
    </body>
</html>
