<?php
require_once('conecta.php');
require_once('function.php');

// Verificar autenticação
if (!isset($_COOKIE['credentials']) || $_COOKIE['credentials'] == false) {
    header("Location: login.php");
    exit;
}

$credentials = json_decode($_COOKIE['credentials'], true);
if (!isset($credentials['id']) || !isset($credentials['cargo']) || !isset($credentials['name']) || !isset($credentials['username'])) {
    header("Location: login.php");
    exit;
}

$userId = (int)$credentials['id'];
$userCargo = (int)$credentials['cargo'];

// Buscar campeonatos usando prepared statement
$stmtCampeonatos = mysqli_stmt_init($connection);
$queryCampeonatos = "SELECT * FROM tbl_campeonatos ORDER BY data_inicio DESC";
if (!mysqli_stmt_prepare($stmtCampeonatos, $queryCampeonatos)) {
    die("Erro ao preparar query de campeonatos");
}
mysqli_stmt_execute($stmtCampeonatos);
$resultCampeonato = mysqli_stmt_get_result($stmtCampeonatos);

switch ($credentials['cargo']) {
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
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Campeonatos</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>

<body class="bg-gray-50 min-h-screen">
    <!-- Barra de navegação -->
    <nav class="flex flex-row justify-between items-center p-4 bg-gray-200 shadow-md">
        <div class="flex items-center gap-4">
            <a href="MenuPrincipal.php" class="text-xl font-bold text-gray-800 hover:text-blue-500 transition">
                <i class="fas fa-trophy"></i> Fatec Campeonatos
            </a>
            <span class="text-gray-400">|</span>
            <span class="text-gray-600">
                <i class="fas fa-list"></i> Campeonatos
            </span>
        </div>

        <div class="flex flex-row items-center space-x-4">
            <h3 class="text-gray-700">
                <?php 
                $cargoLabel = ['Jogador', 'Treinador', 'Administrador'][$userCargo] ?? 'Usuário';
                $cargoIcon = ['fa-user', 'fa-user-tie', 'fa-user-shield'][$userCargo] ?? 'fa-user';
                ?>
                <i class="fas <?php echo $cargoIcon; ?>"></i> 
                <?php echo htmlspecialchars($credentials['name'], ENT_QUOTES, 'UTF-8'); ?> 
                (<?php echo $cargoLabel; ?>)
            </h3>
            <a href="MenuPrincipal.php" class="bg-gray-500 text-white py-2 px-4 rounded hover:bg-gray-600 transition">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </nav>

    <div class="flex gap-6 p-4">
        <!-- Sidebar (apenas para Admin) -->
        <?php if ($userCargo == 2): ?>
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

        <?php elseif ($userCargo == 1): ?>
        <!-- Sidebar para Treinador -->
        <aside class="w-64 bg-white border rounded shadow p-4">
            <h3 class="font-semibold mb-3 text-gray-800">
                <i class="fas fa-bars"></i> Menu Treinador
            </h3>
            <nav class="space-y-2 text-sm">
                <a href="MenuPrincipal.php" class="block text-gray-700 hover:text-blue-600 hover:bg-gray-50 p-2 rounded transition">
                    <i class="fas fa-home"></i> Início
                </a>
                <a href="trainner.php" class="block text-gray-700 hover:text-blue-600 hover:bg-gray-50 p-2 rounded transition">
                    <i class="fas fa-user-plus"></i> Contratar Jogador
                </a>
                <a href="manageChampionships.php" class="block text-gray-700 hover:text-blue-600 hover:bg-gray-50 p-2 rounded transition">
                    <i class="fas fa-list"></i> Ver Campeonatos
                </a>
            </nav>
        </aside>
        <?php endif; ?>

        <!-- Conteúdo principal -->
        <div class="flex-1">
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-trophy text-yellow-500"></i> Campeonatos
                </h1>
                <p class="text-gray-600 mt-2">
                    <?php 
                    if ($userCargo == 0) {
                        echo "Veja os campeonatos e seus jogos";
                    } elseif ($userCargo == 1) {
                        echo "Acompanhe os campeonatos do seu time";
                    } else {
                        echo "Gerencie e visualize todos os campeonatos";
                    }
                    ?>
                </p>
            </div>

            <?php
            $campeonatos = [];
            while ($row = mysqli_fetch_assoc($resultCampeonato)) {
                // Se for jogador ou treinador, verificar se tem time no campeonato
                if ($userCargo == 0 || $userCargo == 1) {
                    $stmtCheckTeam = mysqli_stmt_init($connection);
                    $queryCheckTeam = "SELECT COUNT(*) as count FROM tbl_usuarios_time ut 
                                       INNER JOIN tbl_times t ON ut.id_time = t.id_time 
                                       WHERE ut.id_usuario = ? AND t.id_campeonato = ?";
                    if (mysqli_stmt_prepare($stmtCheckTeam, $queryCheckTeam)) {
                        mysqli_stmt_bind_param($stmtCheckTeam, "ii", $userId, $row['id_campeonato']);
                        mysqli_stmt_execute($stmtCheckTeam);
                        $resultCheck = mysqli_stmt_get_result($stmtCheckTeam);
                        $checkRow = mysqli_fetch_assoc($resultCheck);
                        mysqli_stmt_close($stmtCheckTeam);
                        
                        if ($checkRow['count'] > 0) {
                            $campeonatos[] = $row;
                        }
                    }
                } else {
                    // Admin vê todos
                    $campeonatos[] = $row;
                }
            }
            mysqli_stmt_close($stmtCampeonatos);

            if (empty($campeonatos)) {
                echo "<div class='bg-yellow-100 border border-yellow-300 text-yellow-800 rounded-lg p-6 text-center'>";
                echo "<i class='fas fa-info-circle text-4xl mb-3'></i>";
                echo "<p class='font-semibold'>Nenhum campeonato encontrado</p>";
                if ($userCargo == 0 || $userCargo == 1) {
                    echo "<p class='text-sm mt-2'>Você ainda não está vinculado a nenhum campeonato. Entre em contato com o administrador.</p>";
                }
                echo "</div>";
            } else {
                echo "<div class='grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6'>";
                
                foreach ($campeonatos as $row) {
                    $statusClass = $row['iniciado'] ? 'border-green-400 bg-green-50' : 'border-gray-300 bg-white';
                    $statusIcon = $row['iniciado'] ? 'fa-check-circle text-green-600' : 'fa-clock text-orange-600';
                    $statusText = $row['iniciado'] ? 'Em andamento' : 'Aguardando início';
                    
                    echo "<div class='border-2 {$statusClass} rounded-lg shadow-md p-6 transition-all hover:shadow-xl hover:scale-105 cursor-pointer'>";
                    echo "<a href='viewChampionship.php?id=" . (int)$row['id_campeonato'] . "' class='block'>";
                    
                    echo "<div class='flex items-center justify-between mb-3'>";
                    echo "<h2 class='text-xl font-bold text-blue-700'>" . htmlspecialchars($row['titulo'], ENT_QUOTES, 'UTF-8') . "</h2>";
                    echo "<i class='fas {$statusIcon} text-xl'></i>";
                    echo "</div>";
                    
                    echo "<div class='space-y-2 text-sm text-gray-700 mb-4'>";
                    echo "<p><i class='fas fa-calendar-alt w-5'></i> " . date('d/m/Y', strtotime($row['data_inicio'])) . "</p>";
                    echo "<p><i class='fas fa-clock w-5'></i> {$row['intervalo_minutos']} min</p>";
                    echo "<p><i class='fas {$statusIcon} w-5'></i> {$statusText}</p>";
                    echo "</div>";
                    
                    echo "<div class='mt-4 pt-4 border-t border-gray-200'>";
                    echo "<span class='text-blue-600 font-semibold hover:underline flex items-center gap-2'>";
                    echo "<i class='fas fa-eye'></i> Ver detalhes";
                    echo "</span>";
                    echo "</div>";
                    
                    echo "</a>";
                    echo "</div>";
                }
                
                echo "</div>";
            }
            ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-center mt-8 py-4 text-sm text-gray-600 border-t">
        <p>&copy; 2025 Gerenciador de Campeonato. Todos os direitos reservados.</p>
    </footer>

</body>
</html>