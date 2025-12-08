<?php
include 'conecta.php';
include 'function.php';
$id = $_GET['id'];
$SelectCampeonato = "SELECT * FROM tbl_campeonatos where id_campeonato = $id";
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
            <h1 class="text-2xl font-bold">Visualizar Campeonato</h1>
            <?php
            foreach ($resultCampeonato as $row) {
                echo "<div class='bg-white border border-gray-300 rounded-lg shadow-md p-6 m-2 flex flex-col items-center justify-center'>";
                echo "<h2 class='text-xl font-bold text-blue-700'>" . htmlspecialchars($row['titulo'], ENT_QUOTES, 'UTF-8') . "</h2>";
                echo "<p class='text-gray-600'>" . htmlspecialchars($row['criado_em'], ENT_QUOTES, 'UTF-8') . "</p>";
                echo "<p class='text-gray-800 mt-4'>Data de Início: <span class='font-semibold'>" . htmlspecialchars($row['data_inicio'], ENT_QUOTES, 'UTF-8') . "</span></p>";
                echo "<p class='text-gray-800'>Intervalo entre jogos (minutos): <span class='font-semibold'>" . htmlspecialchars($row['intervalo_minutos'], ENT_QUOTES, 'UTF-8') . "</span></p>";
                echo "<p class='text-gray-800'>Pontos por vitória: <span class='font-semibold'>" . htmlspecialchars($row['pontos_vitoria'], ENT_QUOTES, 'UTF-8') . "</span></p>";
                echo "<p class='text-gray-800'>Pontos por empate: <span class='font-semibold'>" . htmlspecialchars($row['pontos_empate'], ENT_QUOTES, 'UTF-8') . "</span></p>";
                echo "<p class='text-gray-800'>Pontos por derrota: <span class='font-semibold'>" . htmlspecialchars($row['pontos_derrota'], ENT_QUOTES  , 'UTF-8') . "</span></p>"; 
                echo "<button class='bg-green-500 text-white py-2 px-4 rounded'>Iniciar Campeonato</button>";
                echo "<p class='text-gray-800 cursor-pointer'>Status: <span class='font-semibold'>" . ($row['iniciado'] ? 'Iniciado' : 'Não Iniciado') . "</span></p>";
                // Buscar times vinculados ao campeonato
                $id_campeonato = $row['id_campeonato'];
                $queryTimes = "SELECT nome FROM tbl_times WHERE id_campeonato = $id_campeonato";
                $resultTimes = mysqli_query($connection, $queryTimes);
                $times = [];
                while ($t = mysqli_fetch_assoc($resultTimes)) {
                    $times[] = $t['nome'];
                }
                if (count($times) < 2) {
                    echo "<p class='text-red-500 mt-4'>Não é possível montar um campeonato: menos de 2 times vinculados.</p>";
                } else {
                    // Gerar rodadas proceduralmente (todos contra todos, ida)
                    $numTimes = count($times);
                    $rodadas = ($numTimes % 2 == 0) ? $numTimes - 1 : $numTimes;
                    $timesList = $times;
                    if ($numTimes % 2 != 0) $timesList[] = 'BYE'; // Adiciona time fictício se ímpar
                    $numTimesList = count($timesList);
                    // Rodadas em colunas
                    echo "<div class='grid grid-cols-" . $rodadas . " gap-6 mt-4 w-full'>";
                    $rodadasArr = [];
                    $tempTimesList = $timesList;
                    for ($rodada = 1; $rodada <= $rodadas; $rodada++) {
                        $jogosRodada = [];
                        for ($i = 0; $i < $numTimesList / 2; $i++) {
                            $casa = $tempTimesList[$i];
                            $fora = $tempTimesList[$numTimesList - 1 - $i];
                            if ($casa != 'BYE' && $fora != 'BYE') {
                                $jogosRodada[] = "$casa x $fora";
                            }
                        }
                        $rodadasArr[] = $jogosRodada;
                        // Rotaciona times (exceto o primeiro)
                        array_splice($tempTimesList, 1, 0, array_splice($tempTimesList, -1));
                    }
                    // Exibe cada rodada em uma coluna
                    foreach ($rodadasArr as $idx => $jogosRodada) {
                        echo "<div class='bg-gray-50 border border-gray-200 rounded p-3 flex flex-col items-center'>";
                        echo "<h3 class='text-lg font-semibold text-gray-800 mb-2'>Rodada " . ($idx + 1) . "</h3>";
                        foreach ($jogosRodada as $jogo) {
                            echo "<span class='font-bold text-blue-600 mb-2'>$jogo</span>";
                        }
                        echo "</div>";
                    }
                    echo "</div>";
                }
                echo "</div>";
            }
            ?>
        </div>

    </div>

</body>

</html>