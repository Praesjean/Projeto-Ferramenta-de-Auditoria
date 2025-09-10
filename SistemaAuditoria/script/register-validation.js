document.querySelectorAll('.required').forEach((input) => {
  input.addEventListener('input', () => {
    const type = input.dataset.type;
    const value = input.value.trim();

    if (value === "") {
      removeError(input);
      return;
    }

    switch (type.toLowerCase()) {
      case "nome":
        !inputWithoutNumbers(value) ? setError(input) : removeError(input);
        break;

      case "e-mail":
      case "email":
        !isEmail(value) ? setError(input) : removeError(input);
        break;

      case "senha":
        !validPassword(value) ? setError(input) : removeError(input);
        break;

      case "confirmar senha":
        const senhaInput = document.querySelector('[data-type="senha"]');
        value !== (senhaInput ? senhaInput.value : "") ? setError(input) : removeError(input);
        break;

      default:
        removeError(input);
        break;
    }
  });
});

// ------------------ FUNÇÃO ------------------
// FUNÇÃO  QUE VERIFICA SE OS CAMPOS ESTÃO VAZIS E EXIBE O ALERTA CASO ESTAJAM
function btnRegisterOnClick(event, formElement) {

  const inputs = formElement.querySelectorAll('.required');
  let hasError = false;

  for (let input of inputs) {
    const type = input.dataset.type.toLowerCase();
    const isRequired = input.dataset.required === "true";
    const value = input.value.trim();

    if (isRequired && value === "") {
      setError(input);
      errorAlert(`Preenchimento obrigatório: ${type}`, input);
      hasError = true;
      break;
    }
    if (type === 'nome' && !inputWithoutNumbers(value)) {
      setError(input);
      errorAlert("O nome não pode conter números ou caracteres especiais.", input);
      hasError = true;
      break;
    }
    if (type === 'email' && !isEmail(value)) {
      setError(input);
      errorAlert("Digite um e-mail válido.", input);
      hasError = true;
      break;
    }
    if (type === 'senha' && !validPassword(value)) {
      setError(input);
      errorAlert("A senha deve conter no mínimo 8 caracteres.", input);
      hasError = true;
      break;
    }
    if (type === 'confirmar senha') {
      const senhaInput = formElement.querySelector('[data-type="senha"]');
      if (value !== (senhaInput ? senhaInput.value : "")) {
        setError(input);
        errorAlert("As senhas não coincidem.", input);
        hasError = true;
        break;
      }
    }

  }

  if (hasError) {
    event.preventDefault();
  } else {
    formElement.submit();
    const submitBtn = formElement.querySelector('#submit');
    if (submitBtn) {
      submitBtn.disabled = true;
    }
  }
}

function setError(input) {
  input.style.border = '2px solid #e63636';
  const span = input.nextElementSibling;
  if (span && span.classList.contains('span-required')) {
    span.style.display = 'block';
  }
  input.focus();
}

function removeError(input) {
  input.style.border = '';
  const span = input.nextElementSibling;
  if (span && span.classList.contains('span-required')) {
    span.style.display = 'none';
  }
}

function errorAlert(message, input) {
  Swal.fire({
    title: 'Erro!',
    text: message,
    icon: 'error',
    confirmButtonText: 'Entendido',
    confirmButtonColor: '#929292ff',
    timer: 7000,
    timerProgressBar: true
  }).then(() => {
    setTimeout(() => {
      input.focus();
    }, 300);
  });
}

// ------------------ REGEX ------------------

function inputWithoutNumbers(value) {
  const re = /^[A-Za-zÀ-ÖØ-öø-ÿ\s]+$/;
  return re.test(value);
}

function isEmail(email) {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
  return re.test(email);
}

function validPassword(pass) {
  return pass.length >= 8;
}