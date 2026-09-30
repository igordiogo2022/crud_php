<?php
require "conexao.php";

// Verifica se há algum id para editar
$idEditando = $_GET["editar"] ?? null;
$funcionarioEditando = null;

// Verifica se há algum id para deletar
$idDeletando = $_GET["deletar"] ?? null;

// Busca todos os funcionários no banco
$comandoQuery = $pdo->query("SELECT * FROM funcionarios");
$funcionarios = $comandoQuery->fetchAll(PDO::FETCH_ASSOC);

// Se há id para editar, busca o usuario pelo id
if($idEditando!=null){
    $comando = $pdo->prepare("
        SELECT * FROM funcionarios
        WHERE id = ?
    ");

    $comando->execute([$idEditando]);

    $funcionarioEditando = $comando->fetch(PDO::FETCH_ASSOC);
}

// Se há id para deletar, busca o usuário pelo id
if($idDeletando!=null){
    $comando = $pdo->prepare("
        DELETE FROM funcionarios 
        WHERE id = ?
    ");

    $comando->execute([$idDeletando]);
    
    // reload na página
    header("Location: index.php");
    exit;
}

// CREATE / UPDATE
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Pega dados do formulário
    $id = $_POST["id"] ?? null;
    $nome = $_POST["nome"];
    $cargo = $_POST["cargo"];
    $salario = $_POST["salario"];
    $status = $_POST["status"];
    
    if($id==null || $id==''){     
        $comando = $pdo->prepare("
            INSERT INTO funcionarios (nome, cargo, salario, status)
            VALUES (?, ?, ?, ?)
        ");

        // adiciona os dados no banco
        $comando->execute([
            $nome, 
            $cargo, 
            $salario, 
            $status
        ]);
    }else{
        $comando = $pdo->prepare("
            UPDATE funcionarios
            SET nome=?, cargo=?, salario=?, status=?
            WHERE id=?
        ");

        $comando->execute([
            $nome, 
            $cargo, 
            $salario,
            $status, 
            $id
        ]);

    }

    // reload na página
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">

    <title>Gerenciamento de Funcionários</title>
</head>

<body>

    <header>

        <h1>Funcionários</h1>
        <p>
            Gerenciamento de funcionários
        </p>

    </header>

    <main>
        <!-- =========================
             CADASTRO
        ========================== -->

        <section class="form-container">

            <h2>Cadastrar funcionário</h2>

            <form action="" method="POST">
                <div class="form-grid">

                    <input type="hidden" name="id" value="<?= $funcionarioEditando["id"] ?? "" ?>">

                    <div class="campo">
                        <label for="nome">Nome</label>

                        <input type="text" id="nome" name="nome" placeholder="Digite o nome" value="<?= $funcionarioEditando["nome"] ?? "" ?>" required>
                    </div>

                    <div class="campo">
                        <label for="cargo">Cargo</label>

                        <select id="cargo" name="cargo" required>
                            <option value="">Selecione um cargo</option>
                            <option value="Dono" <?= ($funcionarioEditando["cargo"] ?? "") == "Dono" ? "selected" : "" ?>>Dono</option>
                            <option value="Presidente" <?= ($funcionarioEditando["cargo"] ?? "") == "Presidente" ? "selected" : "" ?>>Presidente</option>
                            <option value="Administrador" <?= ($funcionarioEditando["cargo"] ?? "") == "Administrador" ? "selected" : "" ?>>Administrador</option>
                            <option value="Moderador" <?= ($funcionarioEditando["cargo"] ?? "") == "Moderador" ? "selected" : "" ?>>Moderador</option>
                            <option value="Funcionário" <?= ($funcionarioEditando["cargo"] ?? "") == "Funcionário" ? "selected" : "" ?>>Funcionário</option>
                        </select>
                    </div>

                    <div class="campo">
                        <label for="salario">Salário</label>

                        <input type="number" id="salario" name="salario" placeholder="5000" min="0" step="0.01" value="<?= $funcionarioEditando["salario"] ?? "" ?>" required>
                    </div>

                    <div class="campo">
                        <label for="status">Status</label>

                        <select id="status" name="status" required>
                            <option value="ativo" <?= ($funcionarioEditando["status"] ?? "") == "ativo" ? "selected" : "" ?>>Ativo</option>
                            <option value="inativo" <?= ($funcionarioEditando["status"] ?? "") == "inativo" ? "selected" : "" ?>>Inativo</option>
                        </select>
                    </div>

                </div>

                <div class="form-buttons">
                    <button type="submit" class="btn-primary">
                        <?= $funcionarioEditando != null ? "Editar funcionário" : "Cadastrar funcionário" ?>
                    </button>

                    <button type="reset" class="btn-secondary">
                        Limpar
                    </button>
                </div>
            </form>

        </section>



        <!-- =========================
             LISTAGEM
        ========================== -->

        <section class="lista-container">

            <div class="lista-header">

                <h2>Funcionários cadastrados</h2>

                <input
                    type="search"
                    class="busca"
                    placeholder="Buscar funcionário..."
                >

            </div>


            <div class="tabela-wrapper">

                <table>

                    <thead>

                        <tr>

                            <th>
                                ID
                            </th>

                            <th>
                                Nome
                            </th>

                            <th>
                                Cargo
                            </th>

                            <th>
                                Salário
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Ações
                            </th>

                        </tr>

                    </thead>


                    <tbody>
                        <?php
                        foreach ($funcionarios as $funcionario):?>
                            <tr>
                                <td><?= $funcionario["id"] ?></td>
                                <td><?= $funcionario["nome"] ?></td>
                                <td><?= $funcionario["cargo"] ?></td>
                                <td>R$ <?= number_format($funcionario["salario"], 2, ',', '.') ?></td>
                                <td>
                                <span class="status <?= $funcionario["status"] ?>"><?= ucfirst($funcionario["status"]) ?></span>
                                </td>
                                <td>
                                    <div class="acoes">
                                        <a class="btn-editar" type="button" href="index.php?editar=<?= $funcionario["id"] ?>">Editar</a>
                                        <a class="btn-excluir" type="button" href="index.php?deletar=<?= $funcionario["id"] ?>">Excluir</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </section>

    </main>

</body>

</html>