<?php
session_start();

if (!isset($_SESSION['rol'])) {
    header("Location: ../index.html");
    exit();
}

if ($_SESSION['rol'] != 'admin') {
    header("Location: ../index.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>SmartFood – Clientes</title>

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="../css/styles.css">

</head>

<body class="dashboard-body">

<!-- SIDEBAR -->
<aside class="sf-sidebar">

    <div class="sidebar-logo">
        <div class="logo-icon">🍽️</div>
        <span>SmartFood</span>
    </div>

    <nav class="sidebar-nav">

        <div class="nav-section-title">
            Principal
        </div>

        <a href="dashboard.php" class="sf-nav-link">
            <i class="bi bi-speedometer2"></i>
            Dashboard
        </a>

        <a href="../cliente/menu.html" class="sf-nav-link">
            <i class="bi bi-grid"></i>
            Menú & Pedidos
        </a>

        <div class="nav-section-title">
            Gestión
        </div>

        <a href="pedidos.php" class="sf-nav-link">
            <i class="bi bi-bag-check"></i>
            Pedidos
        </a>

        <a href="clientes.php" class="sf-nav-link active">
            <i class="bi bi-people"></i>
            Clientes
        </a>

        <a href="reportes.php" class="sf-nav-link">
            <i class="bi bi-bar-chart-line"></i>
            Reportes
        </a>

        <a href="../backend/logout.php" class="sf-nav-link">
            <i class="bi bi-box-arrow-right"></i>
            Cerrar sesión
        </a>

    </nav>

</aside>

<!-- MAIN -->
<main class="sf-main">

    <div class="sf-topbar">
        <span class="topbar-title">
            Gestión de Clientes
        </span>
    </div>

    <div class="sf-content">

        <!-- STATS -->
        <div class="row g-3 mb-4">

            <div class="col-md-4">
                <div class="stat-card">

                    <div class="stat-icon">
                        👥
                    </div>

                    <div class="stat-value" id="totalClientes">
                        0
                    </div>

                    <div class="stat-label">
                        Clientes registrados
                    </div>

                </div>
            </div>

            <div class="col-md-4">
                <div class="stat-card">

                    <div class="stat-icon">
                        ⭐
                    </div>

                    <div class="stat-value">
                        Premium
                    </div>

                    <div class="stat-label">
                        Experiencia SmartFood
                    </div>

                </div>
            </div>

            <div class="col-md-4">
                <div class="stat-card">

                    <div class="stat-icon">
                        🚀
                    </div>

                    <div class="stat-value">
                        Activo
                    </div>

                    <div class="stat-label">
                        Sistema funcionando
                    </div>

                </div>
            </div>

        </div>

        <!-- TABLA -->
        <div class="sf-card p-4">

            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">

                <h3 class="text-white m-0">
                    👥 Clientes registrados
                </h3>

                <input
                    type="text"
                    class="sf-search"
                    id="buscarCliente"
                    placeholder="Buscar cliente..."
                    oninput="filtrarClientes()"
                    style="max-width:250px;"
                >

            </div>

            <div class="table-responsive">

                <table class="table align-middle sf-table">

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                        </tr>
                    </thead>

                    <tbody id="tablaClientes"></tbody>

                </table>

            </div>

        </div>

    </div>

</main>

<script>

let clientesGlobal = [];

function renderClientes(lista) {

    const tabla = document.getElementById('tablaClientes');

    if (!lista.length) {

        tabla.innerHTML = `
            <tr>
                <td colspan="4" class="text-center text-light py-5">
                    No hay clientes registrados
                </td>
            </tr>
        `;

        return;
    }

    tabla.innerHTML = lista.map(c => `
        <tr>
            <td class="text-light fw-bold">#${c.id}</td>
            <td class="text-light">${c.nombre}</td>
            <td class="text-info">${c.email}</td>
            <td class="text-light">${c.telefono}</td>
        </tr>
    `).join('');
}

function filtrarClientes() {

    const q = document.getElementById('buscarCliente')
        .value
        .toLowerCase();

    const filtrados = clientesGlobal.filter(c =>
        c.nombre.toLowerCase().includes(q) ||
        c.email.toLowerCase().includes(q) ||
        c.telefono.toLowerCase().includes(q)
    );

    renderClientes(filtrados);
}

async function cargarClientes() {

    try {

        const response = await fetch('../backend/obtener_clientes.php');

        clientesGlobal = await response.json();

        renderClientes(clientesGlobal);

        document.getElementById('totalClientes').innerText =
            clientesGlobal.length;

    } catch (error) {

        console.error('Error cargando clientes:', error);

        document.getElementById('tablaClientes').innerHTML = `
            <tr>
                <td colspan="4" class="text-center text-danger py-5">
                    Error al cargar clientes
                </td>
            </tr>
        `;
    }
}

cargarClientes();

</script>

</body>
</html>