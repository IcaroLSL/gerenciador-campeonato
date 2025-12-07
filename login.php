<?php
    require_once("conecta.php");
    require_once("function.php");
    if (isset($_COOKIE['credentials'])) {
        if ($_COOKIE['credentials'] != false) {
            header("Location: MenuPrincipal.php");
        }
    }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <form action="" method="post">
        <label for="username">Usuário:</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="password">Senha:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <button type="submit" name="logar">Entrar</button>
    </form>
    <?php
        if (isset($_POST['logar'])) {
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $username = clearDataReceived($_POST['username']);
                $password = hash("sha256", clearDataReceived($_POST['password']));
                
                $query = "SELECT id_usuario, cargo, name FROM tbl_usuarios WHERE username = ? AND password = ?";
                $stmt = mysqli_stmt_init($connection);
                if (!(mysqli_stmt_prepare($stmt, $query))) {
                    echo "Erro na preparação da declaração: " . mysqli_stmt_error($stmt);
                }
                mysqli_stmt_bind_param($stmt, "ss", $username, $password);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                
                if (mysqli_num_rows($result) > 0) {
                    $row = mysqli_fetch_assoc($result);
                    echo "<p>Login bem-sucedido! Bem-vindo, $row[name].</p>";
                    $cookieData = array(
                        "id" => $row['id_usuario'],
                        "cargo" => $row['cargo'],
                        "name" => $row['name'],
                        "username" => $username
                    );
                    setcookie("credentials", json_encode($cookieData), time() + (86400 * 30));
                    header("Location: MenuPrincipal.php");
                } else {
                    setcookie("credentials", false, time() + (86400 * 30));
                    echo "<p>Usuário ou senha incorretos. Tente novamente.</p>";
                }
                mysqli_stmt_close($stmt);
            }
        }
    ?>
</body>
</html>