<h2 class="mb-3">Facturas</h2>

<table class="table table-striped table-bordered align-middle">
    <thead class="table-dark">
        <tr>
            <th>Nº</th>
            <th>Cliente</th>
            <th>Total</th>
            <th>Estado</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($invoices as $i): ?>
        <tr>
            <td><?= htmlspecialchars($i['invoice_number']) ?></td>
            <td><?= htmlspecialchars($i['client_name']) ?></td>
            <td><?= number_format($i['total'], 2) ?> €</td>
            <td>
                <?php if ($i['status'] === 'paid'): ?>
                    <span class="badge bg-success">Pagada</span>
                <?php elseif ($i['status'] === 'pending'): ?>
                    <span class="badge bg-warning text-dark">Pendiente</span>
                <?php else: ?>
                    <span class="badge bg-danger">Cancelada</span>
                <?php endif; ?>
            </td>
            <td class="text-end">
                <a class="btn btn-sm btn-primary"
                   href="index.php?action=invoices_show&id=<?= $i['id'] ?>">
                   Ver
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>