<?php
// topo.php: cabeçalho comum para páginas de Home
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$title = $title ?? 'Sistema de Adequação Curricular';
$brand = $brand ?? 'ETC';
$navbarClass = $navbarClass ?? 'navbar-dark bg-dark';
$logoutLabel = $logoutLabel ?? 'Sair';
$logoutIcon = $logoutIcon ?? '&#x1F6AA;';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="ASSETS/CSS/style.css">
    <link rel="stylesheet" href="ASSETS/CSS/topo_rodape.css">
    <?php if (!empty($extraHead)) echo $extraHead; ?>
</head>
<body class="d-flex flex-column min-vh-100">
<nav class="navbar <?php echo $navbarClass; ?> mb-2">
    <div class="container d-flex justify-content-between align-items-center">
        <span class="navbar-brand"><?php echo htmlspecialchars($brand, ENT_QUOTES, 'UTF-8'); ?></span>
        <div class="d-flex gap-2">
            <a href="index.php?action=dashboard" class="btn btn-outline-light btn-sm" title="Home">🏠</a>
            <a href="index.php?action=logout" class="btn btn-outline-light btn-sm" title="Sair">
                <span style="font-size: 0.9rem;"><?php echo $logoutIcon; ?></span>
            </a>
        </div>
    </div>
</nav>
<div class="container flex-grow-1">
<?php if (!empty($_SESSION['flash_success'])): ?>
    <div class="alert alert-success">
        <?php echo htmlspecialchars($_SESSION['flash_success'], ENT_QUOTES, 'UTF-8'); ?>
    </div>
    <?php unset($_SESSION['flash_success']); ?>
<?php endif; ?>
