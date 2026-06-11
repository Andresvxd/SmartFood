async function cargarReportes() {

  try {

    const response = await fetch(
      'backend/obtener_pedidos.php'
    );

    const pedidos = await response.json();

    renderStats(pedidos);

    renderVentasChart(pedidos);

    renderMetodosChart(pedidos);

  } catch(error) {

    console.error(error);
  }
}

function formatPrice(value) {

  return new Intl.NumberFormat('es-CO', {
    style: 'currency',
    currency: 'COP'
  }).format(value);
}

function renderStats(pedidos) {

  const totalVentas = pedidos.reduce(
    (acc, p) => acc + Number(p.total),
    0
  );

  document.getElementById('ventasTotales')
    .innerText = formatPrice(totalVentas);

  document.getElementById('totalPedidos')
    .innerText = pedidos.length;

  const promedio =
    pedidos.length
    ? totalVentas / pedidos.length
    : 0;

  document.getElementById('ticketPromedio')
    .innerText = formatPrice(promedio);
}

function renderVentasChart(pedidos) {

  const ventas = pedidos.map(p => Number(p.total));

  const labels = pedidos.map(p => `#${p.id}`);

  new Chart(
    document.getElementById('ventasChart'),
    {
      type: 'bar',

      data: {

        labels: labels,

        datasets: [{

          label: 'Ventas',

          data: ventas,

          backgroundColor: '#ff6b35',

          borderRadius: 8

        }]
      },

      options: {

        responsive: true,

        plugins: {

          legend: {

            labels: {

              color: '#fff'
            }
          }
        },

        scales: {

          x: {

            ticks: {

              color: '#fff'
            }
          },

          y: {

            ticks: {

              color: '#fff'
            }
          }
        }
      }
    }
  );
}

function renderMetodosChart(pedidos) {

  const conteo = {};

  pedidos.forEach(p => {

    conteo[p.metodo_pago] =
      (conteo[p.metodo_pago] || 0) + 1;
  });

  new Chart(
    document.getElementById('metodosChart'),
    {
      type: 'doughnut',

      data: {

        labels: Object.keys(conteo),

        datasets: [{

          data: Object.values(conteo),

          backgroundColor: [
            '#ff6b35',
            '#00c2ff',
            '#2ed573',
            '#ffcc00'
          ]
        }]
      },

      options: {

        responsive: true,

        plugins: {

          legend: {

            labels: {

              color: '#fff'
            }
          }
        }
      }
    }
  );
}

cargarReportes();