<h2 class="mb-4">Dashboard</h2>

<!-- MÉTRICAS PRINCIPALES -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card text-bg-primary h-100">
            <div class="card-body">
                <h6 class="card-title">Facturación total</h6>
                <h3><?= number_format($totals['total'] ?? 0, 2) ?> €</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card text-bg-success h-100">
            <div class="card-body">
                <h6 class="card-title">Cobrado</h6>
                <h3><?= number_format($totals['paid'] ?? 0, 2) ?> €</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card text-bg-warning h-100">
            <div class="card-body">
                <h6 class="card-title">Pendiente</h6>
                <h3><?= number_format($totals['pending'] ?? 0, 2) ?> €</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card text-bg-dark h-100">
            <div class="card-body">
                <h6 class="card-title">Clientes activos</h6>
                <h3><?= (int)($clients ?? 0) ?></h3>
            </div>
        </div>
    </div>
</div>

<!-- GRÁFICO -->
<div class="card">
    <div class="card-header">
        Facturación mensual
    </div>
    <div class="card-body">
        <canvas id="billingChart" height="120"></canvas>
    </div>
</div>

<!-- CHART.JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const ctx = document.getElementById('billingChart').getContext('2d');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($months ?? []) ?>,
        datasets: [{
            label: 'Facturación (€)',
            data: <?= json_encode($amounts ?? []) ?>,
            backgroundColor: 'rgba(13, 110, 253, 0.6)',
            borderColor: 'rgba(13, 110, 253, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>