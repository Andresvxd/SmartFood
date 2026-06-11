<!DOCTYPE html>
<html lang="es">
<head>

  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

  <title>SmartFood – Pedidos</title>

  <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"/>

  <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"/>

  <link rel="stylesheet"
        href="../css/styles.css"/>

</head>

<body class="dashboard-body">

<?php
session_start();

if(!isset($_SESSION['rol'])){

    header("Location: ../index.html");
    exit();
}
?>

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

    <a href="productos.php" class="sf-nav-link">
      <i class="bi bi-grid"></i>
      Productos
    </a>

    <div class="nav-section-title">
      Gestión
    </div>

    <a href="pedidos.php"
       class="sf-nav-link active">
      <i class="bi bi-bag-check"></i>
      Pedidos
    </a>

    <a href="clientes.php"
       class="sf-nav-link">
      <i class="bi bi-people"></i>
      Clientes
    </a>

    <a href="reportes.php"
       class="sf-nav-link">
      <i class="bi bi-bar-chart-line"></i>
      Reportes
    </a>

    <a href="../backend/logout.php"
       class="sf-nav-link">
      <i class="bi bi-box-arrow-right"></i>
      Cerrar sesión
    </a>

  </nav>

</aside>

<!-- MAIN -->
<main class="sf-main">

  <!-- TOPBAR -->
  <div class="sf-topbar">

    <div>

      <span class="topbar-title">
        Gestión de Pedidos
      </span>

    </div>

    <div class="d-flex gap-2">

      <button class="btn-sf-sm"
              onclick="cargarPedidos()">

        <i class="bi bi-arrow-clockwise"></i>
        Actualizar

      </button>

    </div>

  </div>

  <!-- CONTENT -->
  <div class="sf-content">

    <!-- STATS -->
    <div class="row g-3 mb-4">

      <div class="col-md-4">

        <div class="stat-card">

          <div class="stat-icon">
            📦
          </div>

          <div class="stat-value"
               id="totalPedidos">

            0

          </div>

          <div class="stat-label">
            Pedidos Totales
          </div>

        </div>

      </div>

      <div class="col-md-4">

        <div class="stat-card">

          <div class="stat-icon">
            💰
          </div>

          <div class="stat-value"
               id="ventasTotales">

            $0

          </div>

          <div class="stat-label">
            Ventas Totales
          </div>

        </div>

      </div>

      <div class="col-md-4">

        <div class="stat-card">

          <div class="stat-icon">
            🚚
          </div>

          <div class="stat-value">

            Activo

          </div>

          <div class="stat-label">
            Estado del sistema
          </div>

        </div>

      </div>

    </div>

    <!-- TABLA -->
    <div class="sf-card p-4">

      <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">

        <h3 class="text-white m-0">
          📦 Pedidos registrados
        </h3>

        <input type="text"
               class="form-control"
               id="buscarPedido"
               placeholder="Buscar pedido..."
               oninput="filtrarPedidos()"
               style="max-width:260px;">

      </div>

      <div class="table-responsive">

        <table class="table align-middle sf-table">

          <thead>

            <tr>

              <th>ID</th>
              <th>Cliente</th>
              <th>Total</th>
              <th>Método</th>
              <th>Estado</th>
              <th>Fecha</th>

            </tr>

          </thead>

          <tbody id="tablaPedidos">

            <tr>
              <td colspan="6" class="text-center text-light py-5">
                Cargando pedidos...
              </td>
            </tr>

          </tbody>

        </table>

      </div>

    </div>

  </div>

</main>

<div id="sfToast"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>

let pedidosGlobal = [];

function formatPrice(value) {

  return new Intl.NumberFormat('es-CO', {

    style: 'currency',
    currency: 'COP'

  }).format(value);
}

function getEstadoBadge(total) {

  if(Number(total) >= 50000){

    return `
      <span class="badge bg-success">
        Completado
      </span>
    `;
  }

  return `
    <span class="badge bg-warning text-dark">
      Pendiente
    </span>
  `;
}

function renderPedidos(lista) {

  const tabla =
    document.getElementById('tablaPedidos');

  if (!lista || lista.length === 0) {

    tabla.innerHTML = `

      <tr>

        <td colspan="6"
            class="text-center text-light py-5">

          No hay pedidos registrados

        </td>

      </tr>

    `;

    return;
  }

  tabla.innerHTML = lista.map(p => `

    <tr>

      <td class="text-light fw-bold">
        #${p.id}
      </td>

      <td class="text-light">
        Cliente ${p.cliente_id ?? 'General'}
      </td>

      <td class="text-warning fw-bold">
        ${formatPrice(p.total)}
      </td>

      <td>

        <span class="badge bg-info">

          ${p.metodo_pago}

        </span>

      </td>

      <td>

        ${getEstadoBadge(p.total)}

      </td>

      <td class="text-light">

        ${p.fecha}

      </td>

    </tr>

  `).join('');
}

function actualizarStats() {

  document.getElementById('totalPedidos')
    .innerText = pedidosGlobal.length;

  const totalVentas = pedidosGlobal.reduce(

    (acc, p) => acc + Number(p.total),

    0
  );

  document.getElementById('ventasTotales')
    .innerText = formatPrice(totalVentas);
}

function filtrarPedidos() {

  const q = document.getElementById('buscarPedido')
    .value
    .toLowerCase();

  const filtrados = pedidosGlobal.filter(p =>

    String(p.id).toLowerCase().includes(q) ||

    String(p.cliente_id ?? '')
      .toLowerCase()
      .includes(q) ||

    String(p.metodo_pago)
      .toLowerCase()
      .includes(q)

  );

  renderPedidos(filtrados);
}

async function cargarPedidos() {

  try {

    const response = await fetch(
      '../backend/obtener_pedidos.php'
    );

    if(!response.ok){

      throw new Error(
        'Error HTTP: ' + response.status
      );
    }

    const data = await response.json();

    pedidosGlobal = data;

    renderPedidos(pedidosGlobal);

    actualizarStats();

  } catch(error) {

    console.error(error);

    document.getElementById(
      'tablaPedidos'
    ).innerHTML = `

      <tr>

        <td colspan="6"
            class="text-center text-danger py-5">

          Error cargando pedidos

        </td>

      </tr>

    `;
  }
}

cargarPedidos();

</script>

</body>
</html>


