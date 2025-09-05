<?php include("../../conecta_db.php"); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Checklist Completo de Auditoria</title>
    <link rel="stylesheet" href="../../styles/global/global.css">
    <link rel="stylesheet" href="../../styles/pages/auditoria-interativa.css">
    <script src="../../scripts/pages/auditoria-interativa.js" defer></script>
</head>
<body>
    <div class="container">
        <h2>Checklist Completo de Auditoria</h2>

        <?php
        $artefato = $_POST['artefato'] ?? '';
        if ($artefato != '') {
            $stmt = $conn->prepare("INSERT INTO auditorias (artefato) VALUES (?)");
            $stmt->bind_param("s", $artefato);
            $stmt->execute();
            $auditoria_id = $stmt->insert_id;
            $stmt->close();
        }

        $audit_questions = [
            "Existe documentação atualizada do processo?",
            "Os procedimentos estão sendo seguidos corretamente?",
            "Há registros de auditorias anteriores?",
            "Os responsáveis foram treinados adequadamente?",
            "Resultados são monitorados periodicamente?",
            "Há plano de ação documentado para melhorias?",
            "Os prazos definidos são cumpridos?",
            "As responsabilidades estão claramente definidas?",
            "Existe controle de acesso às informações sensíveis?",
            "Os equipamentos estão em boas condições de operação?",
            "Existe registro de manutenção preventiva?",
            "Os indicadores de desempenho são monitorados?",
            "As não conformidades anteriores foram corrigidas?",
            "Os funcionários têm conhecimento das normas aplicáveis?",
            "Há política de segurança da informação implementada?",
            "Existe evidência de conformidade com requisitos legais?",
            "Há revisão periódica de processos críticos?",
            "Os registros estão completos e legíveis?",
            "Existe plano de contingência documentado?",
            "Os riscos foram identificados e tratados adequadamente?",
            "Existe monitoramento de incidentes e falhas?",
            "Há segregação de funções nas áreas críticas?",
            "O ambiente físico é seguro e organizado?",
            "As auditorias anteriores geraram ações corretivas efetivas?",
            "Os fornecedores seguem requisitos contratuais e normativos?",
            "Os procedimentos de TI estão documentados e atualizados?",
            "Existe controle de backup e recuperação de dados?",
            "Os acessos a sistemas são revisados periodicamente?",
            "Há políticas de senhas e autenticação seguras?",
            "Os controles internos são testados regularmente?",
            "Existe avaliação periódica de desempenho dos colaboradores?",
            "Há comunicação efetiva de mudanças nos processos?",
            "Os registros de produção e qualidade são consistentes?",
            "Há plano de melhoria contínua documentado?",
            "Existe gestão de incidentes de segurança da informação?",
            "Há monitoramento de conformidade legal e regulatória?",
            "O fluxo de aprovação é seguido corretamente?",
            "Há revisão periódica de contratos e acordos?",
            "As métricas de qualidade são analisadas e documentadas?",
            "Os riscos operacionais são identificados, analisados e mitigados?"
        ];
        ?>

        <form method="post" action="auditoria-visual.php" id="checklist-form">
            <input type="hidden" name="auditoria_id" value="<?= $auditoria_id ?>">

            <table class="audit-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Pergunta</th>
                        <th>Sim</th>
                        <th>Não</th>
                        <th>N/A</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($audit_questions as $i => $q): ?>
                    <tr class="audit-row">
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($q) ?></td>
                        <td><input type="radio" name="resposta[<?= $i ?>]" value="Sim" required></td>
                        <td><input type="radio" name="resposta[<?= $i ?>]" value="Nao"></td>
                        <td><input type="radio" name="resposta[<?= $i ?>]" value="NA"></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <h3>Não Conformidades / Observações</h3>
            <div id="nc-list"></div>

            <button type="submit" class="submit-btn">Finalizar Auditoria</button>
        </form>
    </div>
</body>
</html>
