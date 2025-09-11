document.querySelectorAll('.excluir-btn').forEach(button => {
    button.addEventListener('click', function() {
        const auditoriaId = this.getAttribute('data-id');

        Swal.fire({
            title: 'Tem certeza?',
            text: "Esta auditoria será excluída permanentemente!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74c3c',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sim, excluir!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'excluir_auditoria.php?id=' + auditoriaId;
            }
        });
    });
});