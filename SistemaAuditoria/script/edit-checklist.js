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


document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('form');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const isValid = validarFormulario(form);

        if (!isValid) return;

        Swal.fire({
            title: 'Tem certeza?',
            text: "Deseja salvar as alterações deste checklist?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sim, salvar!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    atualizarBotoesExcluir();
});

function validarFormulario(form) {
    let valid = true;

    const titulo = form.querySelector("input[name='titulo']");
    const auditor = form.querySelector("input[name='auditor']");
    const autor = form.querySelector("input[name='autor_documento']");
    const descricao = form.querySelector("textarea[name='descricao']");
    const itens = [...form.querySelectorAll("input[name='itens[]']")];

    if (!titulo || titulo.value.trim() === "") {
        setError(titulo, "O campo Título é obrigatório.");
        valid = false;
    } else {
        removeError(titulo);
    }

    if (!auditor || auditor.value.trim() === "") {
        setError(auditor, "O campo Auditor Responsável é obrigatório.");
        valid = false;
    } else {
        removeError(auditor);
    }

    if (!autor || autor.value.trim() === "") {
        setError(autor, "O campo Autor do Documento é obrigatório.");
        valid = false;
    } else {
        removeError(autor);
    }

    if (!descricao || descricao.value.trim() === "") {
        setError(descricao, "O campo Descrição é obrigatório.");
        valid = false;
    } else {
        removeError(descricao);
    }

    let hasEmptyItem = false;
    itens.forEach((item) => {
        if (item.value.trim() === "") {
            setError(item, "Preencha todos os itens do checklist.");
            hasEmptyItem = true;
        } else {
            removeError(item);
        }
    });

    if (hasEmptyItem) {
        valid = false;
    }

    return valid;
}

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
                btn.onmouseover = function() {
                    btn.style.background = "#c0392b";
                    btn.style.transform = "scale(1.1)";
                };
                btn.onmouseout = function() {
                    btn.style.background = "#e74c3c";
                    btn.style.transform = "scale(1)";
                };
                btn.onclick = function() {
                    removerItem(btn);
                };
                div.appendChild(btn);
            }
        } else {
            if (btn) btn.remove();
        }
    });
}

function removerItem(btn) {
    const itensDiv = document.getElementById("itens");
    if (itensDiv.children.length > 1) {
        btn.parentNode.remove();
        atualizarBotoesExcluir();
    }
}

function setError(input, message) {
    if (!input) return;
    input.style.border = '2px solid #e63636';

    let errorSpan = input.nextElementSibling;
    if (!errorSpan || !errorSpan.classList.contains("error-msg")) {
        errorSpan = document.createElement("span");
        errorSpan.classList.add("error-msg");
        input.insertAdjacentElement("afterend", errorSpan);
    }
    errorSpan.textContent = message;
}

function removeError(input) {
    if (!input) return;
    input.style.border = '';
    const errorSpan = input.nextElementSibling;
    if (errorSpan && errorSpan.classList.contains("error-msg")) {
        errorSpan.remove();
    }
}