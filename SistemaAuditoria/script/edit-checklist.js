function adicionarItem() {
    let div = document.createElement("div");
    div.innerHTML = '<input type="text" name="itens[]" placeholder="Descrição do item" required>';
    document.getElementById("itens").appendChild(div);
}

document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        e.preventDefault();

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
                let formData = new FormData(form);

                fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Checklist atualizado!',
                        text: 'Suas alterações foram salvas com sucesso!',
                        confirmButtonColor: '#28a745'
                    });
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro!',
                        text: 'Não foi possível salvar as alterações.',
                        confirmButtonColor: '#d33'
                    });
                });
            }
        });
    });
});