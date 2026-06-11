<?php
session_start();

if(!isset($_SESSION['rol'])){

    header("Location: ../index.html");
    exit();
}

if($_SESSION['rol'] != 'admin'){

    header("Location: ../index.html");
    exit();
}


?>

<!DOCTYPE html>
<html lang="es">
<head>

  <meta charset="UTF-8"/>
  <meta name="viewport"
        content="width=device-width, initial-scale=1.0"/>

  <title>SmartFood – Dashboard</title>

  <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"/>

  <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"/>

  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;700;800&family=DM+Sans:wght@300;400;500&display=swap"
        rel="stylesheet"/>

  <link rel="stylesheet"
        href="../css/styles.css"/>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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

      <a href="dashboard.php"
         class="sf-nav-link active">

        <i class="bi bi-speedometer2"></i>
        Dashboard

      </a>

      <a href="productos.php"
         class="sf-nav-link">

        <i class="bi bi-grid"></i>
        Productos

      </a>

      <div class="nav-section-title">

        Gestión

      </div>

      <a href="pedidos.php"
         class="sf-nav-link">

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

      <div class="d-flex align-items-center gap-3">

        <span class="topbar-title">

          Dashboard

        </span>

      </div>

    </div>

    <!-- CONTENT -->
    <div class="sf-content">

      <!-- SALUDO -->
      <div class="mb-4">

        <h2 id="greetingTitle"
            style="
              font-family:'Syne',sans-serif;
              font-weight:800;
              color:#fff;
            ">

          Bienvenido Admin 👋

        </h2>

        <p style="
            color:rgba(255,255,255,0.45);
            font-size:0.9rem;
        ">

          Aquí tienes el resumen general de SmartFood.

        </p>

      </div>

      <!-- STATS -->
      <div class="row g-3 mb-4"
           id="statsRow"></div>

      <!-- GRAFICOS -->
      <div class="row g-3 mb-4">

        <!-- BAR -->
        <div class="col-lg-7">

          <div class="sf-card h-100">

            <div class="card-body">

              <h6 class="mb-3"
                  style="
                    color:rgba(255,255,255,0.6);
                    font-size:0.8rem;
                    text-transform:uppercase;
                    letter-spacing:1px;
                  ">

                Ventas de la semana

              </h6>

              <canvas id="ventasChart"
                      height="200"></canvas>

            </div>

          </div>

        </div>

        <!-- DONUT -->
        <div class="col-lg-5">

          <div class="sf-card h-100">

            <div class="card-body">

              <h6 class="mb-3"
                  style="
                    color:rgba(255,255,255,0.6);
                    font-size:0.8rem;
                    text-transform:uppercase;
                    letter-spacing:1px;
                  ">

                Distribución general

              </h6>

              <canvas id="estadosChart"
                      height="200"></canvas>

            </div>

          </div>

        </div>

      </div>

      <!-- PEDIDOS -->
      <div class="sf-card">

        <div class="card-body">

          <div class="d-flex align-items-center justify-content-between mb-3">

            <h6 style="
                color:#fff;
                font-weight:700;
            ">

              Pedidos recientes

            </h6>

            <a href="pedidos.php"
               class="btn-sf-sm">

              Ver todos

            </a>

          </div>

          <div class="table-responsive">

            <table class="table sf-table mb-0">

              <thead>

                <tr>

                  <th>#</th>
                  <th>Cliente</th>
                  <th>Total</th>
                  <th>Método</th>
                  <th>Fecha</th>

                </tr>

              </thead>

              <tbody id="recentOrdersTable"></tbody>

            </table>

          </div>

        </div>

      </div>

    </div>

  </main>

  <div id="sfToast"></div>

  <!-- SCRIPTS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script src="../js/cart.js"></script>
  <script src="../js/chatbot.js"></script>

  <script>

    window.history.forward();

    function formatPrice(value) {

      return new Intl.NumberFormat('es-CO', {

        style: 'currency',
        currency: 'COP'

      }).format(value);
    }

    async function renderDashboard() {

      try {

        const response = await fetch(
          '../backend/dashboard_stats.php'
        );

        const data = await response.json();

        const h = new Date().getHours();

        const saludo =
          h < 12
          ? 'Buenos días'
          : h < 18
          ? 'Buenas tardes'
          : 'Buenas noches';

        document.getElementById(
          'greetingTitle'
        ).textContent = `${saludo} Admin 👋`;

        const stats = [

          {
            label: 'Ventas',
            value: formatPrice(data.ingresos),
            icon: '💰',
            color: 'rgba(46,213,115,0.15)'
          },

          {
            label: 'Pedidos',
            value: data.pedidos,
            icon: '📦',
            color: 'rgba(255,107,53,0.15)'
          },

          {
            label: 'Productos',
            value: data.productos,
            icon: '🍔',
            color: 'rgba(255,171,0,0.15)'
          },

          {
            label: 'Clientes',
            value: 1,
            icon: '👥',
            color: 'rgba(83,172,255,0.15)'
          }

        ];

        document.getElementById(
          'statsRow'
        ).innerHTML = stats.map(s => `

          <div class="col-6 col-lg-3">

            <div class="stat-card">

              <div class="d-flex align-items-center justify-content-between mb-2">

                <div class="stat-icon"
                     style="background:${s.color}">

                  ${s.icon}

                </div>

              </div>

              <div class="stat-value">

                ${s.value}

              </div>

              <div class="stat-label">

                ${s.label}

              </div>

            </div>

          </div>

        `).join('');

        new Chart(
          document.getElementById('ventasChart'),
          {

            type: 'bar',

            data: {

              labels: [
                'Lun',
                'Mar',
                'Mié',
                'Jue',
                'Vie',
                'Sáb',
                'Dom'
              ],

              datasets: [{

                label: 'Ventas',

                data: [
                  48000,
                  72000,
                  65000,
                  89000,
                  95000,
                  120000,
                  78000
                ],

                backgroundColor:
                  'rgba(255,107,53,0.7)',

                borderColor:
                  '#ff6b35',

                borderWidth: 2,

                borderRadius: 8

              }]
            },

            options: {

              responsive: true
            }
          }
        );

        new Chart(
          document.getElementById('estadosChart'),
          {

            type: 'doughnut',

            data: {

              labels: [
                'Pedidos',
                'Productos',
                'Ingresos'
              ],

              datasets: [{

                data: [

                  data.pedidos,
                  data.productos,
                  data.ingresos / 1000

                ],

                backgroundColor: [

                  '#ffab00',
                  '#53acff',
                  '#2ed573'

                ],

                borderWidth: 0

              }]
            },

            options: {

              responsive: true
            }
          }
        );

        document.getElementById(
          'recentOrdersTable'
        ).innerHTML = data.recientes.map(p => `

          <tr>

            <td>#${p.id}</td>

            <td>Cliente General</td>

            <td style="
              color:#ff6b35;
              font-weight:600;
            ">

              ${formatPrice(p.total)}

            </td>

            <td>

              <span class="badge-sf-success">

                ${p.metodo_pago}

              </span>

            </td>

            <td style="
              color:rgba(255,255,255,0.4);
              font-size:0.82rem;
            ">

              ${p.fecha}

            </td>

          </tr>

        `).join('');

      } catch(error) {

        console.error(error);
      }
    }

    renderDashboard();

  </script>

</body>
</html>
