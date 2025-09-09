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
        "Verificar extintores de incêndio",
        "Checar saídas de emergência",
        "Conferir uso de EPIs",
        "Testar alarmes de incêndio",
        "Verificar iluminação de emergência",
        "Checar treinamento da equipe",
        "Conferir sinalização de segurança",
        "Inspecionar condições elétricas",
        "Checar escadas e corrimãos",
        "Avaliar risco de quedas",
        "Conferir primeiros socorros",
        "Checar kit de emergência",
        "Verificar estoque de EPIs",
        "Testar portas corta-fogo",
        "Checar válvulas de gás",
        "Inspecionar armazenamento de produtos químicos",
        "Verificar ventilação",
        "Checar ergonomia dos postos",
        "Avaliar condições de limpeza",
        "Conferir higienização de banheiros",
        "Checar uso de máscaras",
        "Conferir lavagem de mãos",
        "Verificar controle de pragas",
        "Checar descarte de lixo",
        "Avaliar organização do ambiente",
        "Conferir estoque de limpeza",
        "Checar higiene de equipamentos",
        "Verificar procedimentos de segurança",
        "Checar plano de evacuação",
        "Avaliar conformidade geral"
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