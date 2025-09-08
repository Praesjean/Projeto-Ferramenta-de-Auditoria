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
            $_SESSION['error_message'] = "Este e-mail já está cadastrado.";
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
            $_SESSION['success_message'] = "Usuário cadastrado com sucesso!";
            $stmt->close();
            header("Location: dashboard.php");
            exit;
        } else {
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
    <title>Cadastro de Usuário</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
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
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 2px 8px rgba(0,0,0,0.2);
            width: 450px;
            box-sizing: border-box;
    
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        form{
            width:100%;
        }
        label{
            margin: 50px auto;
        }
        input[type=text],
        input[type=email],
        input[type=password],
        .register-btn {
            display: block;
            width: 100%;
            padding: 10px;
            margin: 6px auto;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        .register-btn {
            background: #0077cc;
            border: none;
            color: white;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s;
        }
        .register-btn:hover {
            background: #005fa3;
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
            width: 90%;
        }
    </style>

</head>
<body>
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
            <span class="span-required">Sua senha deve conter no mínimo 8 caracteres, combinando letras maiúsculas, minúsculas, números e símbolos especiais.</span>

            <label for="confirm-pass"><b>Confirme sua senha: *</b></label>
            <input type="password" name="confirmar_senha" id="confirm-pass" class="full-inputUser required" data-type="confirmar senha" data-required="true" placeholder="Repita a senha">
            <span class="span-required">As senhas não coincidem.</span>
        
            <input type="submit" value="Cadastrar-se" id="submit" class="register-btn" onclick="btnRegisterOnClick(event, this.form)">
        </form>

        <p>Já tem conta? <a href="login.php">Login</a></p>
    </div>
</body>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../../script/alert.js"></script>
    <script src="../../script/register-validation.js"></script>

</html>
