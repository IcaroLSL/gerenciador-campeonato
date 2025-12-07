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
        <aside class="w-64 bg-white border rounded shadow p-4">
            <h3 class="font-semibold mb-3 text-gray-800">
                <i class="fas fa-bars"></i> Menu Administrador
            </h3>
            <nav class="space-y-2 text-sm">
                <a href="MenuPrincipal.php" class="block text-gray-700 hover:text-blue-600 hover:bg-gray-50 p-2 rounded transition">
                    <i class="fas fa-home"></i> Início
                </a>
                <a href="register.php" class="block text-gray-700 hover:text-blue-600 hover:bg-gray-50 p-2 rounded transition">
                    <i class="fas fa-user-plus"></i> Cadastrar Usuário
                </a>
                <a href="registerTeam.php" class="block text-gray-700 hover:text-blue-600 hover:bg-gray-50 p-2 rounded transition">
                    <i class="fas fa-users"></i> Cadastrar Time
                </a>
                <a href="registerChampionship.php" class="block text-gray-700 hover:text-blue-600 hover:bg-gray-50 p-2 rounded transition">
                    <i class="fas fa-trophy"></i> Cadastrar Campeonato
                </a>
                <a href="associateTrainerTeam.php" class="block text-gray-700 hover:text-blue-600 hover:bg-gray-50 p-2 rounded transition">
                    <i class="fas fa-link"></i> Associar Treinador-Time
                </a>
                <a href="associateTeamChampionship.php" class="block text-gray-700 hover:text-blue-600 hover:bg-gray-50 p-2 rounded transition">
                    <i class="fas fa-link"></i> Associar Time-Campeonato
                </a>
            </nav>
        </aside>

        <!-- Conteúdo principal -->
        <div class="flex-1">
            <!-- Cabeçalho -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-link text-blue-500"></i> Associação de Treinadores a Times
                </h1>
                <p class="text-gray-600 mt-2">Gerencie as associações entre treinadores e times</p>
            </div>
            
            <!-- Layout de duas colunas -->
            <div class="grid grid-cols-2 gap-6">
            <!-- Coluna esquerda: Formulário -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">
                    <i class="fas fa-user-plus"></i> Associar Treinador
                </h2>
                
                <form action="" method="post" class="space-y-4">
                    <!-- Treinador -->
                    <div>
                        <label for="treinador" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user-tie"></i> Treinador
                        </label>
                    <select 
                        name="treinador" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="">Selecione o treinador...</option>
                        <?php
                            $query = "SELECT id_usuario, name FROM tbl_usuarios WHERE cargo = 1";
                            $stmtTreinador = mysqli_stmt_init($connection);
                            if (!(mysqli_stmt_prepare($stmtTreinador, $query))) {
                                echo "Erro na preparação da declaração: " . mysqli_stmt_error($stmtTreinador);
                            }
                            mysqli_stmt_execute($stmtTreinador);
                            $result = mysqli_stmt_get_result($stmtTreinador);
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo '<option value="' . htmlspecialchars($row['id_usuario'], ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') . '</option>';
                            }
                            mysqli_stmt_close($stmtTreinador);
                        ?>
                    </select>
                </div>
                    
                    <!-- Time -->
                    <div>
                        <label for="time" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-users"></i> Time
                        </label>
                    <select 
                        name="time" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="">Selecione o time...</option>
                        <?php
                            $query = "SELECT id_time, nome FROM tbl_times";
                            $stmtTime = mysqli_stmt_init($connection);
                            if (!(mysqli_stmt_prepare($stmtTime, $query))) {
                                echo "Erro na preparação da declaração: " . mysqli_stmt_error($stmtTime);
                            }
                            mysqli_stmt_execute($stmtTime);
                            $result = mysqli_stmt_get_result($stmtTime);
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo '<option value="' . htmlspecialchars($row['id_time'], ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($row['nome'], ENT_QUOTES, 'UTF-8') . '</option>';
                            }
                            mysqli_stmt_close($stmtTime);
                            ?>
                        </select>
                    </div>
                    
                    <!-- Botão -->
                    <div>
                        <button 
                            type="submit" 
                            name="register"
                            class="w-full bg-blue-500 text-white py-3 px-4 rounded-lg font-semibold hover:bg-blue-600 transition duration-200 flex items-center justify-center gap-2"
                        >
                            <i class="fas fa-check"></i> Associar
                        </button>
                    </div>
                </form>
                
                <!-- Mensagens de sucesso/erro -->
                <div class="mt-4">
                <?php
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        if (isset($_POST['register'])) {
                            $treinador = (int)clearDataReceived($_POST['treinador']);
                            $time = (int)clearDataReceived($_POST['time']);

                            $stmtUpdateOrInsert = mysqli_stmt_init($connection);
                            $queryCheck = "SELECT * FROM tbl_usuarios_time WHERE id_usuario = ?";
                            if (!(mysqli_stmt_prepare($stmtUpdateOrInsert, $queryCheck))) {
                                echo "<div class='p-4 bg-red-100 border border-red-300 text-red-700 rounded'>";
                                echo "<i class='fas fa-exclamation-circle'></i> Erro na preparação: " . mysqli_error($connection);
                                echo "</div>";
                            } else {
                                mysqli_stmt_bind_param($stmtUpdateOrInsert, "i", $treinador);
                                mysqli_stmt_execute($stmtUpdateOrInsert);
                                $resultCheck = mysqli_stmt_get_result($stmtUpdateOrInsert);
                                
                                if (mysqli_num_rows($resultCheck) > 0) {
                                    // Atualizar associação existente
                                    mysqli_stmt_close($stmtUpdateOrInsert);
                                    $stmtUpdateOrInsert = mysqli_stmt_init($connection);
                                    $query = "UPDATE tbl_usuarios_time SET id_time = ? WHERE id_usuario = ?";
                                    if (!(mysqli_stmt_prepare($stmtUpdateOrInsert, $query))) {
                                        echo "<div class='p-4 bg-red-100 border border-red-300 text-red-700 rounded'>";
                                        echo "<i class='fas fa-exclamation-circle'></i> Erro na preparação: " . mysqli_error($connection);
                                        echo "</div>";
                                    } else {
                                        mysqli_stmt_bind_param($stmtUpdateOrInsert, "ii", $time, $treinador);
                                    }
                                } else {
                                    // Inserir nova associação
                                    mysqli_stmt_close($stmtUpdateOrInsert);
                                    $stmtUpdateOrInsert = mysqli_stmt_init($connection);
                                    $query = "INSERT INTO tbl_usuarios_time (id_usuario, id_time) VALUES (?, ?)";
                                    if (!(mysqli_stmt_prepare($stmtUpdateOrInsert, $query))) {
                                        echo "<div class='p-4 bg-red-100 border border-red-300 text-red-700 rounded'>";
                                        echo "<i class='fas fa-exclamation-circle'></i> Erro na preparação: " . mysqli_error($connection);
                                        echo "</div>";
                                    } else {
                                        mysqli_stmt_bind_param($stmtUpdateOrInsert, "ii", $treinador, $time);
                                    }
                                }
                            }
                            if (mysqli_stmt_execute($stmtUpdateOrInsert)) {
                                echo "<div class='p-4 bg-green-100 border border-green-300 text-green-700 rounded flex items-center gap-2'>";
                                echo "<i class='fas fa-check-circle'></i>";
                                echo "<span><strong>Sucesso!</strong> Associação registrada com sucesso!</span>";
                                echo "</div>";
                                echo "<script>setTimeout(function(){ window.location.reload(); }, 1500);</script>";
                            } else {
                                echo "<div class='p-4 bg-red-100 border border-red-300 text-red-700 rounded flex items-center gap-2'>";
                                echo "<i class='fas fa-exclamation-circle'></i>";
                                echo "<span><strong>Erro!</strong> " . mysqli_stmt_error($stmtUpdateOrInsert) . "</span>";
                                echo "</div>";    
                            }
                            mysqli_stmt_close($stmtUpdateOrInsert);
                        }
                    }
                    ?>
                </div>
            </div>
            
            <!-- Coluna direita: Tabela de associações -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">
                    <i class="fas fa-list"></i> Associações Atuais
                </h2>
                
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-gray-100 border-b-2 border-gray-300">
                                <th class="text-left p-3 font-semibold text-gray-700">
                                    <i class="fas fa-user-tie"></i> Treinador
                                </th>
                                <th class="text-left p-3 font-semibold text-gray-700">
                                    <i class="fas fa-users"></i> Time
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                // Buscar todas as associações de treinadores e times
                                $queryAssociations = "SELECT u.name as treinador_nome, t.nome as time_nome 
                                                     FROM tbl_usuarios_time ut
                                                     INNER JOIN tbl_usuarios u ON ut.id_usuario = u.id_usuario
                                                     INNER JOIN tbl_times t ON ut.id_time = t.id_time
                                                     WHERE u.cargo = 1
                                                     ORDER BY u.name";
                                
                                $stmtAssoc = mysqli_stmt_init($connection);
                                $hasAssociations = false;
                                
                                if (!mysqli_stmt_prepare($stmtAssoc, $queryAssociations)) {
                                    echo "<tr><td colspan='2' class='p-3 text-center text-red-500'>";
                                    echo "<i class='fas fa-exclamation-circle'></i> Erro ao carregar associações";
                                    echo "</td></tr>";
                                } else {
                                    mysqli_stmt_execute($stmtAssoc);
                                    $resultAssoc = mysqli_stmt_get_result($stmtAssoc);
                                    
                                    if ($resultAssoc && mysqli_num_rows($resultAssoc) > 0) {
                                        while ($rowAssoc = mysqli_fetch_assoc($resultAssoc)) {
                                            $hasAssociations = true;
                                            $treinadorNome = htmlspecialchars($rowAssoc['treinador_nome'], ENT_QUOTES, 'UTF-8');
                                            $timeNome = htmlspecialchars($rowAssoc['time_nome'], ENT_QUOTES, 'UTF-8');
                                            
                                            echo "<tr class='border-b border-gray-200 hover:bg-gray-50'>";
                                            echo "<td class='p-3'>" . $treinadorNome . "</td>";
                                            echo "<td class='p-3 text-blue-600 font-medium'>" . $timeNome . "</td>";
                                            echo "</tr>";
                                        }
                                    }
                                    mysqli_stmt_close($stmtAssoc);
                                }
                                
                                if (!$hasAssociations) {
                                    echo "<tr><td colspan='2' class='p-6 text-center text-gray-500 italic'>";
                                    echo "<i class='fas fa-info-circle'></i> Nenhuma associação registrada ainda";
                                    echo "</td></tr>";
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        </div>
        
        <!-- Footer -->
        <div class="text-center mt-4 text-sm text-gray-600">
            <p>&copy; 2025 Gerenciador de Campeonato. Todos os direitos reservados.</p>
        </div>
    </div>
</body>
</html>