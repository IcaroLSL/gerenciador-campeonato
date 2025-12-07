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
    <title>Registro de Usuários</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body class="bg-gray-100 min-h-screen py-8">
    <div class="max-w-2xl mx-auto">
        <!-- Card principal -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            <!-- Cabeçalho -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-user-plus text-blue-500"></i> Registro de Usuários
                </h1>
                <p class="text-gray-600 mt-2">Preencha os dados para criar um novo usuário no sistema</p>
            </div>
            
            <form action="" method="post" class="space-y-6">
                <!-- Nome -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-id-card"></i> Nome Completo
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Digite o nome completo"
                    >
                </div>
                
                <!-- Usuário -->
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user"></i> Usuário
                    </label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Digite o nome de usuário"
                    >
                </div>
                
                <!-- Senha -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock"></i> Senha
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Digite a senha"
                    >
                </div>
                
                <!-- Cargo -->
                <div>
                    <label for="position" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-briefcase"></i> Cargo
                    </label>
                    <select 
                        name="position" 
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="">Selecione o cargo...</option>
                        <option value="0">Jogador (Usuário)</option>
                        <option value="1">Treinador</option>
                        <option value="2">Administrador</option>
                    </select>
                </div>
                
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-envelope"></i> E-mail <span class="text-gray-400 text-xs">(opcional)</span>
                    </label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Digite o e-mail"
                    >
                </div>
                
                <!-- Botões -->
                <div class="flex gap-4">
                    <button 
                        type="submit" 
                        name="register"
                        class="flex-1 bg-blue-500 text-white py-3 px-4 rounded-lg font-semibold hover:bg-blue-600 transition duration-200 flex items-center justify-center gap-2"
                    >
                        <i class="fas fa-check"></i> Registrar Usuário
                    </button>
                    <a 
                        href="MenuPrincipal.php"
                        class="flex-1 bg-gray-500 text-white py-3 px-4 rounded-lg font-semibold hover:bg-gray-600 transition duration-200 flex items-center justify-center gap-2 text-center"
                    >
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </form>
            
            <!-- Mensagens de sucesso/erro -->
            <div class="mt-6">
                <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (isset($_POST['register'])) {
                    $username = clearDataReceived($_POST['username']);
                    $password = hash("sha256", clearDataReceived($_POST['password']));
                    $cargo    = clearDataReceived($_POST['position']);
                    $name     = clearDataReceived($_POST['name']);
                    $email    = clearDataReceived($_POST['email']);

                    $query = "INSERT INTO tbl_usuarios (username, password, cargo, name, email) VALUES (?, ?, ?, ?, ?)";
                    $stmt = mysqli_stmt_init($connection);
                    if (!(mysqli_stmt_prepare($stmt, $query))) {
                        echo "Erro na preparação da declaração: " . mysqli_stmt_error($stmt);
                    }
                    mysqli_stmt_bind_param($stmt, "ssiss", $username, $password, $cargo, $name, $email);
                    if (mysqli_stmt_execute($stmt)) {
                        echo "<div class='p-4 bg-green-100 border border-green-300 text-green-700 rounded flex items-center gap-2'>";
                        echo "<i class='fas fa-check-circle'></i>";
                        echo "<span><strong>Sucesso!</strong> Usuário registrado com sucesso!</span>";
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
        
        <!-- Footer -->
        <div class="text-center mt-4 text-sm text-gray-600">
            <p>&copy; 2025 Gerenciador de Campeonato. Todos os direitos reservados.</p>
        </div>
    </div>
</body>
</html>