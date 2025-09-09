document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("form");
    const itensContainer = document.getElementById("itens");

    itensContainer.querySelectorAll(".remove-btn").forEach(btn => {
        btn.addEventListener("click", () => btn.parentElement.remove());
    });

    form.addEventListener("submit", (e) => {
        const titulo = form.querySelector("input[name='titulo']");
        const auditor = form.querySelector("input[name='auditor']");
        const itens = [...form.querySelectorAll("input[name='itens[]']")];

        if (titulo.value.trim() === "") {
            e.preventDefault();
            setError(titulo);
            errorAlert("O campo Título é obrigatório.", titulo);
            return;
        } else { removeError(titulo); }

        if (auditor.value.trim() === "") {
            e.preventDefault();
            setError(auditor);
            errorAlert("O campo Auditor é obrigatório.", auditor);
            return;
        } else { removeError(auditor); }

        const emptyItem = itens.find(i => i.value.trim() === "");
        if (emptyItem) {
            e.preventDefault();
            setError(emptyItem);
            errorAlert("Preencha todos os itens do checklist.", emptyItem);
            return;
        } else { itens.forEach(i => removeError(i)); }
    });
});

function setError(input) {
    input.style.border = '2px solid #e63636';
    input.focus();
}

function removeError(input) {
    input.style.border = '';
}

function errorAlert(message, input) {
    Swal.fire({
        title: 'Erro!',
        text: message,
        icon: 'error',
        confirmButtonText: 'Entendido',
        confirmButtonColor: '#081369ff',
        timer: 5000,
        timerProgressBar: true
    }).then(() => { input.focus(); });
}

function showSuccess(message, redirectUrl = null) {
    Swal.fire({
        title: 'Sucesso!',
        text: message,
        icon: 'success',
        confirmButtonColor: '#081369ff',
        confirmButtonText: 'Ok'
    }).then(() => {
        if (redirectUrl) window.location.href = redirectUrl;
    });
}

function showError(message) {
    Swal.fire({
        title: 'Erro!',
        text: message,
        icon: 'error',
        confirmButtonColor: '#081369ff',
        confirmButtonText: 'Entendido'
    });
}

function adicionarItem() {
    const div = document.createElement("div");
    div.classList.add("checklist-item");
    div.innerHTML = `
        <input type="text" name="itens[]" placeholder="Descrição do item">
        <button type="button" class="remove-btn">❌</button>
    `;
    document.getElementById("itens").appendChild(div);

    div.querySelector(".remove-btn").addEventListener("click", () => div.remove());
}