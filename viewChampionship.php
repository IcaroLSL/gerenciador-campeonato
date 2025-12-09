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

// Validar ID do campeonato
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: MenuPrincipal.php");
    exit;
}

$id_campeonato = (int)$_GET['id'];

// Buscar dados do campeonato usando prepared statement
$stmtCampeonato = mysqli_stmt_init($connection);
$queryCampeonato = "SELECT * FROM tbl_campeonatos WHERE id_campeonato = ?";
if (!mysqli_stmt_prepare($stmtCampeonato, $queryCampeonato)) {
    die("Erro ao preparar query do campeonato");
}
mysqli_stmt_bind_param($stmtCampeonato, "i", $id_campeonato);
mysqli_stmt_execute($stmtCampeonato);
$resultCampeonato = mysqli_stmt_get_result($stmtCampeonato);
$campeonato = mysqli_fetch_assoc($resultCampeonato);
mysqli_stmt_close($stmtCampeonato);

if (!$campeonato) {
    header("Location: MenuPrincipal.php");
    exit;
}

// Se jogador ou treinador, buscar o time do usuário
$userTeamId = null;
$userTeamName = null;
if ($userCargo == 0 || $userCargo == 1) {
    $stmtUserTeam = mysqli_stmt_init($connection);
    $queryUserTeam = "SELECT ut.id_time, t.nome 
                      FROM tbl_usuarios_time ut 
                      INNER JOIN tbl_times t ON ut.id_time = t.id_time 
                      WHERE ut.id_usuario = ? AND t.id_campeonato = ?";
    if (mysqli_stmt_prepare($stmtUserTeam, $queryUserTeam)) {
        mysqli_stmt_bind_param($stmtUserTeam, "ii", $userId, $id_campeonato);
        mysqli_stmt_execute($stmtUserTeam);
        $resultUserTeam = mysqli_stmt_get_result($stmtUserTeam);
        if ($row = mysqli_fetch_assoc($resultUserTeam)) {
            $userTeamId = (int)$row['id_time'];
            $userTeamName = $row['nome'];
        }
        mysqli_stmt_close($stmtUserTeam);
    }
}

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
    <title>Visualizar Campeonato</title>
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
                <i class="fas fa-calendar-alt"></i> Visualizar Campeonato
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
        <?php endif; ?>

        <!-- Conteúdo principal -->
        <div class="flex-1">
            <!-- Cabeçalho do Campeonato -->
            <div class="bg-white border border-gray-300 rounded-lg shadow-md p-6 mb-6">
                <h1 class="text-3xl font-bold text-blue-700 mb-4">
                    <i class="fas fa-trophy"></i> <?php echo htmlspecialchars($campeonato['titulo'], ENT_QUOTES, 'UTF-8'); ?>
                </h1>
                <div class="grid grid-cols-2 gap-4 text-gray-700">
                    <p><strong>Data de Início:</strong> <?php echo htmlspecialchars($campeonato['data_inicio'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><strong>Intervalo:</strong> <?php echo (int)$campeonato['intervalo_minutos']; ?> minutos</p>
                    <p><strong>Status:</strong> 
                        <span class="<?php echo ($campeonato['iniciado'] ? 'text-green-600' : 'text-orange-600'); ?> font-semibold">
                            <?php echo ($campeonato['iniciado'] ? 'Iniciado' : 'Não Iniciado'); ?>
                        </span>
                    </p>
                    <?php if ($userCargo == 0 || $userCargo == 1): ?>
                        <p><strong>Seu Time:</strong> 
                            <span class="text-blue-600 font-semibold">
                                <?php echo $userTeamName ? htmlspecialchars($userTeamName, ENT_QUOTES, 'UTF-8') : 'Não vinculado'; ?>
                            </span>
                        </p>
                    <?php endif; ?>
                </div>

                <?php 
                // Processar início do campeonato (apenas admin)
                if ($userCargo == 2 && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['iniciar_campeonato'])) {
                    $stmtUpdate = mysqli_stmt_init($connection);
                    $queryUpdate = "UPDATE tbl_campeonatos SET iniciado = 1 WHERE id_campeonato = ?";
                    if (mysqli_stmt_prepare($stmtUpdate, $queryUpdate)) {
                        mysqli_stmt_bind_param($stmtUpdate, "i", $id_campeonato);
                        if (mysqli_stmt_execute($stmtUpdate)) {
                            echo "<script>window.location.href = 'viewChampionship.php?id=" . $id_campeonato . "';</script>";
                            exit;
                        }
                        mysqli_stmt_close($stmtUpdate);
                    }
                }
                
                // Botão iniciar (apenas admin e se não iniciado)
                if ($userCargo == 2 && !$campeonato['iniciado']): 
                ?>
                    <form method="post" class="mt-4" onsubmit="return confirm('Deseja iniciar o campeonato?');">
                        <button type="submit" name="iniciar_campeonato" class="bg-green-500 text-white py-2 px-4 rounded hover:bg-green-600 transition">
                            <i class="fas fa-play"></i> Iniciar Campeonato
                        </button>
                    </form>
                <?php endif; ?>
            </div>

            <?php
            // Buscar times vinculados ao campeonato
            $stmtTimes = mysqli_stmt_init($connection);
            $queryTimes = "SELECT id_time, nome FROM tbl_times WHERE id_campeonato = ? ORDER BY nome";
            if (!mysqli_stmt_prepare($stmtTimes, $queryTimes)) {
                die("Erro ao preparar query de times");
            }
            mysqli_stmt_bind_param($stmtTimes, "i", $id_campeonato);
            mysqli_stmt_execute($stmtTimes);
            $resultTimes = mysqli_stmt_get_result($stmtTimes);
            
            $times = [];
            while ($t = mysqli_fetch_assoc($resultTimes)) {
                $times[] = $t['nome'];
            }
            mysqli_stmt_close($stmtTimes);

            if (count($times) < 2) {
                echo "<div class='bg-red-100 border border-red-300 text-red-700 rounded-lg p-4'>";
                echo "<i class='fas fa-exclamation-triangle'></i> ";
                echo "<strong>Atenção:</strong> Não é possível montar um campeonato com menos de 2 times vinculados.";
                echo "</div>";
            } else {
                // Gerar rodadas proceduralmente (todos contra todos, ida)
                $numTimes = count($times);
                $rodadas = ($numTimes % 2 == 0) ? $numTimes - 1 : $numTimes;
                $timesList = $times;
                if ($numTimes % 2 != 0) $timesList[] = 'BYE'; // Adiciona time fictício se ímpar
                $numTimesList = count($timesList);
                
                // Gerar todas as rodadas
                $rodadasArr = [];
                $tempTimesList = $timesList;
                for ($rodada = 1; $rodada <= $rodadas; $rodada++) {
                    $jogosRodada = [];
                    for ($i = 0; $i < $numTimesList / 2; $i++) {
                        $casa = $tempTimesList[$i];
                        $fora = $tempTimesList[$numTimesList - 1 - $i];
                        if ($casa != 'BYE' && $fora != 'BYE') {
                            $jogosRodada[] = ['casa' => $casa, 'fora' => $fora];
                        }
                    }
                    $rodadasArr[] = $jogosRodada;
                    // Rotaciona times (exceto o primeiro)
                    array_splice($tempTimesList, 1, 0, array_splice($tempTimesList, -1));
                }
                
                // Filtrar jogos se for jogador
                if ($userCargo == 0 && $userTeamName) {
                    $jogosDoUsuario = [];
                    foreach ($rodadasArr as $idx => $jogosRodada) {
                        foreach ($jogosRodada as $jogo) {
                            if ($jogo['casa'] === $userTeamName || $jogo['fora'] === $userTeamName) {
                                $jogosDoUsuario[] = [
                                    'rodada' => $idx + 1,
                                    'casa' => $jogo['casa'],
                                    'fora' => $jogo['fora']
                                ];
                            }
                        }
                    }
                    
                    echo "<div class='bg-white border border-gray-300 rounded-lg shadow-md p-6'>";
                    echo "<h2 class='text-2xl font-bold text-gray-800 mb-4'>";
                    echo "<i class='fas fa-futbol'></i> Seus Jogos no Campeonato";
                    echo "</h2>";
                    
                    if (empty($jogosDoUsuario)) {
                        echo "<p class='text-gray-600 italic'>Nenhum jogo encontrado para seu time neste campeonato.</p>";
                    } else {
                        echo "<div class='space-y-3'>";
                        foreach ($jogosDoUsuario as $jogo) {
                            $isCasa = ($jogo['casa'] === $userTeamName);
                            echo "<div class='bg-gray-50 border border-gray-200 rounded-lg p-4'>";
                            echo "<p class='text-sm text-gray-600 mb-2'><strong>Rodada " . $jogo['rodada'] . "</strong></p>";
                            echo "<div class='flex items-center justify-center gap-4 text-lg'>";
                            echo "<span class='" . ($isCasa ? "font-bold text-blue-600" : "text-gray-700") . "'>" . htmlspecialchars($jogo['casa'], ENT_QUOTES, 'UTF-8') . "</span>";
                            echo "<span class='text-gray-500 font-semibold'>X</span>";
                            echo "<span class='" . (!$isCasa ? "font-bold text-blue-600" : "text-gray-700") . "'>" . htmlspecialchars($jogo['fora'], ENT_QUOTES, 'UTF-8') . "</span>";
                            echo "</div>";
                            echo "</div>";
                        }
                        echo "</div>";
                    }
                    echo "</div>";
                    
                } else {
                    // Admin e Treinador veem todas as rodadas
                    echo "<div class='bg-white border border-gray-300 rounded-lg shadow-md p-6'>";
                    echo "<h2 class='text-2xl font-bold text-gray-800 mb-4'>";
                    echo "<i class='fas fa-calendar-alt'></i> Rodadas do Campeonato";
                    echo "</h2>";
                    
                    // Layout em grid responsivo
                    $colsClass = min($rodadas, 4); // Máximo 4 colunas
                    echo "<div class='grid grid-cols-1 md:grid-cols-2 lg:grid-cols-" . $colsClass . " gap-4'>";
                    
                    foreach ($rodadasArr as $idx => $jogosRodada) {
                        echo "<div class='bg-gray-50 border border-gray-200 rounded-lg p-4'>";
                        echo "<h3 class='text-lg font-semibold text-blue-600 mb-3 text-center border-b pb-2'>Rodada " . ($idx + 1) . "</h3>";
                        echo "<div class='space-y-2'>";
                        
                        foreach ($jogosRodada as $jogo) {
                            $destacarCasa = ($userCargo == 1 && $userTeamName && $jogo['casa'] === $userTeamName);
                            $destacarFora = ($userCargo == 1 && $userTeamName && $jogo['fora'] === $userTeamName);
                            
                            echo "<div class='flex items-center justify-between text-sm p-2 rounded " . ($destacarCasa || $destacarFora ? "bg-blue-100 border border-blue-300" : "bg-white") . "'>";
                            echo "<span class='" . ($destacarCasa ? "font-bold text-blue-700" : "text-gray-700") . "'>" . htmlspecialchars($jogo['casa'], ENT_QUOTES, 'UTF-8') . "</span>";
                            echo "<span class='text-gray-500 font-semibold mx-2'>X</span>";
                            echo "<span class='" . ($destacarFora ? "font-bold text-blue-700" : "text-gray-700") . "'>" . htmlspecialchars($jogo['fora'], ENT_QUOTES, 'UTF-8') . "</span>";
                            echo "</div>";
                        }
                        
                        echo "</div>";
                        echo "</div>";
                    }
                    
                    echo "</div>";
                    echo "</div>";
                }
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