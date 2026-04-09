<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Facturación</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">


    <!-- Bootstrap CSS -->
    https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="styleshee

</head>
<body class="bg-light">
<h1>Sistema de Facturación</h1>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="index.php?action=dashboard">Facturación</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="index.php?action=clients">Clientes</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php?action=products">Productos</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php?action=budgets">Presupuestos</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php?action=invoices">Facturas</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php?action=company">Empresa</a></li>

                <?php if (auth_user()['role'] === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link text-warning" href="index.php?action=admin_users">Admin</a>
                    </li>
                <?php endif; ?>
            </ul>

            <span class="navbar-text me-3">
                <?= htmlspecialchars(auth_user()['name']) ?>
            </span>

            <a class="btn btn-outline-light btn-sm" href="index.php?action=logout">Salir</a>
        </div>
    </div>
</nav>

<div class="container">


<hr>