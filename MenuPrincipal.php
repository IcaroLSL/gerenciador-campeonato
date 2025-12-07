<?php
if (!isset($_COOKIE['credentials']) || $_COOKIE['credentials'] == false) {
    header("Location: login.php");
    exit;
}

if (isset($_POST['deslogar'])) {
    setcookie("credentials", false, time() + (86400 * 30));
    header("Location: login.php");
    exit;
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
        exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Principal</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>

<body>
    <nav class="flex flex-row justify-between items-center p-4 bg-gray-200">
        <div>
            Fatec Campeonatos
        </div>

        <div class="flex flex-row items-center space-x-4">
            <h3>
                <?php echo $cookieData['name']; ?> (<?php echo $userType; ?>)
            </h3>

            <form action="" method="post">
                <button class="bg-red-500 text-white py-2 px-4 rounded cursor-pointer" type="submit" name="deslogar">Deslogar</button>
            </form>
        </div>

    </nav>
    <div class="p-4 space-y-4">
        <h1 class="text-2xl font-bold">Menu Principal de <?php echo $userType; ?></h1>
        <h2 class="text-xl">Opções Disponíveis:</h2>
        <?php
        echo '<ul class="grid grid-cols-3 gap-4">';

        switch ($userType) {
            case 'Administrador':
                echo '
                <li class="p-4 border border-gray-300 rounded hover:bg-gray-100 text-center hover:cursor-pointer"><a href="#"><i class="fas fa-eye"></i> Ver jogos de campeonato</a></li>
                <li class="p-4 border border-gray-300 rounded hover:bg-gray-100 text-center hover:cursor-pointer"><a href="registerChampionship.php"><i class="fas fa-plus-circle"></i> Cadastrar campeonato</a></li>
                <li class="p-4 border border-gray-300 rounded hover:bg-gray-100 text-center hover:cursor-pointer"><a href="register.php"><i class="fas fa-user"></i> Cadastrar Usuarios</a></li>
                <li class="p-4 border border-gray-300 rounded hover:bg-gray-100 text-center hover:cursor-pointer"><a href="registerTeam.php"><i class="fas fa-user"></i> Cadastrar Time</a></li>';
                break;
            case 'Treinador':
                echo '
                <li class="p-4 border border-gray-300 rounded hover:bg-gray-100 text-center hover:cursor-pointer"><a href="CadastroJogador.php"><i class="fas fa-trophy"></i> Ver campeonatos</a></li>
                <li class="p-4 border border-gray-300 rounded hover:bg-gray-100 text-center hover:cursor-pointer"><a href="trainner.php"><i class="fas fa-user-plus"></i> Contratar Jogador</a></li>';
                break;
            case 'Jogador':
                echo '<li class="p-4 border border-gray-300 rounded hover:bg-gray-100 text-center hover:cursor-pointer"><a href="ListarJogadores.php"><i class="fas fa-gamepad"></i> Ver meus jogos</a></li>';
                break;
        }

        echo '</ul>';
        ?>
    </div>
</body>

</html>