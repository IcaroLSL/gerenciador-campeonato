<?php
if (!isset($_COOKIE['credentials']) || $_COOKIE['credentials'] == false) {
    header("Location: login.php");
}

if (isset($_POST['deslogar'])) {
    setcookie("credentials", false, time() + (86400 * 30));
    header("Location: login.php");
}

if (isset($_COOKIE['credentials']) && $_COOKIE['credentials'] != false) {
    $cookieData = json_decode($_COOKIE['credentials'], true);
}

switch ($cookieData['cargo']) {
    case 0:
        $userType = "Usuário";
        break;
    case 1:
        $userType = "Treinador";
        break;
    case 2:
        $userType = "Administrador";
        break;
    default:
        header("Location: login.php");
        break;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>

    <div>
        <h1>Menu Principal: <?php echo $userType; ?></h1>
        <form action="" method="post">
            <button type="submit" name="deslogar">Deslogar</button>
        </form>
        <h2>Opções Disponíveis:</h2>
        <?php
        echo '<ul>';

        switch ($userType) {

            case 'Administrador':
                
                echo '
                <li><a href="CadastroJogador.php">Ver jogos de campeonato</a></li>
                <li><a href="CadastroJogador.php">Cadastrar campeonato</a></li>
                <li><a href="CadastroUsuario.php">Cadastrar Time</a></li>
                <li><a href="CadastroUsuario.php">Cadastrar Treinadores</a></li>
                <li><a href="CadastroUsuario.php">Cadastrar Jogadores</a></li>';
                break;
            case 'Treinador':
                echo '
                <li><a href="CadastroJogador.php">Ver campeonatos</a></li>
                <li><a href="CadastroTime.php">Contratar Jogador</a></li>';
                break;
            case 'Jogador':
                echo '<li><a href="ListarJogadores.php">Ver meus jogos</a></li>';
                break;
        }

        echo '</ul>';

        ?>
    </div>

</body>

</html>