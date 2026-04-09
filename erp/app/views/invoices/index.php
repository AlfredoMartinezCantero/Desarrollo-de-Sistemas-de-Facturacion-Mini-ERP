<h2 class="mb-4">Facturas</h2>

<?php if (empty($invoices)): ?>
    <div class="alert alert-info">
        Todavía no tienes facturas emitidas.
    </div>
<?php else: ?>
<table class="table table-striped table-bordered align-middle">
    <thead class="table-dark">
        <tr>
            <th>Nº</th>
            <th>Cliente</th>
            <th>Total</th>
            <th>Estado</th>
            <th class="text-end">Acciones</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($invoices as $invoice): ?>
        <tr>
            <td><?= htmlspecialchars($invoice['invoice_number']) ?></td>
            <td><?= htmlspecialchars($invoice['client_name']) ?></td>
            <td><?= number_format($invoice['total'], 2) ?> €</td>
            <td>
                <?php if ($invoice['status'] === 'paid'): ?>
                    <span class="badge bg-success">Pagada</span>
                <?php elseif ($invoice['status'] === 'pending'): ?>
                    <span class="badge bg-warning text-dark">Pendiente</span>
                <?php else: ?>
                    <span class="badge bg-danger">Cancelada</span>
                <?php endif; ?>
            </td>
            <td class="text-end">
                <a href="index.php?action=invoices_show&id=<?= $invoice['id'] ?>" class="btn btn-sm btn-primary">
                    👁 Ver
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>