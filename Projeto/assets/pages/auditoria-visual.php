<?php include("../../conecta_db.php"); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Auditoria Visual</title>
    <link rel="stylesheet" href="../../styles/global/global.css">
    <link rel="stylesheet" href="../../styles/pages/auditoria-visual.css">
    <script src="../../scripts/pages/auditoria-visual.js" defer></script>
</head>
<body>
    <div class="container">
        <h2>Checklist Final</h2>

        <?php
        $auditoria_id = $_POST['auditoria_id'] ?? 0;
        $respostas = $_POST['resposta'] ?? [];
        $nc_items = $_POST['nc'] ?? [];

        $total = count($respostas);
        $sim_count = 0;
        foreach($respostas as $r){
            if($r === "Sim") $sim_count++;
        }
        $percent = $total > 0 ? round(($sim_count / $total) * 100) : 0;
        ?>

        <h3>Percentual de aderência:</h3>
        <div id="progress" class="progress-circle" data-percent="<?= $percent ?>">
            <svg class="progress-svg" width="120" height="120">
                <circle class="bg" cx="60" cy="60" r="50"></circle>
                <circle class="progress" cx="60" cy="60" r="50"></circle>
            </svg>
            <div class="progress-text">0%</div>
        </div>


        <table class="checklist-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Pergunta</th>
                    <th>Resposta</th>
                </tr>
            </thead>
            <tbody>
                <?php
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

                foreach($audit_questions as $i => $q): ?>
                    <tr class="<?= ($respostas[$i] ?? '') === 'Nao' ? 'nc-row' : '' ?>">
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($q) ?></td>
                        <td><?= htmlspecialchars($respostas[$i] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h3>Não Conformidades</h3>
        <div id="nc-list">
            <?php foreach($nc_items as $id => $nc): ?>
                <div class="nc-item">Item <?= $id ?>: <?= htmlspecialchars($nc) ?></div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
