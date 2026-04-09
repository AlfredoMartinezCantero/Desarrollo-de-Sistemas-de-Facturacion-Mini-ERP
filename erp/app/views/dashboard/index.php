<h2 class="mb-4">Dashboard</h2>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-bg-primary">
            <div class="card-body">
                <h6>Facturación</h6>
                <h4><?= number_format($totals['total'], 2) ?> €</h4>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-bg-success">
            <div class="card-body">
                <h6>Cobrado</h6>
                <h4><?= number_format($totals['paid'], 2) ?> €</h4>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-bg-warning">
            <div class="card-body">
                <h6>Pendiente</h6>
                <h4><?= number_format($totals['pending'], 2) ?> €</h4>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-bg-dark">
            <div class="card-body">
                <h6>Clientes</h6>
                <h4><?= $clients ?></h4>
            </div>
        </div>
    </div>
</div>
