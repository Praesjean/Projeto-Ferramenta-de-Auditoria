function atualizarBotoesExcluir() {
    const itensDiv = document.getElementById("itens");
    const children = Array.from(itensDiv.children);

    children.forEach((div, index) => {
        let btn = div.querySelector("button");
        if (children.length > 1) {
            if (!btn) {
                btn = document.createElement("button");
                btn.type = "button";
                btn.innerText = "✖";
                btn.style.background = "#e74c3c";
                btn.style.color = "white";
                btn.style.border = "none";
                btn.style.borderRadius = "50%";
                btn.style.width = "28px";
                btn.style.height = "28px";
                btn.style.fontSize = "16px";
                btn.style.cursor = "pointer";
                btn.style.transition = "all 0.2s";
                btn.style.display = "flex";
                btn.style.alignItems = "center";
                btn.style.justifyContent = "center";
                btn.style.marginLeft = "8px";
                btn.onmouseover = function() { btn.style.background = "#c0392b"; btn.style.transform = "scale(1.1)"; };
                btn.onmouseout = function() { btn.style.background = "#e74c3c"; btn.style.transform = "scale(1)"; };
                btn.onclick = function() { removerItem(btn); };
                div.appendChild(btn);
            }
        } else {
            if (btn) btn.remove();
        }
    });
}

function adicionarItem(valor = "") {
    const itensDiv = document.getElementById("itens");

    const div = document.createElement("div");
    div.style.display = "flex";
    div.style.alignItems = "center";
    div.style.marginBottom = "5px";

    const input = document.createElement("input");
    input.type = "text";
    input.name = "itens[]";
    input.value = valor;
    input.placeholder = "Descrição do item";
    input.style.flex = "1";
    input.style.padding = "8px";
    input.style.fontSize = "16px";
    div.appendChild(input);

    itensDiv.appendChild(div);
    atualizarBotoesExcluir();
}

function removerItem(btn) {
    const itensDiv = document.getElementById("itens");
    if (itensDiv.children.length > 1) {
        btn.parentNode.remove();
        atualizarBotoesExcluir();
    }
}

function usarTemplate() {
    const itensDiv = document.getElementById("itens");

    const template = [
        "O título do projeto foi preenchido?",
        "O objetivo do plano de projeto foi preenchido?",
        "A visão geral do projeto foi preenchida?",
        "A descrição do escopo do projeto foi preenchida?",
        "Os objetivos do projeto foram definidos?",
        "O cronograma do projeto foi documentado?",
        "O orçamento estimado foi incluído?",
        "A equipe do projeto foi identificada?",
        "Os riscos do projeto foram avaliados?",
        "As partes interessadas foram listadas?",
        "O plano de comunicação foi elaborado?",
        "Os critérios de sucesso foram estabelecidos?",
        "A metodologia de trabalho foi especificada?",
        "O escopo do produto foi validado?",
        "As entregas principais foram definidas?",
        "Os recursos necessários foram alocados?",
        "O plano de qualidade foi criado?",
        "Os indicadores de desempenho foram definidos?",
        "As responsabilidades foram atribuídas?",
        "Os documentos de referência foram anexados?",
        "A aprovação da liderança foi registrada?",
        "A descrição das atividades foi revisada?",
        "O plano de mudanças foi elaborado?",
        "As dependências foram mapeadas?",
        "Os marcos principais foram determinados?",
        "O plano de testes foi incluído?",
        "As restrições foram documentadas?",
        "O histórico de revisões foi atualizado?",
        "O plano de encerramento foi criado?",
        "O alinhamento com os objetivos estratégicos foi verificado?"
    ];

    const inputs = Array.from(itensDiv.querySelectorAll("input[name='itens[]']"));

    let templateIndex = 0;
    for (let i = 0; i < inputs.length && templateIndex < template.length; i++) {
        if (inputs[i].value.trim() === "") {
            inputs[i].value = template[templateIndex];
            templateIndex++;
        }
    }

    const existentes = inputs.map(i => i.value);
    for (; templateIndex < template.length; templateIndex++) {
        const item = template[templateIndex];
        if (!existentes.includes(item)) {
            adicionarItem(item);
        }
    }
}

window.onload = function() {
    if (document.getElementById("itens").children.length === 0) {
        adicionarItem();
    }
};