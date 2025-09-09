function showSuccess(message, redirectUrl) {
  Swal.fire({
    icon: 'success',
    title: 'Sucesso',
    text: message,
    allowOutsideClick: false,
    allowEscapeKey: false,
    backdrop: true
  }).then(() => {
    if (redirectUrl) {
      window.location.href = redirectUrl;
    }
  });
}

function showError(message) {
  Swal.fire({
    icon: 'error',
    title: 'Erro',
    text: message,
    allowOutsideClick: false,
    allowEscapeKey: false,
    backdrop: true
  });
}
