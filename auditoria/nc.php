<?php include("conexao.php"); ?>
<!DOCTYPE html>
<html>
<head><title>Gerenciar NC</title></head>
<body>
<h2>Gerenciar Não Conformidade</h2>
<?php
$id = $_GET['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $responsavel = $_POST['responsavel'];
    $prazo = $_POST['prazo'];
    $status = $_POST['status'];
    $conn->query("UPDATE nao_conformidades 
                  SET responsavel='$responsavel', prazo='$prazo', status='$status' 
                  WHERE id=$id");
    echo "<p>NC atualizada com sucesso!</p>";
}

$nc = $conn->query("SELECT * FROM nao_conformidades WHERE id=$id")->fetch_assoc();
?>

<form method="post">
    <p><b>Descrição:</b> <?= $nc['descricao'] ?></p>
    Responsável: <input type="text" name="responsavel" value="<?= $nc['responsavel'] ?>"><br><br>
    Prazo: <input type="date" name="prazo" value="<?= $nc['prazo'] ?>"><br><br>
    Status: 
    <select name="status">
        <option <?= $nc['status']=="Aberta"?"selected":"" ?>>Aberta</option>
        <option <?= $nc['status']=="Em Andamento"?"selected":"" ?>>Em Andamento</option>
        <option <?= $nc['status']=="Resolvida"?"selected":"" ?>>Resolvida</option>
        <option <?= $nc['status']=="Escalonada"?"selected":"" ?>>Escalonada</option>
    </select><br><br>
    <button type="submit">Salvar</button>
</form>
</body>
</html>
