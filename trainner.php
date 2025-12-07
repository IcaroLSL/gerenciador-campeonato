<?php
    require_once("function.php");
    require_once("conecta.php");
    if (!isset($_COOKIE['credentials']) || $_COOKIE['credentials'] == false) {
        header("Location: login.php");
        exit;
    }
    $cookieData = json_decode($_COOKIE['credentials'], true);
    if ($cookieData['cargo'] != 1) {
        header("Location: MenuPrincipal.php");
        exit;
    }
    
    // Logout
    if (isset($_POST['deslogar'])) {
        setcookie("credentials", false, time() + (86400 * 30), "/");
        header("Location: login.php");
        exit;
    }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Treinador - Gerenciar Time</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body class="bg-gray-50">
    <!-- Barra de navegação -->
    <nav class="flex flex-row justify-between items-center p-4 bg-gray-200 shadow-md">
        <div class="flex items-center gap-4">
            <a href="MenuPrincipal.php" class="text-xl font-bold text-gray-800 hover:text-blue-500 transition">
                <i class="fas fa-trophy"></i> Fatec Campeonatos
            </a>
            <span class="text-gray-400">|</span>
            <span class="text-gray-600">
                <i class="fas fa-users"></i> Gerenciar Time
            </span>
        </div>

        <div class="flex flex-row items-center space-x-4">
            <h3 class="text-gray-700">
                <i class="fas fa-user-tie"></i> <?php echo htmlspecialchars($cookieData['name'], ENT_QUOTES, 'UTF-8'); ?> (Treinador)
            </h3>

            <form action="" method="post">
                <button class="bg-red-500 text-white py-2 px-4 rounded cursor-pointer hover:bg-red-600 transition" type="submit" name="deslogar">
                    <i class="fas fa-sign-out-alt"></i> Deslogar
                </button>
            </form>
        </div>
    </nav>
    <?php
        $userId = isset($cookieData['id']) ? (int)$cookieData['id'] : 0;

        // Buscar o time do treinador logado (usa tbl_usuarios_time agora)
        $query = "SELECT id_time FROM tbl_usuarios_time WHERE id_usuario = ?";
        $stmtTime = mysqli_stmt_init($connection);
        if (!mysqli_stmt_prepare($stmtTime, $query)) {
            error_log('Prepare falhou (treinador -> time): ' . mysqli_error($connection));
            echo "<p>Erro interno. Contate o administrador.</p>";
            exit;
        }

        mysqli_stmt_bind_param($stmtTime, "i", $userId);
        mysqli_stmt_execute($stmtTime);
        $result = mysqli_stmt_get_result($stmtTime);
        if (!$result || mysqli_num_rows($result) == 0) {
            echo "<p>Você ainda não está associado a nenhum time. Por favor, contate o administrador.</p>";
            mysqli_stmt_close($stmtTime);
            exit;
        }

        $row = mysqli_fetch_assoc($result);
        $idTime = (int)$row['id_time'];
        mysqli_stmt_close($stmtTime);
    ?>
    
    <!-- Conteúdo principal -->
    <div class="p-4">
        <h1 class="text-2xl font-bold mb-6"><i class="fas fa-clipboard-list"></i> Gerenciar Time</h1>
        
        <!-- Layout de duas colunas -->
        <div class="grid grid-cols-2 gap-6">
            <!-- Coluna esquerda: Contratar jogadores -->
            <div class="p-6 bg-white border border-gray-300 rounded shadow">
                <h2 class="text-xl font-bold mb-4"><i class="fas fa-user-plus"></i> Contratar Jogador</h2>
                <form action="" method="post">
                    <select name="jogadores" id="jogadores" class="w-full p-2 border border-gray-300 rounded mb-4" required>
                <?php
                    // Buscar jogadores (cargo=0) que não estão no time do treinador ainda
                    // Simplificado: lista jogadores sem time OU de outros times
                    $query = "SELECT tu.id_usuario, tu.name 
                              FROM tbl_usuarios tu 
                              LEFT JOIN tbl_usuarios_time tut ON tu.id_usuario = tut.id_usuario 
                              WHERE tu.cargo = 0 
                              AND (tut.id_time IS NULL OR tut.id_time != ?)";

                    $stmt = mysqli_stmt_init($connection);
                    $playersFound = false;
                    if (!mysqli_stmt_prepare($stmt, $query)) {
                        error_log('Prepare falhou (buscar jogadores): ' . mysqli_error($connection));
                        echo "<option>Erro ao carregar jogadores</option>";
                    } else {
                        mysqli_stmt_bind_param($stmt, "i", $idTime);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);

                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $playersFound = true;
                                $id_usuario = (int)$row['id_usuario'];
                                $name = htmlspecialchars($row['name'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                                echo "<option value='" . $id_usuario . "'>" . $name . "</option>";
                            }
                        }
                        mysqli_stmt_close($stmt);
                    }

                    if (!$playersFound) {
                        echo "<option disabled>Não há jogadores disponíveis no momento</option>";
                    }
                ?>
                    </select>
                    <p class="text-sm text-gray-600 mb-4">Caso não encontre o jogador desejado, por favor, contate o administrador para realizar o cadastro.</p>
                    <button type="submit" name="contratar" class="bg-blue-500 text-white py-2 px-4 rounded cursor-pointer hover:bg-blue-600">
                        <i class="fas fa-check"></i> Contratar
                    </button>
                </form>
            </div>
            
            <!-- Coluna direita: Jogadores no time -->
            <div class="p-6 bg-white border border-gray-300 rounded shadow">
                <h2 class="text-xl font-bold mb-4"><i class="fas fa-users"></i> Jogadores no Time: 
                    <?php 
                        $queryTeamName = "SELECT nome FROM tbl_times WHERE id_time = ?";
                        $stmtTeamName = mysqli_stmt_init($connection);
                        $teamName = "Desconhecido";
                        if (mysqli_stmt_prepare($stmtTeamName, $queryTeamName)) {
                            mysqli_stmt_bind_param($stmtTeamName, "i", $idTime);
                            mysqli_stmt_execute($stmtTeamName);
                            $resultTeamName = mysqli_stmt_get_result($stmtTeamName);
                            if ($resultTeamName && mysqli_num_rows($resultTeamName) > 0) {
                                $rowTeamName = mysqli_fetch_assoc($resultTeamName);
                                $teamName = $rowTeamName['nome'];
                            }
                            mysqli_stmt_close($stmtTeamName);
                        }
                        echo $teamName; 
                    ?>
                </h2>
                <ul class="space-y-2">
                    <?php
                        // Buscar jogadores (cargo=0) que estão no time do treinador
                        $queryTeam = "SELECT tu.id_usuario, tu.name 
                                      FROM tbl_usuarios tu 
                                      INNER JOIN tbl_usuarios_time tut ON tu.id_usuario = tut.id_usuario 
                                      WHERE tu.cargo = 0 AND tut.id_time = ?
                                      ORDER BY tu.name";
                        
                        $stmtTeam = mysqli_stmt_init($connection);
                        $teamPlayersFound = false;
                        
                        if (!mysqli_stmt_prepare($stmtTeam, $queryTeam)) {
                            error_log('Prepare falhou (buscar jogadores do time): ' . mysqli_error($connection));
                            echo "<li class='text-red-500'>Erro ao carregar jogadores do time</li>";
                        } else {
                            mysqli_stmt_bind_param($stmtTeam, "i", $idTime);
                            mysqli_stmt_execute($stmtTeam);
                            $resultTeam = mysqli_stmt_get_result($stmtTeam);
                            
                            if ($resultTeam && mysqli_num_rows($resultTeam) > 0) {
                                while ($rowTeam = mysqli_fetch_assoc($resultTeam)) {
                                    $teamPlayersFound = true;
                                    $playerName = htmlspecialchars($rowTeam['name'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                                    echo "<li class='p-3 border border-gray-200 rounded hover:bg-gray-50'>";
                                    echo "<i class='fas fa-user text-blue-500'></i> " . $playerName;
                                    echo "</li>";
                                }
                            }
                            mysqli_stmt_close($stmtTeam);
                        }
                        
                        if (!$teamPlayersFound) {
                            echo "<li class='text-gray-500 italic'>Nenhum jogador contratado ainda</li>";
                        }
                    ?>
                </ul>
            </div>
        </div>
    </div>
    
    <?php
        if (isset($_POST['contratar']) && $_SERVER["REQUEST_METHOD"] == "POST") {
            $idJogador = (int) clearDataReceived($_POST['jogadores']);
            
            // Verificar se o jogador já está em algum time
            $checkQuery = "SELECT id_time FROM tbl_usuarios_time WHERE id_usuario = ?";
            $checkStmt = mysqli_stmt_init($connection);
            if (mysqli_stmt_prepare($checkStmt, $checkQuery)) {
                mysqli_stmt_bind_param($checkStmt, "i", $idJogador);
                mysqli_stmt_execute($checkStmt);
                $checkResult = mysqli_stmt_get_result($checkStmt);
                
                if (mysqli_num_rows($checkResult) > 0) {
                    // Jogador já está em um time, atualizar para o novo time
                    mysqli_stmt_close($checkStmt);
                    $updateQuery = "UPDATE tbl_usuarios_time SET id_time = ? WHERE id_usuario = ?";
                    $stmt = mysqli_stmt_init($connection);
                    if (mysqli_stmt_prepare($stmt, $updateQuery)) {
                        mysqli_stmt_bind_param($stmt, "ii", $idTime, $idJogador);
                        if (mysqli_stmt_execute($stmt)) {
                            echo "<div class='p-4 bg-green-100 border border-green-300 text-green-700 rounded mb-4'>Jogador contratado com sucesso!</div>";
                            // Recarregar a página para atualizar a lista
                            echo "<script>setTimeout(function(){ window.location.reload(); }, 1500);</script>";
                        } else {
                            echo "<div class='p-4 bg-red-100 border border-red-300 text-red-700 rounded mb-4'>Erro ao contratar jogador: " . mysqli_stmt_error($stmt) . "</div>";
                        }
                        mysqli_stmt_close($stmt);
                    }
                } else {
                    // Jogador não está em nenhum time, inserir novo registro
                    mysqli_stmt_close($checkStmt);
                    $insertQuery = "INSERT INTO tbl_usuarios_time (id_usuario, id_time) VALUES (?, ?)";
                    $stmt = mysqli_stmt_init($connection);
                    if (mysqli_stmt_prepare($stmt, $insertQuery)) {
                        mysqli_stmt_bind_param($stmt, "ii", $idJogador, $idTime);
                        if (mysqli_stmt_execute($stmt)) {
                            echo "<div class='p-4 bg-green-100 border border-green-300 text-green-700 rounded mb-4'>Jogador contratado com sucesso!</div>";
                            // Recarregar a página para atualizar a lista
                            echo "<script>setTimeout(function(){ window.location.reload(); }, 1500);</script>";
                        } else {
                            echo "<div class='p-4 bg-red-100 border border-red-300 text-red-700 rounded mb-4'>Erro ao contratar jogador: " . mysqli_stmt_error($stmt) . "</div>";
                        }
                        mysqli_stmt_close($stmt);
                    } else {
                        echo "<div class='p-4 bg-red-100 border border-red-300 text-red-700 rounded mb-4'>Erro na preparação: " . mysqli_error($connection) . "</div>";
                    }
                }
            } else {
                echo "<div class='p-4 bg-red-100 border border-red-300 text-red-700 rounded mb-4'>Erro ao verificar jogador: " . mysqli_error($connection) . "</div>";
            }
        }
    ?>
</body>
</html>