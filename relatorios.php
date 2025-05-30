<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$username = $_SESSION['username'];
$role = $_SESSION['role'] ?? 'Utilizador';

?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Relatórios - Sistema de Inventário</title>
    <link rel="stylesheet" href="styles/dashboard.css"/>
    <link rel="stylesheet" href="styles/relatorios.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body>
    <div class="dashboard-container">
        <?php include 'header.php'; ?>

        <main class="main-content">
            <h1><i class="fas fa-file-export"></i> Gerar Relatório</h1>
            
            <form class="report-form" method="POST" action="generate-report.php">
                <div class="form-group">
                    <label for="report-type">Tipo de Relatório:</label>
                    <select id="report-type" name="report-type" required>
                        <option value="stock">Stock</option>
                        <option value="orders">Encomendas</option>
                        <?php if ($role === 'Administrador'): ?>
                            <option value="users">Utilizadores</option>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- CORREÇÃO: Datas não devem ser obrigatórias para todos os relatórios -->
                <div id="date-fields">
                    <div class="form-group">
                        <label for="date-start">Data Início:</label>
                        <input type="date" id="date-start" name="date-start">
                    </div>
                    <div class="form-group">
                        <label for="date-end">Data Fim:</label>
                        <input type="date" id="date-end" name="date-end">
                    </div>
                </div>

                <div class="form-group">
                    <label>Formato:</label>
                    <div class="radio-group">
                        <label>
                            <input type="radio" name="format" value="csv" checked> CSV
                        </label>
                        <label>
                            <input type="radio" name="format" value="pdf"> PDF
                        </label>
                    </div>
                </div>

                <button type="submit" class="submit-btn">
                    <i class="fas fa-download"></i> Gerar Relatório
                </button>
            </form>
        </main>
    </div>
</body>
</html>
