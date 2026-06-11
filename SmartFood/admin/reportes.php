<?php
session_start();

if(!isset($_SESSION['rol'])){

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

  <title>SmartFood – Reportes</title>

  <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"/>

  <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"/>

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
       class="sf-nav-link">

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
       class="sf-nav-link active">

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

  <div class="sf-topbar">

    <span class="topbar-title">
      Reportes & Estadísticas
    </span>

  </div>

  <div class="sf-content">

    <!-- STATS -->
    <div class="row g-3 mb-4">

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
            📦
          </div>

          <div class="stat-value"
               id="totalPedidos">

            0

          </div>

          <div class="stat-label">
            Total Pedidos
          </div>

        </div>

      </div>

      <div class="col-md-4">

        <div class="stat-card">

          <div class="stat-icon">
            📈
          </div>

          <div class="stat-value"
               id="ticketPromedio">

            $0

          </div>

          <div class="stat-label">
            Ticket Promedio
          </div>

        </div>

      </div>

    </div>

    <!-- CHARTS -->
    <div class="row g-4">

      <div class="col-lg-7">

        <div class="sf-card p-4">

          <h4 class="text-white mb-4">
            📊 Ventas por Pedido
          </h4>

          <canvas id="ventasChart"></canvas>

        </div>

      </div>

      <div class="col-lg-5">

        <div class="sf-card p-4">

          <h4 class="text-white mb-4">
            💳 Métodos de Pago
          </h4>

          <canvas id="metodosChart"></canvas>

        </div>

      </div>

    </div>

  </div>

</main>

<script>

function formatPrice(value){

  return new Intl.NumberFormat('es-CO', {

    style: 'currency',
    currency: 'COP'

  }).format(value);
}

async function cargarReportes(){

  try{

    const response = await fetch(
      '../backend/obtener_pedidos.php'
    );

    if(!response.ok){

      throw new Error(
        'Error cargando pedidos'
      );
    }

    const pedidos = await response.json();

    // STATS
    const totalPedidos = pedidos.length;

    const ventasTotales = pedidos.reduce(

      (acc, p) => acc + Number(p.total),

      0
    );

    const ticketPromedio = totalPedidos > 0

      ? ventasTotales / totalPedidos
      : 0;

    document.getElementById(
      'ventasTotales'
    ).innerText = formatPrice(
      ventasTotales
    );

    document.getElementById(
      'totalPedidos'
    ).innerText = totalPedidos;

    document.getElementById(
      'ticketPromedio'
    ).innerText = formatPrice(
      ticketPromedio
    );

    // CHART VENTAS
    const labelsVentas = pedidos.map(
      p => '#' + p.id
    );

    const dataVentas = pedidos.map(
      p => Number(p.total)
    );

    new Chart(

      document.getElementById(
        'ventasChart'
      ),

      {

        type: 'bar',

        data: {

          labels: labelsVentas,

          datasets: [{

            label: 'Ventas',

            data: dataVentas,

            backgroundColor:
              'rgba(255,107,53,0.7)',

            borderColor:
              '#ff6b35',

            borderWidth: 2,

            borderRadius: 8

          }]
        },

        options: {

          responsive: true,

          plugins: {

            legend: {

              labels: {

                color: '#ffffff'
              }
            }
          },

          scales: {

            x: {

              ticks: {

                color: '#ffffff'
              },

              grid: {

                color:
                  'rgba(255,255,255,0.05)'
              }
            },

            y: {

              ticks: {

                color: '#ffffff'
              },

              grid: {

                color:
                  'rgba(255,255,255,0.05)'
              }
            }
          }
        }
      }
    );

    // MÉTODOS DE PAGO
    const metodos = {};

    pedidos.forEach(p => {

      const metodo = p.metodo_pago;

      if(!metodos[metodo]){

        metodos[metodo] = 0;
      }

      metodos[metodo]++;
    });

    new Chart(

      document.getElementById(
        'metodosChart'
      ),

      {

        type: 'doughnut',

        data: {

          labels: Object.keys(metodos),

          datasets: [{

            data: Object.values(metodos),

            backgroundColor: [

              '#ff6b35',
              '#2ed573',
              '#53acff',
              '#ffab00'

            ],

            borderWidth: 0
          }]
        },

        options: {

          responsive: true,

          plugins: {

            legend: {

              position: 'bottom',

              labels: {

                color: '#ffffff'
              }
            }
          }
        }
      }
    );

  }catch(error){

    console.error(error);

    alert(
      'Error cargando reportes'
    );
  }
}

cargarReportes();

</script>

</body>
</html>