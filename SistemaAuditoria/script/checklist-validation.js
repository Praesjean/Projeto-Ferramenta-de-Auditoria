document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("form");
    const itensContainer = document.getElementById("itens");

    itensContainer.querySelectorAll(".remove-btn").forEach(btn => {
        btn.addEventListener("click", () => btn.parentElement.remove());
    });

    form.addEventListener("submit", (e) => {
        let valid = true;

        const titulo = form.querySelector("input[name='titulo']");
        const auditor = form.querySelector("input[name='auditor']");
        const autor = form.querySelector("input[name='autor']");
        const descricao = form.querySelector("textarea[name='descricao']");
        const itens = [...form.querySelectorAll("input[name='itens[]']")];

        if (titulo.value.trim() === "") {
            e.preventDefault();
            setError(titulo, "O campo Título é obrigatório.");
            valid = false;
        } else {
            removeError(titulo);
        }

        if (auditor.value.trim() === "") {
            e.preventDefault();
            setError(auditor, "O campo Auditor é obrigatório.");
            valid = false;
        } else {
            removeError(auditor);
        }

        if (autor.value.trim() === "") {
            e.preventDefault();
            setError(autor, "O campo Autor do Documento é obrigatório.");
            valid = false;
        } else {
            removeError(autor);
        }

        if (descricao.value.trim() === "") {
            e.preventDefault();
            setError(descricao, "O campo Descrição é obrigatório.");
            valid = false;
        } else {
            removeError(descricao);
        }

        let hasEmptyItem = false;
        itens.forEach((i) => {
            if (i.value.trim() === "") {
                setError(i, "Preencha todos os itens do checklist.");
                hasEmptyItem = true;
            } else {
                removeError(i);
            }
        });

        if (hasEmptyItem) {
            e.preventDefault();
            valid = false;
        }

        return valid;
    });
});

function setError(input, message) {
    input.style.border = '2px solid #e63636';
    input.focus();

    let errorSpan = input.nextElementSibling;
    if (!errorSpan || !errorSpan.classList.contains("error-msg")) {
        errorSpan = document.createElement("span");
        errorSpan.classList.add("error-msg");
        input.insertAdjacentElement("afterend", errorSpan);
    }
    errorSpan.textContent = message;
}

function removeError(input) {
    input.style.border = '';
    const errorSpan = input.nextElementSibling;
    if (errorSpan && errorSpan.classList.contains("error-msg")) {
        errorSpan.textContent = "";
    }
}