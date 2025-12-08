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

    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-white border-r p-4">
            <h3 class="text-lg font-semibold mb-4">Navegação</h3>
            <nav class="space-y-2">
                <?php if ($userType === 'Administrador'): ?>
                    <a href="registerChampionship.php" class="block text-gray-700 hover:text-blue-600">📌 Cadastrar campeonato</a>
                    <a href="register.php" class="block text-gray-700 hover:text-blue-600">👥 Cadastrar usuários</a>
                    <a href="registerTeam.php" class="block text-gray-700 hover:text-blue-600">🏷️ Cadastrar time</a>
                    <a href="associateTeamChampionship.php" class="block text-gray-700 hover:text-blue-600">🔗 Associar time/campeonato</a>
                    <a href="associateTrainerTeam.php" class="block text-gray-700 hover:text-blue-600">🧑‍🏫 Associar treinador/time</a>
                    <a href="manageChampionships.php" class="block text-gray-700 hover:text-blue-600">🏆 Ver campeonatos</a>
                <?php elseif ($userType === 'Treinador'): ?>
                    <a href="trainner.php" class="block text-gray-700 hover:text-blue-600">➕ Contratar jogador</a>
                    <a href="CadastroJogador.php" class="block text-gray-700 hover:text-blue-600">🏆 Ver campeonatos</a>
                <?php else: ?>
                    <a href="ListarJogadores.php" class="block text-gray-700 hover:text-blue-600">🎮 Meus jogos</a>
                <?php endif; ?>
            </nav>
        </aside>

        <main class="flex-1 p-4 space-y-4">
            <h1 class="text-2xl font-bold">Menu Principal de <?php echo $userType; ?></h1>
            <h2 class="text-xl">Opções Disponíveis:</h2>
            <?php
            echo '<ul class="grid grid-cols-3 gap-4">';

            switch ($userType) {
                case 'Administrador':
                    echo '
                
                <a href="manageChampionships.php"><li class="p-4 border border-gray-300 rounded hover:bg-gray-100 text-center hover:cursor-pointer"><i class="fas fa-eye"></i> Ver jogos de campeonato</li></a>
                <a href="registerChampionship.php"><li class="p-4 border border-gray-300 rounded hover:bg-gray-100 text-center hover:cursor-pointer"><i class="fas fa-plus-circle"></i> Cadastrar campeonato</li></a>
                <a href="register.php"><li class="p-4 border border-gray-300 rounded hover:bg-gray-100 text-center hover:cursor-pointer"><i class="fas fa-user"></i> Cadastrar Usuarios</li></a>
                <a href="registerTeam.php"><li class="p-4 border border-gray-300 rounded hover:bg-gray-100 text-center hover:cursor-pointer"><i class="fas fa-user"></i> Cadastrar Time</li></a>
                <a href="associateTeamChampionship.php"><li class="p-4 border border-gray-300 rounded hover:bg-gray-100 text-center hover:cursor-pointer"><i class="fas fa-user"></i> Associar Time Campeonato</li></a>
                <a href="associateTrainerTeam.php"><li class="p-4 border border-gray-300 rounded hover:bg-gray-100 text-center hover:cursor-pointer"><i class="fas fa-trophy"></i> Associar Treinador Time</li></a>';
                    break;
                case 'Treinador':
                    echo '
                        <a href="CadastroJogador.php"><li class="p-4 border border-gray-300 rounded hover:bg-gray-100 text-center hover:cursor-pointer"><i class="fas fa-trophy"></i> Ver campeonatos</li></a>
                        <a href="trainner.php"><li class="p-4 border border-gray-300 rounded hover:bg-gray-100 text-center hover:cursor-pointer"><i class="fas fa-user-plus"></i> Contratar Jogador</li></a>';
                    break;
                case 'Jogador':
                    echo '<a href="ListarJogadores.php"><li class="p-4 border border-gray-300 rounded hover:bg-gray-100 text-center hover:cursor-pointer"><i class="fas fa-gamepad"></i> Ver meus jogos</li></a>';
                    break;
            }

            echo '</ul>';
            ?>
    </div>
</body>

</html>