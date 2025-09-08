function showSuccess(message, redirectUrl = null) {
  Swal.fire({
    title: 'Sucesso!',
    text: message,
    icon: 'success',
    confirmButtonColor: '#081369ff',
    confirmButtonText: 'Ok'
  }).then(() => {
    if (redirectUrl) {
      window.location.href = redirectUrl;
    }
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