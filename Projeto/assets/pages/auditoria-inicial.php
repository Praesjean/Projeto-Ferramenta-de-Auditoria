<?php include("../../conecta_db.php"); ?>
<!DOCTYPE html>
<html>
    <head>
        <title>Iniciar Auditoria</title>
        <link rel="stylesheet" href="../../styles/global/global.css">
        <link rel="stylesheet" href="../../styles/pages/auditoria-inicial.css">
    </head>
    <body>
        <div class="container">
            <h2>Iniciar Nova Auditoria</h2>
            <form method="post" action="auditoria-interativa.php">
                <div class="form-group">
                    <label>Artefato Avaliado:</label>
                    <input type="text" name="artefato" placeholder="Ex: Documento de processo, sistema, relatório" required>
                </div>

                <div class="form-group">
                    <label>Descrição do Artefato:</label>
                    <textarea name="descricao" rows="5" placeholder="Descreva detalhes do artefato"></textarea>
                </div>

                <div class="form-group">
                    <label>Responsável pela Auditoria:</label>
                    <input type="text" name="responsavel" placeholder="Nome do auditor" required>
                </div>

                <div class="form-group">
                    <label>Data da Auditoria:</label>
                    <input type="date" name="data_auditoria" required>
                </div>

                <div class="form-group">
                    <label>Prioridade:</label>
                    <select name="prioridade" required>
                        <option value="">Selecione</option>
                        <option value="Alta">Alta</option>
                        <option value="Media">Média</option>
                        <option value="Baixa">Baixa</option>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="submit">Iniciar Auditoria</button>
                    <button type="reset" class="reset-btn">Limpar Campos</button>
                </div>
            </form>
        </div>
    </body>
</html>
