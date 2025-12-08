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
    <title>Registro de Campeonato</title>
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
                <i class="fas fa-user-plus"></i> Registro de Campeonatos
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


        <!-- Conteúdo principal -->
        <div class="flex-1">
            <!-- Card principal -->
            <div class="bg-white rounded-lg shadow-lg p-8">
                <!-- Cabeçalho -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-user-plus text-blue-500"></i> Registro de Campeonatos
                    </h1>
                    <p class="text-gray-600 mt-2">Preencha os dados para criar um novo Campeonato no sistema</p>
                </div>

                <form action="" method="post" class="space-y-6">
                    <!-- Nome -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-id-card"></i> Titulo do Campeonato
                        </label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Digite o título do campeonato">
                    </div>

                    <!-- Data Ínicio -->
                    <div>
                        <label for="data_inicio" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-lock"></i> Data Ínicio
                        </label>
                        <input
                            type="date"
                            id="data_inicio"
                            name="data_inicio"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Digite a data de início do campeonato">
                    </div>

                    <!-- Intervalo Em Minutos -->
                    <div>
                        <label for="intervalo_minutos" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-briefcase"></i> Intervalo Em Minutos
                        </label>
                        <input
                            type="time"
                            id="intervalo_minutos"
                            name="intervalo_minutos"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Botão -->
                    <div>
                        <button
                            type="submit"
                            name="register"
                            class="w-full bg-blue-500 text-white py-3 px-4 rounded-lg font-semibold hover:bg-blue-600 transition duration-200 flex items-center justify-center gap-2">
                            <i class="fas fa-check"></i> Registrar Campeonato
                        </button>
                    </div>
                </form>

                <!-- Mensagens de sucesso/erro -->
                <div class="mt-6">
                    <?php
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        if (isset($_POST['register'])) {
                            $name     = clearDataReceived($_POST['name']);
                            $data_inicio   = clearDataReceived($_POST['data_inicio']);

                            // converter intervalo (input time HH:MM ou valor numérico) para minutos inteiros
                            $intervalo_raw = clearDataReceived($_POST['intervalo_minutos']);
                            $intervalo_minutos = 0;
                            if (is_string($intervalo_raw) && preg_match('/^(\d{1,2}):(\d{2})(:\d{2})?$/', $intervalo_raw, $m)) {
                                $hours = (int)$m[1];
                                $minutes = (int)$m[2];
                                $intervalo_minutos = $hours * 60 + $minutes;
                            } else {
                                // se não for formato de hora, tenta interpretar como número de minutos
                                $intervalo_minutos = (int)$intervalo_raw;
                            }

                            $query = "INSERT INTO tbl_campeonatos (titulo, data_inicio, intervalo_minutos) VALUES (?, ?, ?)";
                            $stmt = mysqli_stmt_init($connection);
                            if (!(mysqli_stmt_prepare($stmt, $query))) {
                                echo "Erro na preparação da declaração: " . mysqli_stmt_error($stmt);
                            }
                            // titulo: string, data_inicio: string, intervalo_minutos: integer
                            mysqli_stmt_bind_param($stmt, "ssi", $name, $data_inicio, $intervalo_minutos);
                            if (mysqli_stmt_execute($stmt)) {
                                echo "<div class='p-4 bg-green-100 border border-green-300 text-green-700 rounded flex items-center gap-2'>";
                                echo "<i class='fas fa-check-circle'></i>";
                                echo "<span><strong>Sucesso!</strong> Campeonato registrado com sucesso!</span>";
                                echo "</div>";
                            } else {
                                echo "<div class='p-4 bg-red-100 border border-red-300 text-red-700 rounded flex items-center gap-2'>";
                                echo "<i class='fas fa-exclamation-circle'></i>";
                                echo "<span><strong>Erro!</strong> " . mysqli_stmt_error($stmt) . "</span>";
                                echo "</div>";
                            }
                            mysqli_stmt_close($stmt);
                        }
                    }
                    ?>
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