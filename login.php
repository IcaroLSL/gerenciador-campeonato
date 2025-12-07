<?php
    require_once("conecta.php");
    require_once("function.php");
    
    $mensagem = "";
    
    if (isset($_POST['logar']) && $_SERVER["REQUEST_METHOD"] == "POST") {
        $username = clearDataReceived($_POST['username']);
        $password = hash("sha256", clearDataReceived($_POST['password']));
        
        $query = "SELECT id_usuario, cargo, name FROM tbl_usuarios WHERE username = ? AND password = ?";
        $stmt = mysqli_stmt_init($connection);
        if (!(mysqli_stmt_prepare($stmt, $query))) {
            $mensagem = "Erro na preparação da declaração: " . mysqli_stmt_error($stmt);
        } else {
            mysqli_stmt_bind_param($stmt, "ss", $username, $password);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            if (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $cookieData = array(
                    "id" => $row['id_usuario'],
                    "cargo" => $row['cargo'],
                    "name" => $row['name'],
                    "username" => $username
                );
                setcookie("credentials", json_encode($cookieData), time() + (86400 * 30), "/");
                header("Location: MenuPrincipal.php");
                exit;
            } else {
                $mensagem = "<p>Usuário ou senha incorretos. Tente novamente.</p>";
            }
            mysqli_stmt_close($stmt);
        }
    }
    
    if (isset($_COOKIE['credentials']) && $_COOKIE['credentials'] != false) {
        header("Location: MenuPrincipal.php");
        exit;
    }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Gerenciador de Campeonato</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Barra de navegação simples -->
    <nav class="p-4 bg-gray-200 shadow-md">
        <div class="max-w-7xl mx-auto">
            <div class="text-xl font-bold text-gray-800">
                <i class="fas fa-trophy"></i> Fatec Campeonatos
            </div>
        </div>
    </nav>
    
    <div class="flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md">
        <!-- Card principal -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            <!-- Cabeçalho -->
            <div class="text-center mb-8">
                <i class="fas fa-trophy text-blue-500 text-5xl mb-4"></i>
                <h1 class="text-3xl font-bold text-gray-800">Gerenciador de Campeonato</h1>
                <p class="text-gray-600 mt-2">Faça login para continuar</p>
            </div>
            
            <!-- Mensagem de erro/sucesso -->
            <?php if (!empty($mensagem)): ?>
                <div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-700 rounded">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $mensagem; ?>
                </div>
            <?php endif; ?>
            
            <!-- Formulário -->
            <form action="" method="post" class="space-y-6">
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
                        placeholder="Digite seu usuário"
                    >
                </div>
                
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
                        placeholder="Digite sua senha"
                    >
                </div>
                
                <button 
                    type="submit" 
                    name="logar"
                    class="w-full bg-blue-500 text-white py-3 px-4 rounded-lg font-semibold hover:bg-blue-600 transition duration-200 flex items-center justify-center gap-2"
                >
                    <i class="fas fa-sign-in-alt"></i> Entrar
                </button>
            </form>
            
            <!-- Link para registro (se houver página de registro pública) -->
            <div class="mt-6 text-center text-sm text-gray-600">
                <p>Não possui uma conta? Entre em contato com o administrador.</p>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="text-center mt-4 text-sm text-gray-600">
            <p>&copy; 2025 Gerenciador de Campeonato. Todos os direitos reservados.</p>
        </div>
    </div>
    </div>
</body>
</html>