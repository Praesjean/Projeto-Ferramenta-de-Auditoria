<?php
session_start();
include('../../conecta_db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);

    if (!empty($email) && !empty($senha)) {
        $sql = "SELECT id, nome, email, senha FROM usuarios WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 1) {
            $usuario = $resultado->fetch_assoc();

            if (password_verify($senha, $usuario['senha'])) {
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                $_SESSION['usuario_email'] = $usuario['email'];

                $_SESSION['success_message'] = "Login realizado com sucesso!";
                header("Location: dashboard.php");
                exit;
            } else {
                $_SESSION['error_message'] = "E-mail e/ou senha incorreta.";
            }
        } else {
            $_SESSION['error_message'] = "Usuário não encontrado.";
        }

        $stmt->close();
    } else {
        $_SESSION['error_message'] = "Preencha todos os campos.";
    }
}

if (isset($_SESSION['error_message'])) {
    $error = addslashes($_SESSION['error_message']);
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            showError('{$error}');
        });
    </script>";
    unset($_SESSION['error_message']);
}

if (isset($_SESSION['success_message'])) {
    $success = addslashes($_SESSION['success_message']);
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            showSuccess('{$success}');
        });
    </script>";
    unset($_SESSION['success_message']);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Auditoria | Login</title>
    <link href="../../styles/pages/login/login.css" rel="stylesheet">
</head>
<body>
    <header>
        <div class="header">
            <h1>Sistema de Auditoria</h1>
        </div>
    </header>

    <div class="main">
        <div class="container">
            <h2>Login</h2>
            <form method="POST" action="">
                <label for="text">Email:</label>
                <input type="text" id="email" name="email" class="required" data-type="email" data-required="true" placeholder="exemplo@gmail.com">
                <span class="span-required">Insira um e-mail válido!</span>
                
                <label for="password">Senha:</label>
                <input type="password" name="senha" id="password" class="required" data-type="senha" data-required="true" placeholder="Digite sua senha">

                <input type="submit" value="Entrar"  id="submit" class="login-btn" onclick="btnRegisterOnClick(event, this.form)">
            </form>

            <p>Não tem uma conta? <a href="cadastro.php">Cadastrar</a></p>
        </div>
    </div>

    <footer>
        &copy; <?php echo date('Y'); ?> Sistema de Auditoria. Todos os direitos reservados.
        <br>
        Desenvolvido por: Arthur Rodrigues, Jean Inácio, João Gabriel e Stefany Carlos.
    </footer>
</body>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../../script/alert.js"></script>
<script src="../../script/register-validation.js"></script>

</html>