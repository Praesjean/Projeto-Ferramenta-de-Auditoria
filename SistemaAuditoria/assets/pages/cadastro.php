<?php 
session_start();
include('../../conecta_db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);
    $confirmar_senha = trim($_POST['confirmar_senha']);

    if (!empty($nome) && !empty($email) && !empty($senha) && !empty($confirmar_senha)) {

        if ($senha !== $confirmar_senha) {
            $_SESSION['error_message'] = "As senhas não coincidem.";
            header("Location: cadastro.php");
            exit;
        }

        $sql_check = "SELECT id FROM usuarios WHERE email = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $_SESSION['error_message'] = "Usuário já cadastrado.";
            $stmt_check->close();
            header("Location: cadastro.php");
            exit;
        }

        $stmt_check->close();

        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        $sql = "INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $nome, $email, $senhaHash);

        if ($stmt->execute()) {
            $novo_id = $stmt->insert_id;

            $_SESSION['usuario_id'] = $novo_id;
            $_SESSION['usuario_nome'] = $nome;
            $_SESSION['usuario_email'] = $email;

            $_SESSION['success_message'] = "Usuário cadastrado com sucesso!";
            $stmt->close();

            header("Location: cadastro.php");
            exit;
        }else {
            $_SESSION['error_message'] = "Erro ao cadastrar: " . $stmt->error;
            $stmt->close();
            header("Location: cadastro.php");
            exit;
        }

    } else {
        $_SESSION['error_message'] = "Preencha todos os campos.";
        header("Location: cadastro.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Auditoria | Cadastro de Usuário</title>
    <link href="../../styles/pages/cadastro/cadastro.css" rel="stylesheet">
</head>
<body>
    <header>
        <h1>Sistema de Auditoria</h1>
    </header>
    
    <main>
        <div class="container">
            <h2>Cadastrar Usuário</h2>

            <form id="form" name="form" method="POST" action="cadastro.php">
                <label for="name"><b>Nome: *</b></label>
                <input type="text" id="name" name="nome" class="full-inputUser required" data-type="nome" data-required="true" placeholder="Insira seu nome completo">
                <span class="span-required">Nome não pode conter números e caracteres especiais.</span>

                <label for="text"><b>E-mail: *</b></label>
                <input type="text" id="email" name="email" class="full-inputUser required" data-type="email" data-required="true" placeholder="exemplo@gmail.com">
                <span class="span-required">Insira um e-mail válido!</span>

                <label for="password"><b>Senha: *</b></label>
                <input type="password" name="senha" id="password" class="full-inputUser required" data-type="senha" data-required="true" placeholder="Crie uma senha">
                <span class="span-required">Sua senha deve conter no mínimo 8 caracteres.</span>

                <label for="confirm-pass"><b>Confirme sua senha: *</b></label>
                <input type="password" name="confirmar_senha" id="confirm-pass" class="full-inputUser required" data-type="confirmar senha" data-required="true" placeholder="Repita a senha">
                <span class="span-required">As senhas não coincidem.</span>
            
                <input type="submit" value="Cadastrar-se" id="submit" class="register-btn" onclick="btnRegisterOnClick(event, this.form)">
            </form>

            <p>Já tem conta? <a href="login.php">Login</a></p>
        </div>
    </main>

    <footer>
        &copy; <?php echo date('Y'); ?> Sistema de Auditoria. Todos os direitos reservados.
        <br>Desenvolvido por: Arthur Rodrigues, Jean Inácio, João Gabriel e Stefany Carlos.
    </footer>
</body>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../../script/alert.js"></script>
    <script src="../../script/register-validation.js"></script>

    <?php if (isset($_SESSION['success_message'])): ?>
        <script>
            showSuccess("<?php echo $_SESSION['success_message']; ?>", "dashboard.php");
        </script>
    <?php unset($_SESSION['success_message']); endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <script>
            showError("<?php echo $_SESSION['error_message']; ?>");
        </script>
    <?php unset($_SESSION['error_message']); endif; ?>

</html>