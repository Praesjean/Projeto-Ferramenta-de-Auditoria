<?php
session_start();
include('../../conecta_db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);

    if (!empty($email) && !empty($senha)) {
        $sql = "SELECT id, nome, senha FROM usuarios WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 1) {
            $usuario = $resultado->fetch_assoc();

            if (password_verify($senha, $usuario['senha'])) {
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];

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
    <title>Login</title>
    <style>
       body {
            font-family: Arial, sans-serif;
            background: #eef2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            background: white;
            padding: 20px 30px;
            border-radius: 8px;
            box-shadow: 0px 2px 8px rgba(0,0,0,0.2);
            width: 450px;
            box-sizing: border-box;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        form {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        label {
            align-self: flex-start;
            margin: 8px 0 4px 0;
        }
        input[type=text],
        input[type=password],
        .login-btn {
            width: 100%;
            max-width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 14px;
        }
        .login-btn {
            background: #28a745;
            border: none;
            color: white;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s;
        }
        .login-btn:hover {
            background: #218838;
        }
        p {
            text-align:center;
            margin-top:10px;
        }
        p a {
            color: #0077cc;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s;
        }
        p a:hover {
            color: #005fa3;
            text-decoration: underline;
        }
        .span-required {
            display: none;
            font-size: 12px;
            color: #e63636;
            margin-top: 2px;
            text-align: left;
            width: 100%;
        }
    </style>
</head>
<body>
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

        <p>Não tem conta? <a href="cadastro.php">Cadastrar</a></p>
    </div>
</body>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../../script/alert.js"></script>
    <script src="../../script/register-validation.js"></script>

</html>