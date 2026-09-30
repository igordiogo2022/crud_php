<?php 
$host = "localhost";
$banco = "empresa_crud";
$usuario = "SEU_USUARIO";
$senha = "SUA_SENHA";

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$banco;charset=utf8mb4",
        $usuario,
        $senha
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e){
    die("Erro na conexão: " . $e->getMessage());
}

?>
