<?php
include("conexao.php");

$hoje = date("Y-m-d");

$result = $conn->query("SELECT id, descricao, prazo FROM nao_conformidades
                        WHERE prazo < '$hoje' 
                        AND status NOT IN ('Resolvida','Escalonada')");

while ($nc = $result->fetch_assoc()) {
    $id = $nc['id'];
    $descricao = $nc['descricao'];
    $conn->query("UPDATE nao_conformidades SET status='Escalonada' WHERE id=$id");

    echo "NC #$id escalonada.<br>";
}

echo "<br>Processo concluído em ".date("d/m/Y H:i:s");
