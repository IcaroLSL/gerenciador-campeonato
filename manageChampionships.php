<?php
include 'conecta.php';
include 'function.php';

$SelectCampeonato = "SELECT * FROM tbl_campeonatos";
$resultCampeonato = mysqli_query($connection, $SelectCampeonato);
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
    <title>Associação de Treinadores</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>

<body class="bg-gray-100 min-h-screen">
    <!-- Barra de navegação -->
    <nav class="flex flex-row justify-between items-center p-4 bg-gray-200 shadow-md mb-8">
        <div class="flex items-center gap-4">
            <a href="MenuPrincipal.php" class="text-xl font-bold text-gray-800 hover:text-blue-500 transition">
                <i class="fas fa-trophy"></i> Fatec Campeonatos
            </a>
            <span class="text-gray-400">|</span>
            <span class="text-gray-600">
                <i class="fas fa-user-plus"></i> Associação de Treinadores a Times
            </span>
        </div>

        <div class="flex flex-row items-center space-x-4">
            <?php if (isset($credentials)): ?>
                <h3 class="text-gray-700">
                    <i class="fas fa-user-shield"></i> <?php echo htmlspecialchars($credentials['name'], ENT_QUOTES, 'UTF-8'); ?> (Administrador)
                </h3>
            <?php endif; ?>
            <a href="MenuPrincipal.php" class="bg-gray-500 text-white py-2 px-4 rounded hover:bg-gray-600 transition">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </nav>

    <div class="flex gap-6 p-4">
        <!-- Sidebar -->
        <aside class="w-64 bg-white border-r p-4">
            <h3 class="text-lg font-semibold mb-4">Navegação</h3>
            <nav class="space-y-2">
                <a href="registerChampionship.php" class="block text-gray-700 hover:text-blue-600">📌 Cadastrar campeonato</a>
                <a href="register.php" class="block text-gray-700 hover:text-blue-600">👥 Cadastrar usuários</a>
                <a href="registerTeam.php" class="block text-gray-700 hover:text-blue-600">🏷️ Cadastrar time</a>
                <a href="associateTeamChampionship.php" class="block text-gray-700 hover:text-blue-600">🔗 Associar time/campeonato</a>
                <a href="associateTrainerTeam.php" class="block text-gray-700 hover:text-blue-600">🧑‍🏫 Associar treinador/time</a>
                <a href="manageChampionships.php" class="block text-gray-700 hover:text-blue-600">🏆 Ver campeonatos</a>
            </nav>
        </aside>


        <div>
            <h1 class="text-2xl font-bold">Gerenciador de Campeonatos</h1>
            <div class="grid grid-cols-6 gap-6">
                <?php
                while ($row = mysqli_fetch_assoc($resultCampeonato)) {
                    echo "<div class='bg-white border border-gray-300 rounded-lg shadow-md p-6 m-2 flex flex-col items-center justify-center transition-transform hover:scale-105 hover:bg-gray-50 cursor-pointer w-64 h-40'>";
                    echo "<a href='viewChampionship.php?id=" . $row['id_campeonato'] . "' class='text-xl font-bold text-blue-700 hover:underline mb-2'>" . $row['titulo'] . "</a>";
                    echo "<h3 class='text-gray-600 text-base font-medium'><i class='fas fa-eye mr-2'></i>Ver Campeonato</h3>";
                    echo "</div>";
                }
                ?>
            </div>
        </div>

</body>

</html>