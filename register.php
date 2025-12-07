<?php
    require_once("conecta.php");
    require_once("function.php");
    if (isset($_COOKIE['credentials'])) {
        if ($_COOKIE['credentials'] != false) {
            $credentials = json_decode($_COOKIE['credentials'], true);
            if (isset($credentials['id']) && isset($credentials['cargo']) && isset($credentials['name']) && isset($credentials['username'])) {
                if ($credentials['cargo'] != 2) {
                    header("Location: MenuPrincipal.php");
                }
            } else {
                header("Location: MenuPrincipal.php");
            }
        } else {
            header("Location: MenuPrincipal.php");
        }
    }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de usuarios</title>
</head>
<body>
    <h1>Registro de usuarios</h1>
    <form action="" method="post">
        <label for="name">Nome: </label>
        <input type="text" id="name" name="name" required>
        <br>
        <label for="username">Usuário: </label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="password">Senha: </label>
        <input type="password" id="password" name="password" required>
        <br>
        <label for="position">Cargo (0-User, 1-Trainer, 2-Admin): </label>
        <select name="position" required>
            <option value="">Selecione...</option>
            <option value=0>Usuário</option>
            <option value=1>Treinador</option>
            <option value=2>Administrador</option>
        </select>
        <br>
        <label for="email">E-mail: </label>
        <input type="text" name="email" id="email">
        <br>
        <button type="submit" name="register">Registrar</button>    
        <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (isset($_POST['register'])) {
                    $username = clearDataReceived($_POST['username']);
                    $password = hash("sha256", clearDataReceived($_POST['password']));
                    $cargo    = clearDataReceived($_POST['position']);
                    $name     = clearDataReceived($_POST['name']);
                    $email    = clearDataReceived($_POST['email']);

                    $query = "INSERT INTO tbl_usuarios (username, password, cargo, name, email) VALUES (?, ?, ?, ?, ?)";
                    $stmt = mysqli_stmt_init($connection);
                    if (!(mysqli_stmt_prepare($stmt, $query))) {
                        echo "Erro na preparação da declaração: " . mysqli_stmt_error($stmt);
                    }
                    mysqli_stmt_bind_param($stmt, "ssiss", $username, $password, $cargo, $name, $email);
                    if (mysqli_stmt_execute($stmt)) {
                        echo "<h1>Usuário registrado com sucesso!</h1>";
                    } else {
                        echo "<p>Erro ao registrar o usuário: " . mysqli_stmt_error($stmt) . "</p>";
                    }
                    mysqli_stmt_close($stmt);
                }
            }
        ?>
</body>
</html>